<?php

namespace App\Http\Controllers;

use App\Services\EventService;

class HomeController extends Controller
{

    public function index(EventService $eventService)
    {
        $userLat = session('user_latitude', config('location.default_latitude'));
        $userLon = session('user_longitude', config('location.default_longitude'));

        $nearbyEvents = $eventService->getNearbyEvents($userLat, $userLon);
        $popularEvents = $eventService->getPopularEvents();
        $events       = $eventService->getLatestEvents();

        $bookedEventIds = auth()->check() ? auth()->user()->bookings()->pluck('event_id')->toArray() : [];

        return view('home', compact('events', 'bookedEventIds', 'nearbyEvents','popularEvents'));
    }
}
