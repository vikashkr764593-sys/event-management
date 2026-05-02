<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
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
                    ->subject('New Order Placed: #' . $this->order->id)
                    ->greeting('Hello Admin,')
                    ->line('A new order has been placed on the platform.')
                    ->line('Order ID: #' . $this->order->id)
                    ->line('Customer: ' . $this->order->user->name)
                    ->line('Total Amount: ₹' . number_format($this->order->total_amount, 2))
                    ->action('View Order Details', url('/admin/orders/' . $this->order->id))
                    ->line('Please review and monitor the payment status.');
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
            'title' => 'New Order',
            'message' => 'New order received from ' . $this->order->user->name . ' for ₹' . number_format($this->order->total_amount, 2),
            'type' => 'new_order'
        ];
    }
}
