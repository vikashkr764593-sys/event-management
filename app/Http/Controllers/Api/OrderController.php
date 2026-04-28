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
    /**
     * @var OrderService
     */
    protected $orderService;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepo;

    /**
     * OrderController constructor.
     *
     * @param OrderService $orderService
     * @param OrderRepositoryInterface $orderRepo
     */
    public function __construct(OrderService $orderService, OrderRepositoryInterface $orderRepo)
    {
        $this->orderService = $orderService;
        $this->orderRepo = $orderRepo;
    }
    
    /**
     * Fetch orders for the currently authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
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
     * Fetch all orders (Admin).
     *
     * @return JsonResponse
     */
    public function allOrders(): JsonResponse
    { 
        try {
            $orders = $this->orderRepo->all();
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
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $order = $this->orderService->checkout($request->user()->id);
            return response()->json([
                'status' => true, 
                'data'   => new OrderResource($order)
            ], 201);
        } catch (Exception $e) { 
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400); 
        }
    }

    /**
     * Fetch details of a specific order.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $order = $this->orderRepo->find($id);
            return response()->json([
                'status' => true, 
                'data'   => new OrderResource($order)
            ]);
        } catch (Exception $e) { 
            return response()->json(['status' => false, 'message' => 'Order not found.'], 404); 
        }
    }

    /**
     * Verify Razorpay Payment.
     *
     * @param Request $request
     * @return JsonResponse
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
                'message' => 'Payment verified successfully.',
                'data'   => new OrderResource($order)
            ]);
        } catch (Exception $e) { 
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400); 
        }
    }

    /**
     * Admin: Approve an order.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function approve($id): JsonResponse
    {
        try {
            $order = $this->orderRepo->update($id, ['status' => 'approved']);
            return response()->json([
                'status'  => true, 
                'message' => 'Order approved successfully.', 
                'data'    => new OrderResource($order)
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Admin: Reject an order.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function reject($id): JsonResponse
    {
        try {
            $order = $this->orderRepo->update($id, ['status' => 'rejected']);
            return response()->json([
                'status'  => true, 
                'message' => 'Order rejected successfully.', 
                'data'    => new OrderResource($order)
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Admin: Update the status of an order.
     *
     * @param UpdateOrderStatusRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function updateStatus(UpdateOrderStatusRequest $request, $id): JsonResponse
    {
        try {
            $order = $this->orderRepo->update($id, ['status' => $request->status]);
            return response()->json([
                'status'  => true, 
                'message' => 'Order status updated.', 
                'data'    => new OrderResource($order)
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }
}