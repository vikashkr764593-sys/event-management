<?php

namespace App\Services;

use App\Repositories\Contracts\CartRepositoryInterface;
use App\Models\Cart;

class CartService
{
    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepo;

    /**
     * CartService constructor.
     *
     * @param CartRepositoryInterface $cartRepo
     */
    public function __construct(CartRepositoryInterface $cartRepo)
    {
        $this->cartRepo = $cartRepo;
    }

    /**
     * Get a user's cart, loaded with items and instruments.
     *
     * @param int $userId
     * @return Cart
     */
    public function getCart(int $userId): Cart
    {
        return $this->cartRepo->getOrCreateForUser($userId)->load('items.instrument');
    }

    /**
     * Add an instrument to the user's cart.
     *
     * @param int $userId
     * @param int $instrumentId
     * @param int $quantity
     * @return \App\Models\CartItem
     */
    public function addItem(int $userId, int $instrumentId, int $quantity)
    {
        $cart = $this->cartRepo->getOrCreateForUser($userId);
        return $this->cartRepo->addItem($cart->id, $instrumentId, $quantity);
    }

    /**
     * Update the quantity of a specific cart item.
     *
     * @param int $itemId
     * @param int $quantity
     * @return bool
     */
    public function updateItem(int $itemId, int $quantity): bool
    {
        return $this->cartRepo->updateItem($itemId, $quantity);
    }

    /**
     * Remove a specific cart item.
     *
     * @param int $itemId
     * @return bool
     */
    public function removeItem(int $itemId): bool
    {
        return $this->cartRepo->removeItem($itemId);
    }
}