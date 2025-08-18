<?php

namespace App\Http\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 * version="1.0.0",
 * title="Tummy Time API",
 * description="API documentation for the Tummy Time project",
 * @OA\Contact(
 * email="support@example.com"
 * )
 * )
 *
 * @OA\SecurityScheme(
 * securityScheme="sanctum",
 * type="http",
 * scheme="bearer"
 * )
 *
 * @OA\Tag(
 * name="Users",
 * description="API Endpoints for User Management"
 * )
 *
 * @OA\Tag(
 * name="Category",
 * description="API Endpoints for Category Management"
 * )
 *
 * @OA\Tag(
 * name="Food",
 * description="API Endpoints for Food Management"
 * )
 *
 * @OA\Tag(
 * name="Cart",
 * description="API Endpoints for Cart Management"
 * )
 *
 * @OA\Tag(
 * name="Orders",
 * description="API Endpoints for Orders Management"
 * )
 * 
 * @OA\Tag(
 * name="Payments",
 * description="API Endpoints for Payment Management"
 * )
 * 
 * @OA\Tag(
 * name="Feedback",
 * description="API Endpoints for Feedback Management"
 * )
 *
 * @OA\Schema(
 * schema="ErrorResponse",
 * title="Error Response",
 * description="Standard error response format for generic errors (e.g., 401, 403, 404, 500)",
 * @OA\Property(property="errors", type="string", description="Error message"),
 * example={"errors": "Something went wrong."}
 * )
 *
 * @OA\Schema(
 * schema="SuccessResponse",
 * title="Success Response",
 * description="Standard success response format for updates and deletes",
 * @OA\Property(property="success", type="string", description="Success message"),
 * example={"success": "Operation successful."}
 * )
 *
 * @OA\Schema(
 * schema="User",
 * title="User",
 * description="User model",
 * @OA\Property(property="id", type="integer", format="int64", description="User ID"),
 * @OA\Property(property="first_name", type="string", description="User's first name"),
 * @OA\Property(property="last_name", type="string", description="User's last name"),
 * @OA\Property(property="email", type="string", format="email", description="User's email address"),
 * @OA\Property(property="username", type="string", description="User's unique username"),
 * @OA\Property(property="is_staff", type="boolean", description="Indicates if the user has admin privileges"),
 * @OA\Property(property="is_active", type="boolean", description="Indicates if the user account is active"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp of user creation"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Timestamp of last update"),
 * example={
 * "id": 1, "first_name": "John", "last_name": "Doe", "email": "john.doe@example.com",
 * "username": "johndoe", "is_staff": false, "is_active": true,
 * "created_at": "2023-01-01T12:00:00.000000Z", "updated_at": "2023-01-01T12:00:00.000000Z"
 * }
 * )
 *
 * @OA\Schema(
 * schema="UserPagination",
 * title="User Pagination",
 * description="Paginated list of users",
 * @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/User")),
 * @OA\Property(property="links", type="object", description="Pagination links"),
 * @OA\Property(property="meta", type="object", description="Pagination meta information")
 * )
 *
 * @OA\Schema(
 * schema="Category",
 * title="Category",
 * description="Category model",
 * @OA\Property(property="id", type="integer", format="int64", description="Category ID"),
 * @OA\Property(property="name", type="string", description="Category name"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp of category creation"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Timestamp of last update"),
 * example={
 * "id": 1,
 * "name": "Beverages",
 * "created_at": "2023-01-01T12:00:00.000000Z",
 * "updated_at": "2023-01-01T12:00:00.000000Z"
 * }
 * )
 *
 * @OA\Schema(
 * schema="CategoryPagination",
 * title="Category Pagination",
 * description="Paginated list of categories",
 * @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Category")),
 * @OA\Property(property="links", type="object", description="Pagination links"),
 * @OA\Property(property="meta", type="object", description="Pagination meta information")
 * )
 *
 * @OA\Schema(
 * schema="Food",
 * title="Food",
 * description="Food item model",
 * @OA\Property(property="id", type="integer", format="int64", description="Food ID"),
 * @OA\Property(property="category_id", type="integer", format="int64", description="ID of the associated category"),
 * @OA\Property(property="name", type="string", description="Name of the food item"),
 * @OA\Property(property="description", type="string", nullable=true, description="Description of the food item"),
 * @OA\Property(property="price", type="number", format="float", description="Price of the food item"),
 * @OA\Property(property="is_available", type="boolean", description="Availability status of the food item"),
 * @OA\Property(property="stock", type="integer", description="Current stock quantity"),
 * @OA\Property(property="sold", type="integer", description="Number of units sold"),
 * @OA\Property(property="image_url", type="string", nullable=true, description="URL of the food item's image"),
 * example={
 * "id": 1,
 * "category_id": 2,
 * "name": "Burger",
 * "description": "A delicious beef patty with lettuce and tomatoes.",
 * "price": 10.50,
 * "is_available": true,
 * "stock": 50,
 * "sold": 20,
 * "image_url": "http://localhost:8000/storage/food_images/burger.jpg"
 * }
 * )
 *
 * @OA\Schema(
 * schema="FoodWithCount",
 * title="FoodWithCount",
 * description="Food model with a count of related order items",
 * allOf={
 * @OA\Schema(ref="#/components/schemas/Food"),
 * @OA\Schema(
 * @OA\Property(
 * property="order_items_count",
 * type="integer",
 * description="The number of times this food item has been ordered."
 * )
 * )
 * }
 * )
 *
 * @OA\Schema(
 * schema="FoodPagination",
 * title="Food Pagination",
 * description="Paginated list of food items",
 * @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Food")),
 * @OA\Property(property="links", type="object", description="Pagination links"),
 * @OA\Property(property="meta", type="object", description="Pagination meta information")
 * )
 *
 * @OA\Schema(
 * schema="FoodWithCountPagination",
 * title="Food with Count Pagination",
 * description="Paginated list of food items including their order count",
 * @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/FoodWithCount")),
 * @OA\Property(property="links", type="object", description="Pagination links"),
 * @OA\Property(property="meta", type="object", description="Pagination meta information")
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
 * "items": {
 * {"food_id": 1, "quantity": 2},
 * {"food_id": 2, "quantity": 1}
 * }
 * }
 * )
 *
 * @OA\Schema(
 * schema="Order",
 * title="Order",
 * description="Order model",
 * @OA\Property(property="id", type="integer", format="int64", description="Order ID"),
 * @OA\Property(property="user_id", type="integer", format="int64", description="ID of the user who placed the order"),
 * @OA\Property(property="order_date", type="string", format="date-time", description="Date and time of the order"),
 * @OA\Property(property="total_amount", type="number", format="float", description="Total amount of the order"),
 * @OA\Property(
 * property="order_status",
 * type="string",
 * enum={"pending", "preparing", "ready", "picked", "delivered", "cancelled"},
 * default="pending",
 * description="Current status of the order"
 * ),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp of order creation"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Timestamp of last update"),
 * example={
 * "id": 1,
 * "user_id": 10,
 * "order_date": "2023-01-01T12:00:00.000000Z",
 * "total_amount": 25.75,
 * "order_status": "pending",
 * "created_at": "2023-01-01T12:00:00.000000Z",
 * "updated_at": "2023-01-01T12:00:00.000000Z"
 * }
 * )
 *
 * @OA\Schema(
 * schema="OrderItem",
 * title="OrderItem",
 * description="Order item model",
 * @OA\Property(property="id", type="integer", format="int64", description="Order item ID"),
 * @OA\Property(property="order_id", type="integer", format="int64", description="ID of the associated order"),
 * @OA\Property(property="food_id", type="integer", format="int64", description="ID of the associated food item"),
 * @OA\Property(property="quantity", type="integer", description="Quantity of the food item"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp of creation"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Timestamp of last update"),
 * example={
 * "id": 1,
 * "order_id": 1,
 * "food_id": 101,
 * "quantity": 2,
 * "created_at": "2023-01-01T12:00:00.000000Z",
 * "updated_at": "2023-01-01T12:00:00.000000Z"
 * }
 * )
 *
 * @OA\Schema(
 * schema="OrderWithItems",
 * title="OrderWithItems",
 * description="Order model with associated order items",
 * allOf={
 * @OA\Schema(ref="#/components/schemas/Order"),
 * @OA\Schema(
 * @OA\Property(
 * property="order_items",
 * type="array",
 * @OA\Items(ref="#/components/schemas/OrderItem")
 * )
 * )
 * },
 * example={
 * "id": 1,
 * "user_id": 10,
 * "order_date": "2023-01-01T12:00:00.000000Z",
 * "total_amount": 25.75,
 * "order_status": "pending",
 * "created_at": "2023-01-01T12:00:00.000000Z",
 * "updated_at": "2023-01-01T12:00:00.000000Z",
 * "order_items": {
 * {
 * "id": 1,
 * "order_id": 1,
 * "food_id": 101,
 * "quantity": 2,
 * "created_at": "2023-01-01T12:00:00.000000Z",
 * "updated_at": "2023-01-01T12:00:00.000000Z"
 * }
 * }
 * }
 * )
 *
 * @OA\Schema(
 * schema="NewOrder",
 * title="NewOrder",
 * description="New order model",
 * @OA\Property(property="id", type="integer", format="int64", description="New order ID"),
 * @OA\Property(property="order_id", type="integer", format="int64", description="ID of the associated order"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp of creation"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Timestamp of last update"),
 * @OA\Property(
 * property="order",
 * ref="#/components/schemas/Order"
 * ),
 * example={
 * "id": 1,
 * "order_id": 1,
 * "created_at": "2023-01-01T12:00:00.000000Z",
 * "updated_at": "2023-01-01T12:00:00.000000Z",
 * "order": {
 * "id": 1,
 * "user_id": 10,
 * "order_date": "2023-01-01T12:00:00.000000Z",
 * "total_amount": 25.75,
 * "order_status": "pending"
 * }
 * }
 * )
 *
 * @OA\Schema(
 * schema="OrderPagination",
 * title="Order Pagination",
 * description="Paginated list of orders",
 * @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Order")),
 * @OA\Property(property="links", type="object", description="Pagination links"),
 * @OA\Property(property="meta", type="object", description="Pagination meta information")
 * )
 *
 * @OA\Schema(
 * schema="NewOrderPagination",
 * title="New Order Pagination",
 * description="Paginated list of new orders",
 * @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/NewOrder")),
 * @OA\Property(property="links", type="object", description="Pagination links"),
 * @OA\Property(property="meta", type="object", description="Pagination meta information")
 * )
 *
 * @OA\Schema(
 * schema="Payment",
 * title="Payment",
 * description="Payment model",
 * @OA\Property(property="id", type="integer", format="int64", description="Payment ID"),
 * @OA\Property(property="user_id", type="integer", format="int64", description="ID of the user who made the payment"),
 * @OA\Property(property="order_id", type="integer", format="int64", description="ID of the associated order"),
 * @OA\Property(property="payment_type", type="string", enum={"cash", "card"}, description="Type of payment"),
 * @OA\Property(property="payment_date", type="string", format="date-time", description="Date and time of the payment"),
 * example={
 * "id": 1,
 * "user_id": 1,
 * "order_id": 10,
 * "payment_type": "cash",
 * "payment_date": "2023-01-01T13:00:00.000000Z"
 * }
 * )
 *
 * @OA\Schema(
 * schema="PaymentPagination",
 * title="Payment Pagination",
 * description="Paginated list of payments",
 * @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Payment")),
 * @OA\Property(property="links", type="object", description="Pagination links"),
 * @OA\Property(property="meta", type="object", description="Pagination meta information")
 * )
 *
 * @OA\Schema(
 * schema="RegisterPaymentRequest",
 * title="RegisterPaymentRequest",
 * description="Request body for creating a new payment",
 * required={"order_id", "payment_type"},
 * @OA\Property(property="order_id", type="integer", description="ID of the order to be paid"),
 * @OA\Property(property="payment_type", type="string", enum={"cash", "card"}, description="Type of payment"),
 * example={
 * "order_id": 1,
 * "payment_type": "cash"
 * }
 * )
 * 
 * @OA\Schema(
 * schema="Feedback",
 * title="Feedback",
 * description="Feedback model",
 * @OA\Property(property="id", type="integer", format="int64", description="Feedback ID"),
 * @OA\Property(property="user_id", type="integer", format="int64", description="ID of the user who provided feedback"),
 * @OA\Property(property="food_id", type="integer", format="int64", description="ID of the associated food item"),
 * @OA\Property(property="comment", type="string", description="The feedback comment"),
 * @OA\Property(property="feedback_date", type="string", format="date-time", description="Timestamp of the feedback"),
 * example={
 * "id": 1,
 * "user_id": 10,
 * "food_id": 20,
 * "comment": "This food was excellent, highly recommend!",
 * "feedback_date": "2023-01-01T14:30:00.000000Z"
 * }
 * )
 *
 * @OA\Schema(
 * schema="FeedbackList",
 * title="FeedbackList",
 * description="List of feedback items",
 * @OA\Property(
 * property="feedbacks",
 * type="array",
 * @OA\Items(ref="#/components/schemas/Feedback")
 * )
 * )
 *
 * @OA\Schema(
 * schema="RegisterFeedbackRequest",
 * title="RegisterFeedbackRequest",
 * description="Request body for creating a new feedback entry",
 * required={"food_id", "comment"},
 * @OA\Property(property="food_id", type="integer", description="ID of the food item being commented on"),
 * @OA\Property(property="comment", type="string", description="The feedback comment"),
 * example={
 * "food_id": 1,
 * "comment": "This food was excellent, highly recommend!"
 * }
 * )
 *
 * @OA\Schema(
 * schema="UpdateFeedbackRequest",
 * title="UpdateFeedbackRequest",
 * description="Request body for updating a feedback entry",
 * required={"comment"},
 * @OA\Property(property="comment", type="string", description="The updated feedback comment"),
 * example={
 * "comment": "The updated comment."
 * }
 * )
 * 
 */
class Annotations
{
    // This class is just a container for the annotations. No actual code needed here.
}
