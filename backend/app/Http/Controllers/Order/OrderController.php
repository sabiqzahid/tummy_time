<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\NewOrder;
use App\Models\Cart;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    
    /**
     * @OA\Get(
     * path="/api/orders",
     * summary="Get a list of all orders for the current user or all orders for staff/admin",
     * tags={"Orders"},
     * security={{"sanctum": {}}},
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(ref="#/components/schemas/OrderPagination")
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal server error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function index()
    {
        try {
            $orders = Auth::user()->isStaff() || Auth::user()->isSuperAdmin() ? 
                        Order::paginate(10) : 
                        Order::where('user_id', Auth::user()->id)->paginate(10);

            return response()->json($orders, 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                "errors" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     * path="/api/orders/new",
     * summary="Get a list of new orders (Staff/Admin only)",
     * tags={"Orders"},
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="order_status",
     * in="query",
     * description="Filter new orders by status (e.g., pending)",
     * @OA\Schema(type="string")
     * ),
     * @OA\Parameter(
     * name="order_id",
     * in="query",
     * description="Search for a specific order by ID",
     * @OA\Schema(type="string")
     * ),
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(ref="#/components/schemas/NewOrderPagination")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal server error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function indexNewOrders(Request $request)
    {
        if (!Auth::user()->isSuperAdmin() && !Auth::user()->isStaff()) {
            return response()->json([
                "errors" => "You are not authorized to see new orders."
            ], 403);
        }
        
        try {
            $query = NewOrder::with('order');
            
            if ($request->has('order_status')) {
                $status = $request->input('order_status');
                $query->whereHas('order', function ($q) use ($status) {
                    $q->where('order_status', $status);
                });
            }

            if ($request->has('order_id')) {
                $orderId = $request->input('order_id');
                $query->where('order_id', $orderId);
            }
            
            $newOrders = $query->paginate(10);
            
            return response()->json($newOrders, 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                "errors" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     * path="/api/orders/{order}",
     * summary="Get details for a specific order",
     * tags={"Orders"},
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="order",
     * in="path",
     * required=true,
     * description="ID of the order to retrieve",
     * @OA\Schema(type="string")
     * ),
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(ref="#/components/schemas/OrderWithItems")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=404,
     * description="Order not found",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal server error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function show(string $order)
    {
        try {
            $order = Order::with([
                'orderItems.food' => function ($query) {
                    $query->select('id', 'name');
                },
                'user' => function ($query) {
                    $query->select('id', 'username');
                }
            ])->find($order);

            if (!$order) {
                return response()->json([
                    "errors" => "Order not found."
                ], 404);
            }

            if ($order->user_id !== Auth::user()->id && !Auth::user()->isSuperAdmin() && !Auth::user()->isStaff()) {
                return response()->json([
                    "errors" => "You are not authorized to see this order."
                ], 403);
            }

            return response()->json($order, 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                "errors" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     * path="/api/orders",
     * summary="Create a new order from the user's cart",
     * tags={"Orders"},
     * security={{"sanctum": {}}},
     * @OA\Response(
     * response=201,
     * description="Order created successfully",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Order created successfully.")
     * )
     * ),
     * @OA\Response(
     * response=400,
     * description="Bad request",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=404,
     * description="Cart not found",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal server error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function create(Request $request)
    {
        try {
            DB::transaction(function () {
                $user = Auth::user();

                $cart = Cart::where('user_id', $user->id)
                            ->with('cartItems.food')
                            ->first();

                if (!$cart) {
                    throw new \Exception('Cart not found.');
                }

                if ($cart->cartItems->isEmpty()) {
                    throw new \Exception('Cart item is empty.');
                }

                $totalAmount = 0;
                foreach ($cart->cartItems as $cartItem) {
                    // Checking if food exists and has a price, to prevent errors
                    if ($cartItem->food) {
                        $totalAmount += $cartItem->quantity * $cartItem->food->price;
                    }
                }

                $order = Order::create([
                    'user_id' => $user->id,
                    'order_date' => now(),
                    'total_amount' => $totalAmount,
                ]);

                foreach ($cart->cartItems as $cartItem) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'food_id' => $cartItem->food_id,
                        'quantity' => $cartItem->quantity,
                    ]);
                }

                NewOrder::create(['order_id' => $order->id]);

                $cart->cartItems()->delete();
            });

            return response()->json([
                'message' => 'Order created successfully.'
            ], 201);
        } catch (\Exception $e) {
            Log::error($e);

            if ($e->getMessage() === 'Cart not found.') {
                return response()->json([
                    "errors" => "Cart not found."
                ], 404);
            }

            if ($e->getMessage() === 'Cart item is empty.') {
                return response()->json([
                    "errors" => "Cart item is empty."
                ], 400);
            }

            return response()->json([
                "errors" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Patch(
     * path="/api/orders/{order}",
     * summary="Update the status of a specific order",
     * tags={"Orders"},
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="order",
     * in="path",
     * required=true,
     * description="ID of the order to update",
     * @OA\Schema(type="string")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"order_status"},
     * @OA\Property(
     * property="order_status",
     * type="string",
     * enum={"preparing", "ready", "picked", "delivered", "cancelled"},
     * example="ready"
     * )
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Order updated successfully",
     * @OA\JsonContent(ref="#/components/schemas/SuccessResponse")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=404,
     * description="Order not found",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal server error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function update(UpdateOrderRequest $request, string $order)
    {
        $validated = $request->validated();
        try {
            $order = Order::find($order);

            if (!$order) {
                return response()->json([
                    "errors" => "Order not found."
                ], 404);
            }

            if (
                (!Auth::user()->isSuperAdmin() && !Auth::user()->isStaff() &&
                $order->user_id !== Auth::user()->id &&
                $validated['order_status'] !== 'cancelled') ||
                ((Auth::user()->isSuperAdmin() || Auth::user()->isStaff()) &&
                $validated['order_status'] === 'cancelled')
            ) {
                return response()->json([
                    "errors" => "You are not authorized to set " . $validated['order_status'] . " status."
                ], 403);
            }

            if (
                $validated['order_status'] === 'delivered' ||
                $validated['order_status'] === 'cancelled'
            ) {
                if ($validated['order_status'] === 'delivered') {
                    $payment = Payment::where('order_id', $order->id)->first();

                    if (!$payment) {
                        return response()->json([
                            "errors" => "Payment not found."
                        ], 404);
                    }

                    $order->payment_status = 'paid';
                } else {
                    $order->payment_status = 'failed';
                }
                

                $newOrder = NewOrder::where('order_id', $order->id)->first();

                if ($newOrder) {
                    $newOrder->delete();
                }
            }

            $order->order_status = $validated['order_status'];
            $order->order_date = now();
            $order->save();

            return response()->json([
                'success' => 'Order updated successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                "errors" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     * path="/api/orders/{order}",
     * summary="Delete a specific order (owner only)",
     * tags={"Orders"},
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="order",
     * in="path",
     * required=true,
     * description="ID of the order to delete",
     * @OA\Schema(type="string")
     * ),
     * @OA\Response(
     * response=200,
     * description="Order deleted successfully",
     * @OA\JsonContent(ref="#/components/schemas/SuccessResponse")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=404,
     * description="Order not found",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal server error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function destroy(string $order)
    {
        try {
            $order = Order::find($order);

            if (!$order) {
                return response()->json([
                    "errors" => "Order not found."
                ], 404);
            }

            if (!Auth::user()->isSuperAdmin()) {
                return response()->json([
                    "errors" => "You are not authorized to delete this order."
                ], 403);
            }

            $order->delete();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                "errors" => $e->getMessage()
            ], 500);
        }
    }
}
