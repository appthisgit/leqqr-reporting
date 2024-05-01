<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReceiptController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders/{order}/{format}', [OrderController::class, 'show']);
Route::get('/receipts/{receipt}', ReceiptController::class);
