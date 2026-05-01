<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Booking;
use App\Models\User;
use App\Jobs\GenerateReportJob;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminReportController extends Controller
{
    public function index(Request $request)
    {
        $range = $request->get('range', 'this_month');
        $startDate = $this->getStartDate($range);
        $endDate = Carbon::now();

        // 1. KPI Statistics
        $totalRevenue = Order::where('payment_status', 'Paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_amount');

        $totalBookings = Booking::whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $newUserCount = User::whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // 2. Trend Data (Revenue & Bookings)
        $trends = $this->getTrendData($startDate, $endDate, $range);

        return view('admin.reports.index', compact(
            'totalRevenue',
            'totalBookings',
            'newUserCount',
            'range',
            'trends'
        ));
    }

    public function export(Request $request)
    {
        $reportType = $request->get('type', 'orders');
        $range = $request->get('range', 'this_month');

        GenerateReportJob::dispatch(auth()->id(), $reportType, $range);

        return back()->with('success', "Your " . str_replace('_', ' ', $reportType) . " report is being generated. You will receive a notification with the download link shortly.");
    }

    private function getStartDate($range)
    {
        return match ($range) {
            'today'      => Carbon::today(),
            '7_days'     => Carbon::now()->subDays(7),
            'this_month' => Carbon::now()->startOfMonth(),
            'this_year'  => Carbon::now()->startOfYear(),
            default      => Carbon::now()->startOfMonth(),
        };
    }

    private function getTrendData($startDate, $endDate, $range)
    {
        // For 'today', we group by hour. For others, we group by day.
        $groupByFormat = ($range === 'today') ? '%H:00' : '%Y-%m-%d';
        $displayFormat = ($range === 'today') ? 'H:i' : 'M d';

        // Revenue Trend
        $revenueData = Order::where('payment_status', 'Paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw("DATE_FORMAT(created_at, '$groupByFormat') as period"),
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy('period')
            ->orderBy('period')
            ->pluck('total', 'period')
            ->toArray();

        // Bookings Trend
        $bookingData = Booking::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw("DATE_FORMAT(created_at, '$groupByFormat') as period"),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('period')
            ->orderBy('period')
            ->pluck('count', 'period')
            ->toArray();

        // User Trend
        $userData = User::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw("DATE_FORMAT(created_at, '$groupByFormat') as period"),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('period')
            ->orderBy('period')
            ->pluck('count', 'period')
            ->toArray();

        // Generate all time points between start and end to ensure no gaps in chart
        $labels = [];
        $revenueValues = [];
        $bookingValues = [];
        $userValues = [];

        $current = clone $startDate;
        while ($current <= $endDate) {
            $key = $current->format(($range === 'today') ? 'H:00' : 'Y-m-d');
            $label = $current->format($displayFormat);
            
            $labels[] = $label;
            $revenueValues[] = $revenueData[$key] ?? 0;
            $bookingValues[] = $bookingData[$key] ?? 0;
            $userValues[] = $userData[$key] ?? 0;

            if ($range === 'today') {
                $current->addHour();
            } else {
                $current->addDay();
            }
        }

        return [
            'labels'   => $labels,
            'revenue'  => $revenueValues,
            'bookings' => $bookingValues,
            'users'    => $userValues,
        ];
    }
}
