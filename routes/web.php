<?php

use App\Http\Controllers\PublicMenuController;
use App\Http\Controllers\TelegramWebhookController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/create-storage-link', function () {
    Artisan::call('storage:link');
    return 'Storage link created successfully!';
});

Route::get('/', [PublicMenuController::class, 'index'])->name('menu.index');
Route::post('/orders', [PublicMenuController::class, 'store'])->name('menu.order.store');
Route::post('/telegram/webhook', [TelegramWebhookController::class, 'webhook'])->name('telegram.webhook');
Route::get('/telegram/customer-prefill', [TelegramWebhookController::class, 'customerPrefill'])
    ->name('telegram.customer-prefill');
