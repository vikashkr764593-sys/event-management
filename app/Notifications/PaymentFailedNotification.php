<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;
    protected $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order, string $reason = 'Payment failed')
    {
        $this->order = $order;
        $this->reason = $reason;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->error()
                    ->subject('Payment Failed Alert: Order #' . $this->order->id)
                    ->greeting('Hello Admin,')
                    ->line('A payment failure has been detected for an order.')
                    ->line('Order ID: #' . $this->order->id)
                    ->line('Customer: ' . $this->order->user->name)
                    ->line('Attempted Amount: ₹' . number_format($this->order->total_amount, 2))
                    ->line('Failure Reason: ' . $this->reason)
                    ->action('Check Order Status', url('/admin/orders/' . $this->order->id))
                    ->line('Please review the transaction logs in the Razorpay dashboard.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'message' => 'Payment failed for Order #' . $this->order->id . ' (Reason: ' . $this->reason . ')',
            'type' => 'payment_failure'
        ];
    }
}
