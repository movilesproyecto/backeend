<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ExampleController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\UserController;

// Public auth endpoints
Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);

// Public example endpoints
Route::get('/ping', [ExampleController::class, 'ping']);
Route::get('/example', [ExampleController::class, 'index']);

// Departments (list & show public)
Route::get('departments', [DepartmentController::class, 'index']);
Route::get('departments/{department}', [DepartmentController::class, 'show']);
// Public reviews listing for a department
Route::get('departments/{department}/reviews', [ReviewController::class, 'index']);
// Public images listing (without auth)
Route::get('departments/{department}/images', [ImageController::class, 'index']);
Route::get('departments/{department}/images/primary', [ImageController::class, 'primary']);

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/me', [AuthController::class, 'me']);
    Route::put('auth/profile', [AuthController::class, 'updateProfile']);
    Route::post('auth/profile', [AuthController::class, 'updateProfile']);

    Route::post('departments', [DepartmentController::class, 'store']);
    Route::put('departments/{department}', [DepartmentController::class, 'update']);
    Route::delete('departments/{department}', [DepartmentController::class, 'destroy']);
    // favorites
    Route::post('departments/{department}/favorite', [DepartmentController::class, 'favorite']);
    Route::delete('departments/{department}/favorite', [DepartmentController::class, 'unfavorite']);
    // reviews / rating
    Route::post('departments/{department}/rate', [DepartmentController::class, 'rate']);
    // create review (authenticated)
    Route::post('departments/{department}/reviews', [ReviewController::class, 'store']);

    // Image management (authenticated)
    Route::post('departments/{department}/images', [ImageController::class, 'store']);
    Route::post('departments/{department}/upload-image-binary', [DepartmentController::class, 'uploadImageBinary']);
    Route::get('departments/{department}/images/{image}', [ImageController::class, 'show']);
    Route::put('departments/{department}/images/{image}', [ImageController::class, 'update']);
    Route::delete('departments/{department}/images/{image}', [ImageController::class, 'destroy']);

    // User management (admin/superadmin)
    Route::post('users', [UserController::class, 'store']);
    Route::get('users', [UserController::class, 'index']);
    Route::put('users/{user}/role', [UserController::class, 'updateRole']);
    Route::delete('users/{user}', [UserController::class, 'destroy']);

    // Reservations
    Route::get('reservations/available-slots', [ReservationController::class, 'availableSlots']);
    Route::post('reservations', [ReservationController::class, 'store']);
    Route::get('reservations', [ReservationController::class, 'index']);
    Route::get('reservations/{reservation}', [ReservationController::class, 'show']);
    Route::patch('reservations/{reservation}/status', [ReservationController::class, 'updateStatus']);
    Route::delete('reservations/{reservation}', [ReservationController::class, 'destroy']);

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::get('notifications/{notification}', [NotificationController::class, 'show']);
    Route::put('notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::delete('notifications/{notification}', [NotificationController::class, 'destroy']);
    Route::delete('notifications', [NotificationController::class, 'deleteAll']);
});
