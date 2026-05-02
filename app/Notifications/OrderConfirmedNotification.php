<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderConfirmedNotification extends Notification implements ShouldQueue
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
                    ->subject('Order Confirmation: #' . $this->order->id)
                    ->greeting('Hello ' . $this->order->user->name . ',')
                    ->line('Thank you for your purchase! Your payment has been successfully processed.')
                    ->line('Order ID: #' . $this->order->id)
                    ->line('Amount Paid: ₹' . number_format($this->order->total_amount, 2))
                    ->action('View My Orders', url('/orders/my'))
                    ->line('We are now processing your order. You will receive further updates soon.');
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
            'message' => 'Your payment for Order #' . $this->order->id . ' has been confirmed.',
            'type' => 'order_confirmation'
        ];
    }
}
