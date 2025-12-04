<?php

declare(strict_types=1);

use App\Http\Controllers\GroupController;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecurringSharedDebtController;
use App\Http\Controllers\SharedDebtController;
use App\Http\Controllers\ShoppingListController;
use App\Http\Controllers\TransactionController;
use App\Http\Middleware\EnsureIsGroupAdmin;
use App\Http\Middleware\EnsureIsGroupMember;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('groups.index');
});

Route::get('invite/{uuid}', [InviteController::class, 'show'])->name('invites.show');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('groups', GroupController::class)->except(['show']);
    Route::post('invites/{invite}/accept', [InviteController::class, 'accept'])->name('invites.accept');
    Route::post('invites/{invite}/deny', [InviteController::class, 'deny'])->name('invites.deny');

    Route::prefix('groups/{group}')->name('groups.')->middleware(EnsureIsGroupMember::class)->group(function () {
        Route::get('/', [GroupController::class, 'show'])->name('show');
        Route::resource('invites', InviteController::class)->only(['index', 'create', 'destroy', 'store'])->middleware(EnsureIsGroupAdmin::class);
        Route::resource('sharedDebts', SharedDebtController::class);
        Route::resource('transactions', TransactionController::class);
        Route::resource('mapMarkers', MapController::class);

        Route::resource('recurring-debts', RecurringSharedDebtController::class);
        Route::patch('recurring-debts/{recurringDebt}/toggle-active', [RecurringSharedDebtController::class, 'toggleActive'])->name('recurring-debts.toggle-active');
        Route::post('recurring-debts/{recurringDebt}/generate-now', [RecurringSharedDebtController::class, 'generateNow'])->name('recurring-debts.generate-now');

        Route::resource('shoppingLists', ShoppingListController::class);
        Route::post('shoppingLists/{shoppingList}/items', [ShoppingListController::class, 'addItem'])->name('shoppingLists.items.store');
        Route::patch('shoppingLists/{shoppingList}/items/{item}/toggle', [ShoppingListController::class, 'toggleItem'])->name('shoppingLists.items.toggle');
        Route::delete('shoppingLists/{shoppingList}/items/{item}', [ShoppingListController::class, 'deleteItem'])->name('shoppingLists.items.destroy');

        // Route::post('/generate-invite', [GroupController::class, 'generateInvite'])->name('groups.generateInvite');
        Route::get('map', [MapController::class, 'displayMap'])->name('map.display');
    });
});

require __DIR__.'/auth.php';
