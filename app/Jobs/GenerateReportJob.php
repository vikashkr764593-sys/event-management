<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class GenerateReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $reportType;
    protected $range;

    /**
     * Create a new job instance.
     */
    public function __construct($userId, $reportType, $range)
    {
        $this->userId = $userId;
        $this->reportType = $reportType;
        $this->range = $range;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $startDate = $this->getStartDate($this->range);
        $endDate = Carbon::now();
        $filename = "report_{$this->reportType}_" . now()->format('YmdHis') . ".csv";
        $filePath = "reports/{$filename}";

        $handle = fopen('php://temp', 'r+');

        if ($this->reportType === 'orders') {
            $this->generateOrdersReport($handle, $startDate, $endDate);
        } else {
            $this->generateSingerEarningsReport($handle, $startDate, $endDate);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        Storage::disk('public')->put($filePath, $content);

        // Notify the user
        Notification::create([
            'user_id' => $this->userId,
            'title'   => "Report Ready: " . ucfirst($this->reportType),
            'message' => "Your " . str_replace('_', ' ', $this->range) . " report has been generated. [Download Now](" . asset('storage/' . $filePath) . ")",
            'is_read' => false,
        ]);
    }

    private function generateOrdersReport($handle, $startDate, $endDate)
    {
        fputcsv($handle, ['Order ID', 'Customer', 'Date', 'Total Amount', 'Tax (18%)', 'Grand Total', 'Status', 'Payment Status']);

        $orders = Order::with('user')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        foreach ($orders as $order) {
            $tax = $order->total_amount * 0.18;
            $grandTotal = $order->total_amount + $tax;
            fputcsv($handle, [
                $order->id,
                $order->user->name ?? 'Unknown',
                $order->created_at->format('Y-m-d H:i'),
                $order->total_amount,
                number_format($tax, 2),
                number_format($grandTotal, 2),
                $order->status,
                $order->payment_status,
            ]);
        }
    }

    private function generateSingerEarningsReport($handle, $startDate, $endDate)
    {
        fputcsv($handle, ['Singer', 'Booking ID', 'Event Date', 'Earnings', 'Tax (18%)', 'Total Payout', 'Status']);

        $bookings = Booking::with('singer.user')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'approved')
            ->get();

        foreach ($bookings as $booking) {
            $earnings = $booking->singer->fee ?? 0;
            $tax = $earnings * 0.18;
            $total = $earnings + $tax;
            fputcsv($handle, [
                $booking->singer->name ?? ($booking->singer->user->name ?? 'Unknown'),
                $booking->id,
                $booking->event_date,
                $earnings,
                number_format($tax, 2),
                number_format($total, 2),
                $booking->status,
            ]);
        }
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
}
