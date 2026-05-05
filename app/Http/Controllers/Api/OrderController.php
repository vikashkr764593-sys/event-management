<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use App\Http\Resources\OrderResource;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class OrderController extends Controller
{
    protected $orderService;
    protected $orderRepo;

    public function __construct(OrderService $orderService, OrderRepositoryInterface $orderRepo)
    {
        $this->orderService = $orderService;
        $this->orderRepo = $orderRepo;
    }
    
    /**
     * Fetch orders for the currently authenticated user.
     */
    public function index(Request $request): JsonResponse
    { 
        try {
            $orders = $this->orderService->getUserOrders($request->user()->id);
            return response()->json([
                'status' => true, 
                'data'   => OrderResource::collection($orders)
            ]); 
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Create a new order from the user's cart (Checkout).
     * Supports Razorpay and COD.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'payment_method' => 'required|in:razorpay,cod',
                'address'        => 'required|array',
                'address.street' => 'required|string|max:255',
                'address.city'   => 'required|string|max:100',
                'address.state'  => 'required|string|max:100',
                'address.pincode'=> 'required|string|size:6',
                'address.phone'  => 'required|string|min:10',
                'verification_code' => 'required_if:payment_method,cod' // Mocking COD verification
            ]);

            // Add simple logic for COD verification if needed
            if ($request->payment_method === 'cod' && $request->verification_code !== '1234') {
                return response()->json(['status' => false, 'message' => 'Invalid COD verification code.'], 400);
            }

            $order = $this->orderService->checkout($request->user()->id, $request->all());
            
            return response()->json([
                'status' => true, 
                'message' => 'Order initialized successfully.',
                'data'   => new OrderResource($order->load('items.instrument'))
            ], 201);
        } catch (Exception $e) { 
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400); 
        }
    }

    /**
     * Fetch details of a specific order.
     */
    public function show($id): JsonResponse
    {
        try {
            $order = $this->orderRepo->find($id);
            return response()->json([
                'status' => true, 
                'data'   => new OrderResource($order->load('items.instrument'))
            ]);
        } catch (Exception $e) { 
            return response()->json(['status' => false, 'message' => 'Order not found.'], 404); 
        }
    }
 
    /**
     * Verify Razorpay Payment.
     */
    public function verifyPayment(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'order_id'            => 'required|exists:orders,id', 
                'razorpay_payment_id' => 'required|string', 
                'razorpay_signature'  => 'required|string'
            ]);
 
            $order = $this->orderService->verifyPayment(
                $request->order_id, 
                $request->razorpay_payment_id, 
                $request->razorpay_signature
            );

            return response()->json([
                'status' => true, 
                'message' => 'Payment verified and order confirmed successfully.',
                'data'   => new OrderResource($order->load('items.instrument'))
            ]);
        } catch (Exception $e) { 
            $errorMessage = $e->getMessage();
            
            // Notify Admins of failure if order exists
            if ($request->has('order_id')) {
                $order = \App\Models\Order::find($request->order_id);
                if ($order) {
                    $admins = \App\Models\User::where('role', 'admin')->get();
                    // Admin gets the full error for debugging
                    \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\PaymentFailedNotification($order, $errorMessage));
                }
            }

            // Mobile App gets a clean message
            return response()->json([
                'status' => false, 
                'message' => 'Payment verification failed. Please contact support if the amount was deducted.'
            ], 400); 
        }
    }
}
