<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\LocationController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {
    Route::resource('events', EventController::class)->except('show');
    Route::get('/my-events', [EventController::class, 'myEvents'])->name('my-events');

    Route::post('/events/{event}/book', [BookingController::class, 'store'])->name('bookings.store');
    Route::delete('/bookings/{event}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');

    Route::get('/events/{event}/attendees', [EventController::class, 'attendees'])->name('events.attendees')->middleware('can:view,event');

    Route::get('/my-bookings', [BookingController::class, 'myBookings'])->name('my-bookings');

    Route::post('/events/{event}/images/upload', [EventController::class, 'uploadImage'])->name('events.images.upload');
    Route::delete('/events/{event}/images/delete',
        [EventController::class, 'deleteImage'])->name('events.images.delete');
});

Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
Route::get('/events', [EventController::class, 'index'])->name('events.index');

Route::post('/store-location', [LocationController::class, 'store'])->name('location.store');
Route::get('/location-test', [LocationController::class, 'test']);




