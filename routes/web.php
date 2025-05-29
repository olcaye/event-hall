<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\DeveloperController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\LocationController;
use App\Http\Middleware\OnlyDeveloperEnv;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {
    Route::resource('events', EventController::class)->except('show');
    Route::get('/my-events', [EventController::class, 'myEvents'])->name('my-events');

    Route::post('/events/{event}/book', [BookingController::class, 'store'])->name('bookings.store');
    Route::delete('/bookings/{event}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');

    Route::get('/events/{event}/attendees',
        [EventController::class, 'attendees'])->name('events.attendees')->middleware('can:view,event');

    Route::get('/my-bookings', [BookingController::class, 'myBookings'])->name('my-bookings');

    Route::post('/events/{event}/images/upload', [EventController::class, 'uploadImage'])->name('events.images.upload');
    Route::delete('/events/{event}/images/delete',
        [EventController::class, 'deleteImage'])->name('events.images.delete');
});

Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
Route::get('/events', [EventController::class, 'index'])->name('events.index');

Route::post('/store-location', [LocationController::class, 'store'])->name('location.store');
Route::get('/location-test', [LocationController::class, 'test']);


Route::middleware(OnlyDeveloperEnv::class)
    ->prefix('developer')
    ->name('developer.')
    ->group(function () {
        Route::get('/panel', [DeveloperController::class, 'index'])->name('panel');
        Route::post('/flush', [DeveloperController::class, 'flush'])->name('flush');
        Route::post('/flush-and-seed', [DeveloperController::class, 'flushAndSeed'])->name('flush_and_seed');
        Route::post('/seed', [DeveloperController::class, 'seed'])->name('seed');
        Route::post('/session-flush', [DeveloperController::class, 'clearSession'])->name('session.flush');
        Route::post('/login-as/{user}', [DeveloperController::class, 'loginAs'])->name('login_as');
        Route::post('/clear-cache', [DeveloperController::class, 'clearCache'])->name('clear_cache');


    });
