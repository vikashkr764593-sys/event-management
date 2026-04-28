<?php

namespace App\Repositories\Contracts;

use App\Models\Cart;
use Illuminate\Database\Eloquent\Collection;

interface CartRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): Cart;
    public function create(array $data): Cart;
    public function update(int $id, array $data): Cart;
    public function delete(int $id): bool;

    /**
     * Get the cart for a user, or create one if it doesn't exist.
     *
     * @param int $userId
     * @return Cart
     */
    public function getOrCreateForUser(int $userId): Cart;

    /**
     * Add an item to the cart.
     *
     * @param int $cartId
     * @param int $instrumentId
     * @param int $quantity
     * @return \App\Models\CartItem
     */
    public function addItem(int $cartId, int $instrumentId, int $quantity);

    /**
     * Update a cart item's quantity.
     *
     * @param int $itemId
     * @param int $quantity
     * @return bool
     */
    public function updateItem(int $itemId, int $quantity): bool;

    /**
     * Remove an item from the cart.
     *
     * @param int $itemId
     * @return bool
     */
    public function removeItem(int $itemId): bool;
}