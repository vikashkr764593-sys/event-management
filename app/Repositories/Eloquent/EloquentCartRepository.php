<?php

namespace App\Repositories\Eloquent;

use App\Models\Cart;
use App\Models\CartItem;
use App\Repositories\Contracts\CartRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentCartRepository implements CartRepositoryInterface
{
    public function all(): Collection
    {
        return Cart::with('items.instrument')->get();
    }

    public function find(int $id): Cart
    {
        return Cart::with('items.instrument')->findOrFail($id);
    }

    public function create(array $data): Cart
    {
        return Cart::create($data);
    }

    public function update(int $id, array $data): Cart
    {
        $record = $this->find($id);
        $record->update($data);
        return $record;
    }

    public function delete(int $id): bool
    {
        $record = $this->find($id);
        return $record->delete();
    }

    public function getOrCreateForUser(int $userId): Cart
    {
        return Cart::with('items.instrument')->firstOrCreate(['user_id' => $userId]);
    }

    public function addItem(int $cartId, int $instrumentId, int $quantity)
    {
        $item = CartItem::where('cart_id', $cartId)
                        ->where('instrument_id', $instrumentId)
                        ->first();

        if ($item) {
            $item->quantity += $quantity;
            $item->save();
            return $item;
        }

        return CartItem::create([
            'cart_id'       => $cartId,
            'instrument_id' => $instrumentId,
            'quantity'      => $quantity
        ]);
    }

    public function updateItem(int $itemId, int $quantity): bool
    {
        $item = CartItem::findOrFail($itemId);
        return $item->update(['quantity' => $quantity]);
    }

    public function removeItem(int $itemId): bool
    {
        $item = CartItem::findOrFail($itemId);
        return $item->delete();
    }
}