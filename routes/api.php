<?php

use App\Http\Controllers\Api\AuthController;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// http://ticketing.test/api
// universal resource locator
// tickets
// users


Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
