<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ExampleController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\ReviewController;

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

// Protected department routes (create/update/delete)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/me', [AuthController::class, 'me']);

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
    // upload images
    Route::post('departments/{department}/images', [DepartmentController::class, 'uploadImages']);

    // Reservations
    Route::get('reservations/available-slots', [ReservationController::class, 'availableSlots']);
    Route::post('reservations', [ReservationController::class, 'store']);
    Route::get('reservations', [ReservationController::class, 'index']);
    Route::get('reservations/{reservation}', [ReservationController::class, 'show']);
    Route::delete('reservations/{reservation}', [ReservationController::class, 'destroy']);
});
