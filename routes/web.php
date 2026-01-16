<?php

use App\Http\Controllers\CalendarSubscriptionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('calendar.index');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/calendar/subscription/{token}', [CalendarSubscriptionController::class, 'feed'])
    ->name('calendar.subscription.feed');

require __DIR__.'/settings.php';
