<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;

// ======================
// Auth (manual)
// ======================
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ======================
// Public (tanpa login)
// ======================
Route::get('/', [FrontendController::class, 'profile'])->name('profile');
Route::get('/index', [FrontendController::class, 'index'])->name('home');
Route::get('/film/{id}', [FrontendController::class, 'showFilm'])->name('film.show');
Route::get('/ticket/{hash}', [OrderController::class, 'viewTicket'])
    ->name('ticket.view');


// ======================
// Booking (wajib login)
// ======================
Route::middleware('auth')->group(function () {
    // form pemesanan
    Route::get('/order/{showtimeId}', [OrderController::class, 'show'])->name('order.show');

    // simpan pemesanan
    Route::post('/order/store', [OrderController::class, 'store'])->name('order.store');

    // halaman pesanan saya
    Route::get('/my-orders', [OrderController::class, 'myOrders'])->name('my-orders');
    // Rute Untuk cancel Pesanan
    Route::post('/order/{orderId}/cancel', [OrderController::class, 'cancelOrder'])->name('order.cancel');
});
