<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Canteen\CategoryController;
use App\Http\Controllers\Canteen\FoodController;
use App\Http\Controllers\Transaction\CartItemController;
use App\Http\Controllers\Order\OrderController;
use App\Http\Controllers\Order\PaymentController;
use App\Http\Controllers\Canteen\FeedbackController;


Route::post('/login', [AuthController::class, 'login'])
    ->name('api.login');

Route::post('/users', [UserController::class, 'createUser'])
->name('api.createUser');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('api.logout');

    Route::get('/users', [UserController::class, 'index'])
        ->name('api.getUsers');

    Route::get('/users/{user}', [UserController::class, 'show'])
        ->name('api.getUser');

    Route::post('/users/staff', [UserController::class, 'createStaffUser'])
        ->name('api.createStaffUser');

    Route::patch('/users/{user}', [UserController::class, 'update'])
        ->name('api.updateUser');

    Route::delete('/users/{user}', [UserController::class, 'destroy'])
        ->name('api.deleteUser');

    Route::get('/categories', [CategoryController::class, 'index'])
        ->name('api.getCategories');

    Route::get('/categories/{category}', [CategoryController::class, 'show'])
        ->name('api.getCategory');

    Route::post('/categories', [CategoryController::class, 'create'])
        ->name('api.createCategory');

    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])
        ->name('api.deleteCategory');

    Route::get('/foods', [FoodController::class, 'index'])
        ->name('api.getFoods');

    Route::get('/foods/most-sold', [FoodController::class, 'indexMostSold'])
        ->name('api.getFoodsMostSold');

    Route::get('/foods/{food}', [FoodController::class, 'show'])
        ->name('api.getFood');

    Route::post('/foods', [FoodController::class, 'create'])
        ->name('api.createFood');

    Route::patch('/foods/{food}', [FoodController::class, 'update'])
        ->name('api.updateFood');

    Route::delete('/foods/{food}', [FoodController::class, 'destroy'])
        ->name('api.deleteFood');

    Route::get('/cart-items/{user_id}', [CartItemController::class, 'show'])
        ->name('api.getCartItems');

    Route::put('/cart-items/{cart_id}', [CartItemController::class, 'update'])
        ->name('api.updateCart');

    Route::delete('/cart-items/{cart_id}', [CartItemController::class, 'destroy'])
        ->name('api.deleteFromCart');

    Route::get('/orders', [OrderController::class, 'index'])
        ->name('api.getOrders');

    Route::get('/orders/new', [OrderController::class, 'indexNewOrders'])
        ->name('api.getNewOrders');

    Route::get('/orders/{order}', [OrderController::class, 'show'])
        ->name('api.getOrder');

    Route::post('/orders', [OrderController::class, 'create'])
        ->name('api.createOrder');

    Route::patch('/orders/{order}', [OrderController::class, 'update'])
        ->name('api.updateOrder');

    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])
        ->name('api.deleteOrder');

    Route::get('/payments', [PaymentController::class, 'index'])
        ->name('api.getPayments');

    Route::get('/payments/{payment}', [PaymentController::class, 'show'])
        ->name('api.getPayment');

    Route::post('/payments', [PaymentController::class, 'create'])
        ->name('api.createPayment');

    Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])
        ->name('api.deletePayment');

    Route::get('/feedbacks/{food_id}', [FeedbackController::class, 'index'])
        ->name('api.getFeedbacks');

    Route::post('/feedbacks', [FeedbackController::class, 'create'])
        ->name('api.createFeedback');

    Route::patch('/feedbacks/{feedback}', [FeedbackController::class, 'update'])
        ->name('api.updateFeedback');

    Route::delete('/feedbacks/{feedback}', [FeedbackController::class, 'destroy'])
        ->name('api.deleteFeedback');
    });

