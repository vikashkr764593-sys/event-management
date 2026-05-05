<?php

namespace App\Services;

use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Instrument;
use App\Notifications\NewOrderNotification;
use App\Notifications\OrderConfirmedNotification;
use App\Notifications\PaymentFailedNotification;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Razorpay\Api\Api as RazorpayApi;

class OrderService
{
    protected $orderRepo;
    protected $cartService;
    protected $addressValidationService;
    protected $razorpay;

    public function __construct(
        OrderRepositoryInterface $orderRepo, 
        CartService $cartService,
        AddressValidationService $addressValidationService
    ) {
        $this->orderRepo = $orderRepo;
        $this->cartService = $cartService;
        $this->addressValidationService = $addressValidationService;
        
        $keyId = config('services.razorpay.key');
        $keySecret = config('services.razorpay.secret');

        if ($keyId && $keySecret) {
            $this->razorpay = new RazorpayApi($keyId, $keySecret);
        }
    }

    /**
     * Checkout process supporting Razorpay and COD.
     * Implementation: Deducts stock immediately to "reserve" it for 30 minutes.
     */
    public function checkout(int $userId, array $checkoutData): Order
    {
        $paymentMethod = $checkoutData['payment_method'] ?? 'razorpay';
        $shippingAddress = $this->addressValidationService->validate($checkoutData['address']);
        
        $cart = $this->cartService->getCart($userId);
        
        if ($cart->items->isEmpty()) {
            throw new Exception("Cart is empty.");
        }

        return DB::transaction(function () use ($userId, $cart, $paymentMethod, $shippingAddress) {
            $total = 0;
            foreach ($cart->items as $item) {
                $total += ($item->instrument->price * $item->quantity);
            }

            $order = $this->orderRepo->create([
                'user_id'          => $userId, 
                'total_amount'     => $total, 
                'payment_method'   => $paymentMethod,
                'shipping_address' => $shippingAddress,
                'status'           => 'requested',
                'payment_status'   => 'Pending'
            ]);

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id'      => $order->id,
                    'instrument_id' => $item->instrument_id,
                    'quantity'      => $item->quantity,
                    'price'         => $item->instrument->price
                ]);

                // Always reserve stock on order creation to prevent overselling during payment attempts
                $instrument = Instrument::lockForUpdate()->find($item->instrument_id);
                if ($instrument->stock < $item->quantity) {
                    throw new Exception("Insufficient stock for {$instrument->name}.");
                }
                $instrument->decrement('stock', $item->quantity);
            }

            // Clear the cart
            $cart->items()->delete();

            // Razorpay Initialization
            if ($paymentMethod === 'razorpay' && $this->razorpay) {
                $rpOrder = $this->razorpay->order->create([
                    'receipt'  => 'rcpt_' . $order->id, 
                    'amount'   => $total * 100, 
                    'currency' => 'INR'
                ]);
                $order->update(['razorpay_order_id' => $rpOrder['id']]);
            }

            // Notify Admins
            $admins = User::where('role', 'admin')->get();
            Notification::send($admins, new NewOrderNotification($order));

            return $order;
        });
    }

    /**
     * Regenerate Razorpay Order ID for a retry attempt.
     */
    public function regenerateRazorpayOrder(int $orderId, int $userId): Order
    {
        $order = $this->orderRepo->find($orderId);

        if (!$order || $order->user_id !== $userId) {
            throw new Exception("Order not found.");
        }

        if ($order->payment_status === 'Paid') {
            throw new Exception("Order is already paid.");
        }

        // Re-validate total amount against items to prevent tampering
        $calculatedTotal = 0;
        foreach ($order->items as $item) {
            $calculatedTotal += ($item->price * $item->quantity);
        }

        if (abs($calculatedTotal - $order->total_amount) > 0.01) {
            throw new Exception("Price mismatch detected. Please contact support.");
        }

        // Generate new Razorpay Order ID
        if ($this->razorpay) {
            $rpOrder = $this->razorpay->order->create([
                'receipt'  => 'rcpt_retry_' . $order->id, 
                'amount'   => $order->total_amount * 100, 
                'currency' => 'INR'
            ]);
            
            $order->update([
                'razorpay_order_id' => $rpOrder['id'],
                'status' => 'requested' // Reset status to requested if it was failed
            ]);
        }

        return $order;
    }

    /**
     * Verify the Razorpay payment signature and confirm the order.
     */
    public function verifyPayment(int $orderId, string $paymentId, string $signature): Order
    {
        $order = $this->orderRepo->find($orderId);

        if (!$order) {
            throw new Exception("Order not found.");
        }

        if (!$this->razorpay) {
            throw new Exception("Razorpay is not configured.");
        }

        // Verify signature
        $attributes = [
            'razorpay_order_id'   => $order->razorpay_order_id,
            'razorpay_payment_id' => $paymentId,
            'razorpay_signature'  => $signature
        ];

        try {
            $this->razorpay->utility->verifyPaymentSignature($attributes);
        } catch (\Exception $e) {
            throw new Exception("Payment verification failed: Invalid signature.");
        }

        // Double check payment details from Razorpay API
        $payment = $this->razorpay->payment->fetch($paymentId);
        if ($payment->status !== 'captured') {
            throw new Exception("Payment not captured. Status: " . $payment->status);
        }

        if ($payment->amount != ($order->total_amount * 100)) {
            throw new Exception("Payment amount mismatch detected.");
        }

        return DB::transaction(function () use ($orderId, $paymentId, $signature) {
            $this->orderRepo->update($orderId, [
                'payment_status' => 'Paid',
                'razorpay_payment_id' => $paymentId,
                'razorpay_signature' => $signature,
                'status' => 'Processing'
            ]);

            $order = $this->orderRepo->find($orderId);

            // Notify User & Admins
            $order->user->notify(new OrderConfirmedNotification($order));
            $admins = User::where('role', 'admin')->get();
            Notification::send($admins, new OrderConfirmedNotification($order));

            return $order;
        });
    }

    /**
     * Retrieve all orders for a specific user.
     */
    public function getUserOrders(int $userId)
    {
        return Order::with(['user', 'items.instrument'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}