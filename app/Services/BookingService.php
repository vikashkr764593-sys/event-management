<?php

namespace App\Services;

use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Models\User;
use App\Notifications\NewBookingNotification;
use Illuminate\Support\Facades\Notification;

class BookingService
{
    protected $bookingRepo;
    public function __construct(BookingRepositoryInterface $bookingRepo)
    {
        $this->bookingRepo = $bookingRepo;
    }
    public function createBooking(array $data, $userId)
    {
        $data['user_id'] = $userId;
        $booking = $this->bookingRepo->create($data);

        // Notify Admins
        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new NewBookingNotification($booking));

        return $booking;
    }
    public function getUserBookings($userId)
    {
        return \App\Models\Booking::with('singer')->where('user_id', $userId)->get();
    }
}
