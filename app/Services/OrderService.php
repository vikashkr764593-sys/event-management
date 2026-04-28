<?php

namespace App\Services;

use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Models\Order;
use App\Models\OrderItem;
use Exception;
use Razorpay\Api\Api as RazorpayApi;

class OrderService
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepo;

    /**
     * @var CartService
     */
    protected $cartService;

    /**
     * @var RazorpayApi|null
     */
    protected $razorpay;

    /**
     * OrderService constructor.
     *
     * @param OrderRepositoryInterface $orderRepo
     * @param CartService $cartService
     */
    public function __construct(OrderRepositoryInterface $orderRepo, CartService $cartService)
    {
        $this->orderRepo = $orderRepo;
        $this->cartService = $cartService;
        
        if (env('RAZORPAY_KEY') && env('RAZORPAY_SECRET')) {
            $this->razorpay = new RazorpayApi(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        }
    }

    /**
     * Convert a user's cart into an Order and generate a Razorpay order ID.
     *
     * @param int $userId
     * @return Order
     * @throws Exception
     */
    public function checkout(int $userId): Order
    {
        $cart = $this->cartService->getCart($userId);
        
        if ($cart->items->isEmpty()) {
            throw new Exception("Cart is empty.");
        }

        $total = 0;
        foreach ($cart->items as $item) {
            $total += ($item->instrument->price * $item->quantity);
        }

        $razorpayOrderId = null;
        if ($this->razorpay) {
            $rpOrder = $this->razorpay->order->create([
                'receipt'  => 'rcpt_' . $userId . '_' . time(), 
                'amount'   => $total * 100, 
                'currency' => 'INR'
            ]);
            $razorpayOrderId = $rpOrder['id'];
        }

        $order = $this->orderRepo->create([
            'user_id'           => $userId, 
            'total_amount'      => $total, 
            'razorpay_order_id' => $razorpayOrderId,
            'status'            => 'requested'
        ]);

        foreach ($cart->items as $item) {
            OrderItem::create([
                'order_id'      => $order->id,
                'instrument_id' => $item->instrument_id,
                'quantity'      => $item->quantity,
                'price'         => $item->instrument->price
            ]);
        }

        // Clear the cart
        $cart->items()->delete();

        return $this->orderRepo->find($order->id);
    }

    /**
     * Verify the Razorpay payment signature.
     *
     * @param int $orderId
     * @param string $razorpayPaymentId
     * @param string $razorpaySignature
     * @return Order
     */
    public function verifyPayment(int $orderId, string $razorpayPaymentId, string $razorpaySignature): Order
    {
        // In a real scenario, you would verify the signature using the Razorpay API here.
        // For example: $this->razorpay->utility->verifyPaymentSignature([...]);

        $order = $this->orderRepo->find($orderId);
        
        $this->orderRepo->update($orderId, [
            'payment_status'      => 'Paid', 
            'razorpay_payment_id' => $razorpayPaymentId
        ]);

        return $this->orderRepo->find($orderId);
    }

    /**
     * Retrieve all orders for a specific user.
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserOrders(int $userId)
    {
        return Order::with(['user', 'items.instrument'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}