<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Services\CartService;
use App\Http\Resources\CartResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class CartController extends Controller
{
    /**
     * @var CartService
     */
    protected $cartService;

    /**
     * CartController constructor.
     *
     * @param CartService $cartService
     */
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Fetch the authenticated user's cart.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $cart = $this->cartService->getCart($request->user()->id);
            return response()->json([
                'status' => true,
                'data'   => new CartResource($cart)
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Add a new item to the cart.
     *
     * @param AddToCartRequest $request
     * @return JsonResponse
     */
    public function store(AddToCartRequest $request): JsonResponse
    {
        try {
            $this->cartService->addItem($request->user()->id, $request->instrument_id, $request->quantity ?? 1);
            return response()->json([
                'status'  => true,
                'message' => 'Added to cart successfully.'
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the quantity of an existing cart item.
     *
     * @param UpdateCartItemRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateCartItemRequest $request, $id): JsonResponse
    {
        try {
            $this->cartService->updateItem($id, $request->quantity);
            return response()->json([
                'status'  => true,
                'message' => 'Cart item updated successfully.'
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove an item entirely from the cart.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $this->cartService->removeItem($id);
            return response()->json([
                'status'  => true,
                'message' => 'Cart item removed successfully.'
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
