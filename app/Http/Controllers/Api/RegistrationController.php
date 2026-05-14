<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PendingUser;
use App\Http\Resources\UserResource;
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
                'password' => $validated['password'], // Hash cast will handle hashing automatically based on model casts
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
     */
    public function webhook(Request $request)
    {
        $webhookSecret = env('RAZORPAY_WEBHOOK_SECRET');
        $keyId = env('RAZORPAY_KEY', config('services.razorpay.key'));
        $keySecret = env('RAZORPAY_SECRET', config('services.razorpay.secret'));

        $api = new Api($keyId, $keySecret);

        $webhookSignature = $request->header('X-Razorpay-Signature');
        $isWebhook = !empty($webhookSignature);

        $orderId = null;
        $paymentId = null;
        $paymentStatus = null;
        $payload = $request->all();

        try {
            /*
            |--------------------------------------------------------------------------
            | WEBHOOK VERIFICATION (From Razorpay Events)
            |--------------------------------------------------------------------------
            */
            if ($isWebhook) {
                if (!$webhookSecret) {
                    Log::error('Razorpay Webhook secret missing');
                    return response()->json(['status' => false, 'message' => 'Webhook secret missing'], 400);
                }

                $api->utility->verifyWebhookSignature(
                    $request->getContent(),
                    $webhookSignature,
                    $webhookSecret
                );

                $event = $payload['event'] ?? null;

                if ($event === 'payment.captured') {
                    $paymentEntity = data_get($payload, 'payload.payment.entity');
                    $orderId = $paymentEntity['order_id'] ?? null;
                    $paymentId = $paymentEntity['id'] ?? null;
                    $paymentStatus = $paymentEntity['status'] ?? null;
                } elseif ($event === 'order.paid') {
                    $orderEntity = data_get($payload, 'payload.order.entity');
                    $paymentEntity = data_get($payload, 'payload.payment.entity');
                    $orderId = $orderEntity['id'] ?? null;
                    $paymentId = $paymentEntity['id'] ?? null;
                    $paymentStatus = $paymentEntity['status'] ?? 'paid';
                }
            }
            /*
            |--------------------------------------------------------------------------
            | CLIENT SIDE VERIFICATION (Direct from Frontend)
            |--------------------------------------------------------------------------
            */
            elseif (
                $request->filled('razorpay_order_id') &&
                $request->filled('razorpay_payment_id') &&
                $request->filled('razorpay_signature')
            ) {
                $api->utility->verifyPaymentSignature([
                    'razorpay_order_id' => $request->razorpay_order_id,
                    'razorpay_payment_id' => $request->razorpay_payment_id,
                    'razorpay_signature' => $request->razorpay_signature,
                ]);

                $orderId = $request->razorpay_order_id;
                $paymentId = $request->razorpay_payment_id;
                $paymentStatus = 'captured';
            }
            /*
            |--------------------------------------------------------------------------
            | ORDER ID ONLY VERIFICATION (Manual Check)
            |--------------------------------------------------------------------------
            */
            elseif ($request->filled('razorpay_order_id')) {
                $orderId = $request->razorpay_order_id;
                $razorpayOrder = $api->order->fetch($orderId);
                $payments = $razorpayOrder->payments();

                if (is_object($payments) && method_exists($payments, 'toArray')) {
                    $payments = $payments->toArray();
                }

                $paymentList = $payments['items'] ?? [];
                $payment = collect($paymentList)->first(function ($item) {
                    return in_array($item['status'] ?? '', ['captured', 'authorized']);
                });

                if (!$payment) {
                    return response()->json(['status' => false, 'message' => 'Payment not completed'], 400);
                }

                $paymentId = $payment['id'];
                $paymentStatus = $payment['status'];
            } else {
                return response()->json(['status' => false, 'message' => 'Invalid request'], 400);
            }

        } catch (SignatureVerificationError $e) {
            Log::error('Razorpay signature verification failed: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Invalid signature'], 400);
        } catch (\Exception $e) {
            Log::error('Razorpay verification error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }

        if (!$orderId) {
            return response()->json(['status' => false, 'message' => 'Order ID not found'], 400);
        }

        // Find the pending user
        $pendingUser = PendingUser::where('razorpay_order_id', $orderId)->first();

        if (!$pendingUser) {
            return response()->json(['status' => false, 'message' => 'Pending user record not found for this order'], 404);
        }

        // If already processed, return the user session
        if ($pendingUser->status === 'paid') {
            $existingUser = User::where('email', $pendingUser->email)->first();
            if ($existingUser) {
                Auth::login($existingUser);
                $token = $existingUser->createToken('mobile_app')->plainTextToken;
                return response()->json([
                    'status' => true,
                    'message' => 'Already processed. Session restored.',
                    'token' => $token,
                    'user' => new UserResource($existingUser)
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | MIGRATE TO USERS TABLE & INITIALIZE SESSION
        |--------------------------------------------------------------------------
        */
        DB::beginTransaction();
        try {
            // Update Pending User Status
            $pendingUser->update(['status' => 'paid']);

            // Create actual User record
            $newUser = User::create([
                'name' => $pendingUser->name,
                'email' => $pendingUser->email,
                'phone' => $pendingUser->phone,
                'password' => $pendingUser->password, // Note: password is already hashed in PendingUser (check model casts)
                'role' => 'user',
                'status' => 'active',
                'razorpay_order_id' => $orderId,
                'razorpay_payment_id' => $paymentId,
                'payment_status' => $paymentStatus,
                'razorpay_signature' => $request->razorpay_signature ?? $webhookSignature,
            ]);

            DB::commit();

            // Initialize authenticated session
            Auth::login($newUser);
            $token = $newUser->createToken('mobile_app')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Registration successful and session initialized.',
                'token' => $token,
                'user' => new UserResource($newUser)
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration Finalization Error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Failed to complete registration.'], 500);
        }
    }


}
