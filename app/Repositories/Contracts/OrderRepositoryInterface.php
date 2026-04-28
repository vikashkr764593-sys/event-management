<?php

namespace App\Repositories\Contracts;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

interface OrderRepositoryInterface
{
    /**
     * Retrieve all Order records.
     *
     * @return Collection|Order[]
     */
    public function all(): Collection;

    /**
     * Find a Order by ID.
     *
     * @param int $id
     * @return Order
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function find(int $id): Order;

    /**
     * Create a new Order.
     *
     * @param array $data
     * @return Order
     */
    public function create(array $data): Order;

    /**
     * Update an existing Order.
     *
     * @param int $id
     * @param array $data
     * @return Order
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update(int $id, array $data): Order;

    /**
     * Delete a Order by ID.
     *
     * @param int $id
     * @return bool
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function delete(int $id): bool;
}