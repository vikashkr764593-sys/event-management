<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\Instrument;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Notifications\OrderReservationExpiringNotification;

class ReleaseExpiredStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:release-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Release stock for orders that have expired the 2-hour payment window.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        // 1. Handle Expiration (2 Hours)
        $expiredOrders = Order::where('status', 'Payment Pending')
            ->where('reservation_expires_at', '<=', $now)
            ->get();

        foreach ($expiredOrders as $order) {
            DB::transaction(function () use ($order) {
                foreach ($order->items as $item) {
                    $instrument = Instrument::lockForUpdate()->find($item->instrument_id);
                    if ($instrument) {
                        // Move from reserved back to available
                        $quantityToMove = min($item->quantity, $instrument->reserved_stock);
                        $instrument->increment('stock', $quantityToMove);
                        $instrument->decrement('reserved_stock', $quantityToMove);
                    }
                }

                $order->update(['status' => 'Cancelled - Payment Timeout']);
            });
            $this->info("Released stock for Order #{$order->id}");
        }

        // 2. Handle Warning (30 Minutes before expiration)
        // Warning threshold: reservation_expires_at is between now and 30 minutes from now
        $warningThreshold = $now->copy()->addMinutes(30);
        $expiringSoonOrders = Order::where('status', 'Payment Pending')
            ->where('reservation_expires_at', '>', $now)
            ->where('reservation_expires_at', '<=', $warningThreshold)
            // Ensure we only notify once (e.g., using a flag or checking if already notified)
            ->whereNull('notified_expiring_at')
            ->get();

        foreach ($expiringSoonOrders as $order) {
            $order->user->notify(new OrderReservationExpiringNotification($order));
            $order->update(['notified_expiring_at' => Carbon::now()]);
            $this->info("Sent expiration warning for Order #{$order->id}");
        }

        $this->info('Expiration check completed.');
    }
}
