<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DrawController;
use App\Http\Controllers\Admin\PrizeController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\ParticipantController;
use Illuminate\Support\Facades\Route;

// Landing page (QR code peserta diarahkan ke sini)
Route::view('/', 'auth.login')->name('login');
Route::view('/registrasi-ditutup', 'auth.closed')->name('registration.closed');

// Google OAuth
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');

Route::post('/logout', function () {
    auth()->logout();
    return redirect()->route('login');
})->middleware('auth')->name('logout');

// Peserta
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [ParticipantController::class, 'dashboard'])->name('participant.dashboard');
});

// Panitia / Operator
Route::middleware(['auth', 'can.manage.draw'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/registration/toggle', [DashboardController::class, 'toggleRegistration'])->name('registration.toggle');

    Route::get('/prizes', [PrizeController::class, 'index'])->name('prizes.index');
    Route::post('/prizes', [PrizeController::class, 'store'])->name('prizes.store');
    Route::put('/prizes/{prize}', [PrizeController::class, 'update'])->name('prizes.update');
    Route::delete('/prizes/{prize}', [PrizeController::class, 'destroy'])->name('prizes.destroy');

    Route::get('/draw', [DrawController::class, 'index'])->name('draw.index');
    Route::post('/draw', [DrawController::class, 'draw'])->name('draw.execute');
    Route::post('/draw/{winner}/redo', [DrawController::class, 'redo'])->name('draw.redo');
});
