<?php

use App\Http\Controllers\FrontendController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Auth (manual)
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Halaman publik
Route::get('/', [FrontendController::class, 'index'])->name('home');

// Booking (wajib login)
Route::middleware('auth')->group(function () {
    Route::get('/film/{id}', [FrontendController::class, 'showFilm'])->name('film.show');
    Route::get('/order/{showtime}', [FrontendController::class, 'order'])->name('order');
    Route::post('/order/store', [FrontendController::class, 'storeOrder'])->name('storeOrder');
});
