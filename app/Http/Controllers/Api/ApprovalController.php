<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Http\Resources\OrderResource;
use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Exception;

class ApprovalController extends Controller
{
    protected $bookingRepo;
    protected $orderRepo;

    public function __construct(BookingRepositoryInterface $bookingRepo, OrderRepositoryInterface $orderRepo)
    {
        $this->bookingRepo = $bookingRepo;
        $this->orderRepo = $orderRepo;
    }

    /**
     * Get all pending bookings and requested orders for admin approval.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            // Note: In a true production app, you might add specific filtering in the Repository.
            // For now, we fetch all and filter in memory since this is a unified dashboard endpoint.
            $bookings = $this->bookingRepo->all()->where('status', 'Pending');
            $orders = $this->orderRepo->all()->where('status', 'requested');
            
            return response()->json([
                'status' => true,
                'data' => [
                    'bookings' => BookingResource::collection($bookings),
                    'orders'   => OrderResource::collection($orders)
                ]
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Approve a booking or order request.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function approve(\Illuminate\Http\Request $request, $id): JsonResponse
    {
        try {
            $request->validate(['type' => 'required|string|in:booking,order']);
            
            if ($request->type === 'booking') {
                $record = $this->bookingRepo->update($id, ['status' => 'Approved']);
                $resource = new BookingResource($record);
            } else {
                $record = $this->orderRepo->update($id, ['status' => 'approved']);
                $resource = new OrderResource($record);
            }

            return response()->json([
                'status'  => true, 
                'message' => ucfirst($request->type) . ' approved successfully.',
                'data'    => $resource
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Reject a booking or order request.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function reject(\Illuminate\Http\Request $request, $id): JsonResponse
    {
        try {
            $request->validate(['type' => 'required|string|in:booking,order']);
            
            if ($request->type === 'booking') {
                $record = $this->bookingRepo->update($id, ['status' => 'Rejected']);
                $resource = new BookingResource($record);
            } else {
                $record = $this->orderRepo->update($id, ['status' => 'rejected']);
                $resource = new OrderResource($record);
            }

            return response()->json([
                'status'  => true, 
                'message' => ucfirst($request->type) . ' rejected successfully.',
                'data'    => $resource
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }
}