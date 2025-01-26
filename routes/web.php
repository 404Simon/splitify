<?php

use App\Http\Controllers\GroupController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SharedDebtController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('groups/{group}')->name('groups.')->group(function () {
        Route::get('/', [GroupController::class, 'show'])->name('show');  // Keep the group show route here if it was removed
        Route::get('/debts/create', [SharedDebtController::class, 'create'])->name('sharedDebts.create');
        Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
    });

    Route::resource('groups', GroupController::class)->except(['show']);  // Exclude 'show' from top-level resource if you moved it above

    Route::resource('sharedDebts', SharedDebtController::class)
        ->only(['store', 'destroy']);  // Keep only store and destroy for sharedDebts resource

    Route::resource('transactions', TransactionController::class)
        ->only(['store', 'destroy']);  // Keep only store and destroy for transactions resource
});

require __DIR__ . '/auth.php';
