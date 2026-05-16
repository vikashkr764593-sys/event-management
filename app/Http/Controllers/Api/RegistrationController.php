<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\PendingUser;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class RegistrationController extends Controller
{
    /**
     * Pre-register the user and generate a Razorpay order.
     */
    public function preRegister(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email|unique:pending_users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
        ]);

        try {
            // Get Razorpay keys
            $keyId = env('RAZORPAY_KEY', config('services.razorpay.key'));
            $keySecret = env('RAZORPAY_SECRET', config('services.razorpay.secret'));

            if (!$keyId || !$keySecret) {
                return response()->json(['error' => 'Razorpay keys not configured.'], 500);
            }

            $api = new Api($keyId, $keySecret);

            // Registration fee (e.g., 500 INR)
            $amount = 500 * 100; // Amount in paise

            $orderData = [
                'receipt'         => 'reg_' . uniqid(),
                'amount'          => $amount,
                'currency'        => 'INR',
                'payment_capture' => 1 // auto capture
            ];

            $razorpayOrder = $api->order->create($orderData);

            $pendingUser = PendingUser::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
                'razorpay_order_id' => $razorpayOrder['id'],
                'status' => 'pending'
            ]);

            return response()->json([
                'message' => 'Pre-registration successful. Please complete the payment.',
                'order_id' => $razorpayOrder['id'],
                'amount' => $amount,
                'currency' => 'INR',
                'pending_user_id' => $pendingUser->id
            ]);
        } catch (\Exception $e) {
            Log::error('Registration Error: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred during registration. Please try again.'], 500);
        }
    }

    /**
     * Handle Razorpay webhook to finalize registration.
     * Also handles client-side payment verification.
     */

    public function webhook(Request $request)
    {
        Log::info('Registration Webhook/Callback received', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'payload' => $request->all(),
            'is_webhook' => !empty($request->header('X-Razorpay-Signature'))
        ]);

        DB::beginTransaction();

        try {

            /*
        |--------------------------------------------------------------------------
        | RAZORPAY CONFIG
        |--------------------------------------------------------------------------
        */

            $webhookSecret = env('RAZORPAY_WEBHOOK_SECRET');

            $keyId = env(
                'RAZORPAY_KEY',
                config('services.razorpay.key')
            );

            $keySecret = env(
                'RAZORPAY_SECRET',
                config('services.razorpay.secret')
            );

            $api = new Api($keyId, $keySecret);

            /*
        |--------------------------------------------------------------------------
        | DETERMINE REQUEST TYPE & VERIFY SIGNATURE
        |--------------------------------------------------------------------------
        */

            $webhookSignature = $request->header('X-Razorpay-Signature');
            $isWebhook = !empty($webhookSignature);
            $orderId = null;

            if ($isWebhook) {
                // 1. Handle actual Razorpay Webhook
                if ($webhookSecret) {
                    $api->utility->verifyWebhookSignature(
                        $request->getContent(),
                        $webhookSignature,
                        $webhookSecret
                    );
                }

                $payload = json_decode($request->getContent(), true);
                $orderId = $payload['payload']['payment']['entity']['order_id']
                    ?? $payload['payload']['order']['entity']['id']
                    ?? null;

                if (!$orderId) {
                    throw new \Exception('Order ID not found in webhook payload');
                }
            } else {
                // 2. Handle Frontend Callback
                $request->validate([
                    'razorpay_order_id' => 'required',
                ]);

                $orderId = $request->razorpay_order_id;

                // Optionally verify the frontend's payment signature if provided
                if ($request->has('razorpay_signature') && $request->has('razorpay_payment_id')) {
                    $api->utility->verifyPaymentSignature([
                        'razorpay_order_id'   => $orderId,
                        'razorpay_payment_id' => $request->razorpay_payment_id,
                        'razorpay_signature'  => $request->razorpay_signature
                    ]);
                }
            }

            /*
        |--------------------------------------------------------------------------
        | VERIFY PAYMENT USING ORDER ID
        |--------------------------------------------------------------------------
        */

            $razorpayOrder = $api->order->fetch($orderId);
            Log::info('Razorpay Order Fetched', ['order_id' => $orderId, 'status' => $razorpayOrder['status']]);

            $payment = null;

            // 1. If it's a frontend callback and we have payment_id, fetch it directly
            if ($request->has('razorpay_payment_id')) {
                try {
                    $payment = $api->payment->fetch($request->razorpay_payment_id);
                    Log::info('Fetched payment via payment_id from request', ['payment_id' => $request->razorpay_payment_id, 'status' => $payment['status']]);
                } catch (\Exception $e) {
                    Log::warning('Failed to fetch payment via payment_id', ['error' => $e->getMessage()]);
                }
            }

            // 2. If it's a webhook, check the payload for payment details
            if (!$payment && $isWebhook) {
                $payload = json_decode($request->getContent(), true);
                $paymentData = $payload['payload']['payment']['entity'] ?? null;
                if ($paymentData) {
                    $payment = $paymentData;
                    Log::info('Found payment in webhook payload', ['payment_id' => $payment['id'], 'status' => $payment['status']]);
                }
            }

            // 3. Fallback: Search all payments associated with the order
            if (!$payment || !in_array($payment['status'] ?? '', ['captured', 'authorized'])) {
                $paymentsResponse = $api->payment->all(['order_id' => $orderId]);
                $paymentList = $paymentsResponse['items'] ?? [];
                
                Log::info('Fallback: Searching all payments for order', [
                    'order_id' => $orderId,
                    'count' => count($paymentList),
                    'statuses' => collect($paymentList)->pluck('status')->toArray()
                ]);

                $payment = collect($paymentList)->first(function ($item) {
                    return in_array($item['status'] ?? '', ['captured', 'authorized']);
                });
            }

            // Final check: if order is 'paid', we can proceed even if we didn't find a specific payment object
            // (though we prefer having the payment object for the ID)
            $isPaid = ($payment && in_array($payment['status'] ?? '', ['captured', 'authorized'])) || $razorpayOrder['status'] === 'paid';

            if (!$isPaid) {
                Log::error('Payment verification failed', [
                    'order_id' => $orderId,
                    'order_status' => $razorpayOrder['status'],
                    'payment' => $payment
                ]);

                DB::rollBack();

                return response()->json([
                    'status' => false,
                    'message' => 'Payment not completed'
                ], 400);
            }

            /*
        |--------------------------------------------------------------------------
        | FIND PENDING USER
        |--------------------------------------------------------------------------
        */

            $pendingUser = PendingUser::where(
                'razorpay_order_id',
                $orderId
            )->first();

            if (!$pendingUser) {

                DB::rollBack();

                return response()->json([
                    'status' => false,
                    'message' => 'Pending user not found'
                ], 404);
            }

            /*
        |--------------------------------------------------------------------------
        | CHECK EXISTING USER
        |--------------------------------------------------------------------------
        */

            $existingUser = User::where(
                'email',
                $pendingUser->email
            )->first();

            if ($existingUser) {

                Auth::login($existingUser);

                $token = $existingUser
                    ->createToken('mobile_app')
                    ->plainTextToken;

                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'Login successful',
                    'token' => $token,
                    'user' => new UserResource($existingUser)
                ]);
            }

            /*
        |--------------------------------------------------------------------------
        | CREATE USER FROM PENDING USER
        |--------------------------------------------------------------------------
        */

            $newUser = User::create([
                'name' => $pendingUser->name,
                'email' => $pendingUser->email,
                'phone' => $pendingUser->phone,
                'password' => $pendingUser->password,
                'role' => 'user',
                'status' => 'active',

                'razorpay_order_id' => $orderId,
                'razorpay_payment_id' => isset($payment['id']) ? $payment['id'] : null,
                'payment_status' => isset($payment['status']) ? $payment['status'] : 'paid',
            ]);

            /*
        |--------------------------------------------------------------------------
        | UPDATE PENDING USER STATUS
        |--------------------------------------------------------------------------
        */

            $pendingUser->update([
                'status' => 'paid'
            ]);

            // Optional
            // $pendingUser->delete();

            DB::commit();

            /*
        |--------------------------------------------------------------------------
        | AUTO LOGIN
        |--------------------------------------------------------------------------
        */

            Auth::login($newUser);

            $token = $newUser
                ->createToken('mobile_app')
                ->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Payment successful & login successful',
                'token' => $token,
                'user' => new UserResource($newUser)
            ]);
        } catch (SignatureVerificationError $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Invalid webhook signature'
            ], 400);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
