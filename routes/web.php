<?php

use App\Http\Controllers\GroupController;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SharedDebtController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('groups.index');
});

Route::resource('invite', InviteController::class);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('groups', GroupController::class)->except(['show']);

    Route::prefix('groups/{group}')->name('groups.')->group(function () {
        Route::get('/', [GroupController::class, 'show'])->name('show');
        Route::post('/generate-invite', [GroupController::class, 'generateInvite'])->name('groups.generateInvite');
        Route::get('/sharedDebts/create', [SharedDebtController::class, 'create'])->name('sharedDebts.create');
        Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
    });

    Route::resource('sharedDebts', SharedDebtController::class)
        ->only(['store', 'destroy']);

    Route::resource('transactions', TransactionController::class)
        ->only(['store', 'destroy']);
});

require __DIR__ . '/auth.php';
