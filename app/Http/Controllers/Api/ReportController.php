<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\InstrumentRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Exception;

class ReportController extends Controller
{
    protected $bookingRepo;
    protected $orderRepo;
    protected $instrumentRepo;

    public function __construct(
        BookingRepositoryInterface $bookingRepo, 
        OrderRepositoryInterface $orderRepo, 
        InstrumentRepositoryInterface $instrumentRepo
    ) {
        $this->bookingRepo = $bookingRepo;
        $this->orderRepo = $orderRepo;
        $this->instrumentRepo = $instrumentRepo;
    }

    /**
     * Get aggregated booking statistics.
     *
     * @return JsonResponse
     */
    public function bookings(): JsonResponse
    {
        try {
            $allBookings = $this->bookingRepo->all();
            return response()->json([
                'status' => true, 
                'data'   => [
                    'total'    => $allBookings->count(), 
                    'pending'  => $allBookings->where('status', 'Pending')->count(),
                    'approved' => $allBookings->where('status', 'Approved')->count(),
                    'rejected' => $allBookings->where('status', 'Rejected')->count()
                ]
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get aggregated inventory statistics.
     *
     * @return JsonResponse
     */
    public function inventory(): JsonResponse
    {
        try {
            $instruments = $this->instrumentRepo->all();
            
            $totalValue = $instruments->reduce(function ($carry, $item) {
                return $carry + ($item->price * $item->stock);
            }, 0);

            return response()->json([
                'status' => true, 
                'data'   => [
                    'total_unique_instruments' => $instruments->count(),
                    'total_stock_items'        => $instruments->sum('stock'),
                    'out_of_stock'             => $instruments->where('stock', '<=', 0)->count(),
                    'total_inventory_value'    => $totalValue
                ]
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get aggregated order statistics.
     *
     * @return JsonResponse
     */
    public function orders(): JsonResponse
    {
        try {
            $allOrders = $this->orderRepo->all();
            return response()->json([
                'status' => true, 
                'data'   => [
                    'total_orders'    => $allOrders->count(),
                    'total_revenue'   => $allOrders->sum('total_amount'),
                    'pending_orders'  => $allOrders->where('status', 'requested')->count(),
                    'approved_orders' => $allOrders->where('status', 'approved')->count(),
                    'rejected_orders' => $allOrders->where('status', 'rejected')->count(),
                    'paid_orders'     => $allOrders->where('payment_status', 'Paid')->count()
                ]
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }
}