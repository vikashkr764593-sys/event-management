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
        $pendingBookings = Booking::where('status', 'Pending')->count();
        $totalRevenue = Order::where('status', 'approved')->sum('total_amount');
        $outOfStock = Instrument::where('stock', '<=', 0)->count();

        // Chart 1: Booking Trends (Grouped by status)
        $bookingStatuses = Booking::selectRaw('status, count(*) as count')->groupBy('status')->pluck('count', 'status')->toArray();
        
        $bookingChartLabels = ['Pending', 'Approved', 'Rejected'];
        $bookingChartData = [
            $bookingStatuses['Pending'] ?? 0,
            $bookingStatuses['Approved'] ?? 0,
            $bookingStatuses['Rejected'] ?? 0,
        ];

        // Chart 2: Inventory Usage (Grouped by category)
        $inventoryCategories = Instrument::selectRaw('category, sum(stock) as total_stock')->groupBy('category')->pluck('total_stock', 'category')->toArray();
        $inventoryChartLabels = array_keys($inventoryCategories);
        // Replace empty category with 'Uncategorized'
        foreach ($inventoryChartLabels as &$label) {
            if (empty($label)) $label = 'Uncategorized';
        }
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





    /**
     * View the Reports screen
     */
    public function reports()
    {
        return view('pages.reports.index');
    }
}
