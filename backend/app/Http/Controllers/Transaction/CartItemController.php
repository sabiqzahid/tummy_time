<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Http\Requests\Transaction\UpdateCartItemRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 * name="Cart",
 * description="API Endpoints for Cart Management"
 * )
 *
 * @OA\Schema(
 * schema="CartItem",
 * title="CartItem",
 * description="Cart item model",
 * @OA\Property(property="id", type="integer", format="int64", description="Cart item ID"),
 * @OA\Property(property="cart_id", type="integer", format="int64", description="ID of the associated cart"),
 * @OA\Property(property="food_id", type="integer", format="int64", description="ID of the associated food item"),
 * @OA\Property(property="quantity", type="integer", description="Quantity of the food item"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp of creation"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Timestamp of last update"),
 * example={
 * "id": 1,
 * "cart_id": 1,
 * "food_id": 101,
 * "quantity": 2,
 * "created_at": "2023-01-01T12:00:00.000000Z",
 * "updated_at": "2023-01-01T12:00:00.000000Z"
 * }
 * )
 *
 * @OA\Schema(
 * schema="CartItemList",
 * title="CartItemList",
 * description="List of cart items",
 * @OA\Property(
 * property="cartItems",
 * type="array",
 * @OA\Items(ref="#/components/schemas/CartItem")
 * )
 * )
 *
 * @OA\Schema(
 * schema="UpdateCartItemRequest",
 * title="UpdateCartItemRequest",
 * description="Request body for updating cart items",
 * required={"cart_id", "items"},
 * @OA\Property(property="cart_id", type="integer", description="ID of the cart to update"),
 * @OA\Property(
 * property="items",
 * type="array",
 * @OA\Items(
 * @OA\Property(property="food_id", type="integer", description="ID of the food item"),
 * @OA\Property(property="quantity", type="integer", description="Quantity of the food item")
 * ),
 * description="List of new cart items"
 * ),
 * example={
 * "cart_id": 1,
 * "items": {
 * {"food_id": 101, "quantity": 2},
 * {"food_id": 205, "quantity": 1}
 * }
 * }
 * )
 */
class CartItemController extends Controller
{
    public function __construct() {
        $this->middleware('auth:sanctum');
    }

    /**
     * @OA\Get(
     * path="/api/cart-items/{user_id}",
     * operationId="getCartItemsByUserId",
     * tags={"Cart"},
     * summary="Get all cart items for a specific user",
     * description="Retrieves all cart items for a user by their ID. Requires the authenticated user to be the owner of the cart.",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="user_id",
     * in="path",
     * required=true,
     * @OA\Schema(type="integer"),
     * description="ID of the user whose cart items to retrieve"
     * ),
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(ref="#/components/schemas/CartItemList")
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden: You are not authorized to access this cart.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=404,
     * description="Not Found: Cart not found.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal Server Error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function show(string $user_id): JsonResponse
    {
        if ($user_id != Auth::id()) {
            return response()->json([
                'error' => 'You are not authorized to access this cart.',
            ], 403);
        }

        try {
            $cart = Cart::where('user_id', $user_id)->first();

            if (!$cart) {
                return response()->json([
                    'error' => 'Cart not found.',
                ], 404);
            }

            $cartItems = CartItem::where('cart_id', $cart->id)->get();
            
            return response()->json([
                'cartItems' => $cartItems,
            ], 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Patch(
     * path="/api/cart-items/{cart_id}",
     * operationId="updateCartItems",
     * tags={"Cart"},
     * summary="Update a user's cart items",
     * description="Replaces all existing cart items with a new list. This acts as a full cart replacement. Requires the authenticated user to be the cart owner.",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="cart_id",
     * in="path",
     * required=true,
     * @OA\Schema(type="integer"),
     * description="ID of the cart to update"
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(ref="#/components/schemas/UpdateCartItemRequest")
     * ),
     * @OA\Response(
     * response=200,
     * description="Cart updated successfully",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="string", example="Cart updated successfully.")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden: You are not authorized to update this cart.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=422,
     * description="Validation Error: Invalid input.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal Server Error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function update(UpdateCartItemRequest $request, string $cart_id): JsonResponse
    {
        $validated = $request->validated();
        $cartId = (int) $cart_id;

        $cart = Cart::find($cartId);

        if (!$cart || $cart->user_id !== Auth::id()) {
            return response()->json([
                'error' => 'You are not authorized to update this cart.',
            ], 403);
        }
        
        try {
            CartItem::where('cart_id', $cartId)->delete();

            $newCartItems = [];
            foreach ($validated['items'] as $item) {
                $newCartItems[] = [
                    'cart_id' => $cartId,
                    'food_id' => $item['food_id'],
                    'quantity' => $item['quantity'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            CartItem::insert($newCartItems);

            return response()->json([
                'success' => 'Cart updated successfully.',
            ], 200);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     * path="/api/cart-items/{cart_id}",
     * operationId="deleteCartItems",
     * tags={"Cart"},
     * summary="Delete all items from a user's cart",
     * description="Removes all cart items from a specific cart by its ID. Requires the authenticated user to be the cart owner.",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="cart_id",
     * in="path",
     * required=true,
     * @OA\Schema(type="integer"),
     * description="ID of the cart to clear"
     * ),
     * @OA\Response(
     * response=200,
     * description="Cart items deleted successfully",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="string", example="Successfully deleted 3 cart item(s).")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden: You are not authorized to delete this cart.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=404,
     * description="Not Found: Cart not found.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal Server Error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function destroy(string $cart_id): JsonResponse
    {
        $cartId = (int) $cart_id;

        $cart = Cart::find($cartId);

        if (!$cart || $cart->user_id !== Auth::id()) {
            return response()->json([
                'error' => 'You are not authorized to delete this cart.',
            ], 403);
        }

        try {
            $deletedCount = CartItem::where('cart_id', $cartId)->delete();
            
            return response()->json([
                'success' => "Successfully deleted $deletedCount cart item(s).",
            ], 200);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}