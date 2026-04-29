<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
        $webhookSecret = env('RAZORPAY_WEBHOOK_SECRET', config('services.razorpay.webhook_secret'));
        $webhookSignature = $request->header('X-Razorpay-Signature');

        if (!$webhookSecret || !$webhookSignature) {
            return response()->json(['error' => 'Invalid webhook configuration or signature missing'], 400);
        }

        $keyId = env('RAZORPAY_KEY', config('services.razorpay.key'));
        $keySecret = env('RAZORPAY_SECRET', config('services.razorpay.secret'));
        
        $api = new Api($keyId, $keySecret);

        try {
            $api->utility->verifyWebhookSignature($request->getContent(), $webhookSignature, $webhookSecret);
        } catch (SignatureVerificationError $e) {
            Log::warning('Razorpay Webhook Signature Verification Failed.');
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $payload = $request->all();

        // We only care about order.paid event for registration
        if (isset($payload['event']) && $payload['event'] === 'order.paid') {
            $orderId = $payload['payload']['order']['entity']['id'] ?? null;

            if ($orderId) {
                $pendingUser = PendingUser::where('razorpay_order_id', $orderId)->first();

                if ($pendingUser && $pendingUser->status === 'pending') {
                    try {
                        DB::transaction(function () use ($pendingUser) {
                            User::create([
                                'name' => $pendingUser->name,
                                'email' => $pendingUser->email,
                                'phone' => $pendingUser->phone,
                                // we can just pass the already hashed password
                                'password' => $pendingUser->password, 
                                'role' => 'user' // Default role
                            ]);

                            $pendingUser->update(['status' => 'paid']);
                        });
                        Log::info('User registered successfully from webhook for order: ' . $orderId);
                    } catch (\Exception $e) {
                        Log::error('Error creating user from webhook: ' . $e->getMessage());
                        return response()->json(['error' => 'Database transaction failed'], 500);
                    }
                }
            }
        }

        return response()->json(['status' => 'success']);
    }
}
