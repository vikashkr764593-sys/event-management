<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Order;

class OrderReservationExpiringNotification extends Notification implements ShouldQueue
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
                    ->subject('Action Required: Your Order Reservation is Expiring')
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line('The payment reservation for your Order #' . $this->order->id . ' is set to expire in less than 30 minutes.')
                    ->line('If the payment is not completed, the items will be released back into stock and your order will be cancelled.')
                    ->action('Complete Payment Now', url('/orders/' . $this->order->id))
                    ->line('Thank you for choosing us!');
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
            'title' => 'Payment Reservation Expiring',
            'message' => 'Your reservation for Order #' . $this->order->id . ' expires in 30 minutes.',
            'type' => 'reservation_expiring'
        ];
    }
}
