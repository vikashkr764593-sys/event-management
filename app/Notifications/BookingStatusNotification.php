<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingStatusNotification extends Notification implements ShouldQueue
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
        $status = strtolower($this->booking->status);
        $subject = 'Your Booking is ' . ucfirst($status) . ': #' . $this->booking->id;
        
        $message = (new MailMessage)
                    ->subject($subject)
                    ->greeting('Hello ' . $this->booking->user->name . ',');

        if ($status === 'approved') {
            $message->line('Great news! Your booking with ' . ($this->booking->singer->stage_name ?? $this->booking->singer->name) . ' has been approved.')
                    ->line('You can now proceed to the next steps.')
                    ->action('View My Bookings', url('/bookings/my'));
        } else {
            $message->line('We regret to inform you that your booking with ' . ($this->booking->singer->stage_name ?? $this->booking->singer->name) . ' has been rejected.')
                    ->line('If you have any questions, please contact our support team.');
        }

        return $message;
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
            'status' => $this->booking->status,
            'title' => 'Booking Update',
            'message' => 'Your booking status has been updated to ' . $this->booking->status,
            'type' => 'booking_status_update'
        ];
    }
}
