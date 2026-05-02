<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewBookingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
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
                    ->subject('New Booking Received: #' . $this->booking->id)
                    ->greeting('Hello Admin,')
                    ->line('A new booking has been created on the platform.')
                    ->line('Booking ID: #' . $this->booking->id)
                    ->line('Customer: ' . $this->booking->user->name)
                    ->line('Singer: ' . ($this->booking->singer->stage_name ?? $this->booking->singer->name))
                    ->line('Event Date: ' . $this->booking->event_date)
                    ->action('View Booking Details', url('/admin/bookings/' . $this->booking->id))
                    ->line('Please review and process this booking.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'title' => 'New Booking',
            'message' => 'New booking received from ' . $this->booking->user->name,
            'type' => 'new_booking'
        ];
    }
}
