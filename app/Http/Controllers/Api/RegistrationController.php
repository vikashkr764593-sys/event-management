<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\PendingUser;
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
        $webhookSecret = env('RAZORPAY_WEBHOOK_SECRET', config('services.razorpay.webhook_secret'));
        $webhookSignature = $request->header('X-Razorpay-Signature');
        $isWebhook = !empty($webhookSignature);

        $keyId = env('RAZORPAY_KEY', config('services.razorpay.key'));
        $keySecret = env('RAZORPAY_SECRET', config('services.razorpay.secret'));
        $api = new Api($keyId, $keySecret);
        $payload = $request->all();

        try {
            if ($isWebhook) {
                // Server-to-server webhook verification
                if (!$webhookSecret) {
                    return response()->json(['error' => 'Invalid webhook configuration'], 400);
                }
                $api->utility->verifyWebhookSignature($request->getContent(), $webhookSignature, $webhookSecret);
            } elseif ($request->filled(['razorpay_order_id', 'razorpay_payment_id', 'razorpay_signature'])) {
                // Client-side payment verification
                $api->utility->verifyPaymentSignature([
                    'razorpay_order_id' => $request->input('razorpay_order_id'),
                    'razorpay_payment_id' => $request->input('razorpay_payment_id'),
                    'razorpay_signature' => $request->input('razorpay_signature'),
                ]);
                
                // Restructure payload for consistent processing
                $payload['event'] = 'payment.captured';
                $payload['payload']['payment']['entity'] = [
                    'id' => $request->input('razorpay_payment_id'),
                    'order_id' => $request->input('razorpay_order_id'),
                    'status' => 'captured',
                ];
            } else {
                return response()->json(['error' => 'Invalid webhook or verification request'], 400);
            }
        } catch (SignatureVerificationError $e) {
            Log::warning('Razorpay Signature Verification Failed.', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        Log::info('Razorpay webhook received', [
            'event' => $payload['event'] ?? 'unknown',
            'order_id' => data_get($payload, 'payload.order.entity.id'),
            'payment_order_id' => data_get($payload, 'payload.payment.entity.order_id'),
        ]);

        $event = $payload['event'] ?? null;
        $orderId = null;

        if ($event === 'payment.captured') {
            $orderId = data_get($payload, 'payload.payment.entity.order_id');
        } elseif ($event === 'order.paid') {
            $orderId = data_get($payload, 'payload.order.entity.id');
        }

        if (!$orderId) {
            Log::warning('Razorpay webhook ignored: no order id found', ['event' => $event]);
            return response()->json(['status' => false, 'message' => 'Order ID not found'], 400);
        }

        $pendingUser = PendingUser::where('razorpay_order_id', $orderId)->first();

        if (!$pendingUser) {
            Log::warning('Pending user not found for order', ['order_id' => $orderId]);
            return response()->json(['status' => false, 'message' => 'Pending registration not found'], 404);
        }

        if ($pendingUser->status !== 'pending') {
            Log::info('Payment already processed', ['order_id' => $orderId, 'status' => $pendingUser->status]);
            return response()->json(['status' => true, 'message' => 'Payment already processed'], 200);
        }

        $paymentId = data_get($payload, 'payload.payment.entity.id');
        $paymentStatus = data_get($payload, 'payload.payment.entity.status', 'captured');
        $razorpaySignature = $request->input('razorpay_signature');
        /** @var User|null $newUser */
        $newUser = null;

        try {
            DB::transaction(function () use ($pendingUser, $orderId, $paymentId, $paymentStatus, $razorpaySignature, &$newUser) {
                // Move pending user data to users table with payment details
                $newUser = User::create([
                    'name' => $pendingUser->name,
                    'email' => $pendingUser->email,
                    'phone' => $pendingUser->phone,
                    'password' => $pendingUser->password,
                    'role' => 'user',
                    'status' => 'active',
                    'razorpay_order_id' => $orderId,
                    'razorpay_payment_id' => $paymentId,
                    'payment_status' => $paymentStatus,
                    'razorpay_signature' => $razorpaySignature,
                ]);

                // Mark pending user as paid
                $pendingUser->update(['status' => 'paid']);
            });

            Log::info('User registered successfully', ['order_id' => $orderId, 'user_id' => $newUser->id]);
        } catch (\Exception $e) {
            Log::error('Error migrating pending user to active user', [
                'order_id' => $orderId,
                'message' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Failed to complete registration'], 500);
        }

        // For webhook callbacks, return minimal response
        if ($isWebhook) {
            return response()->json(['status' => 'success']);
        }

        // For client-side verification, return token and user details
        $token = $newUser->createToken('mobile_app')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Registration completed successfully',
            'token' => $token,
            'redirect_url' => url('/dashboard'),
            'user' => new UserResource($newUser),
        ], 200);
    }
}
