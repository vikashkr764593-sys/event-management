<?php

namespace App\Services;

use App\Repositories\Contracts\BookingRepositoryInterface;

class BookingService
{
    protected $bookingRepo;
    public function __construct(BookingRepositoryInterface $bookingRepo)
    {
        $this->bookingRepo = $bookingRepo;
    }
    public function createBooking(array $data, $userId)
    {
        // Add external service calls here (e.g. Email/SMS notifications to Singer)
        $data['user_id'] = $userId;
        return $this->bookingRepo->create($data);
    }
    public function getUserBookings($userId)
    {
        return \App\Models\Booking::with('singer')->where('user_id', $userId)->get();
    }
}
