<?php

namespace App\Repositories\Contracts;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Collection;

interface BookingRepositoryInterface
{
    /**
     * Retrieve all Booking records.
     *
     * @return Collection|Booking[]
     */
    public function all(): Collection;

    /**
     * Find a Booking by ID.
     *
     * @param int $id
     * @return Booking
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function find(int $id): Booking;

    /**
     * Create a new Booking.
     *
     * @param array $data
     * @return Booking
     */
    public function create(array $data): Booking;

    /**
     * Update an existing Booking.
     *
     * @param int $id
     * @param array $data
     * @return Booking
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update(int $id, array $data): Booking;

    /**
     * Delete a Booking by ID.
     *
     * @param int $id
     * @return bool
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function delete(int $id): bool;
}