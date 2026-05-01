<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Order;
use App\Models\Instrument;
use App\Models\Chat;
use App\Models\Notification;

class DashboardController extends Controller
{
    /**
     * View the main dashboard with charts and KPIs.
     */
    public function index()
    {
        // KPIs
        $totalBookings = Booking::count();
        $pendingBookings = Booking::where('status', 'pending')->count();
        $totalRevenue = Order::where('payment_status', 'Paid')->sum('total_amount');
        $outOfStock = Instrument::where('stock', '<=', 0)->count();

        // Chart 1: Booking Trends (Grouped by status)
        $bookingStatuses = Booking::selectRaw('status, count(*) as count')->groupBy('status')->pluck('count', 'status')->toArray();
        
        $bookingChartLabels = ['Pending', 'Approved', 'Rejected'];
        $bookingChartData = [
            $bookingStatuses['pending'] ?? 0,
            $bookingStatuses['approved'] ?? 0,
            $bookingStatuses['rejected'] ?? 0,
        ];

        // Chart 2: Inventory Usage (Grouped by category)
        $inventoryCategories = Instrument::leftJoin('categories', 'instruments.category_id', '=', 'categories.id')
            ->selectRaw('COALESCE(categories.name, "Uncategorized") as category_name, sum(instruments.stock) as total_stock')
            ->groupBy('category_name')
            ->pluck('total_stock', 'category_name')
            ->toArray();
        $inventoryChartLabels = array_keys($inventoryCategories);
        $inventoryChartData = array_values($inventoryCategories);

        return view('pages.dashboard.index', compact(
            'totalBookings', 
            'pendingBookings', 
            'totalRevenue', 
            'outOfStock',
            'bookingChartLabels',
            'bookingChartData',
            'inventoryChartLabels',
            'inventoryChartData'
        ));
    }






}
