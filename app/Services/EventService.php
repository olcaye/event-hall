<?php

namespace App\Services;

use App\Enums\EventStatus;
use App\Models\Event;
use Illuminate\Support\Facades\DB;

class EventService
{
    public function getNearbyEvents(float $lat, float $lon, int $limit = 8)
    {
        $distanceSql = DB::raw(sprintf(
            '( %d * acos( cos( radians(%f) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(%f) ) + sin( radians(%f) ) * sin( radians( latitude ) ) ) ) AS distance',
            6371,
            $lat,
            $lon,
            $lat
        ));

        return Event::query()
            ->select('*')
            ->selectRaw($distanceSql)
            ->where('status', EventStatus::ACTIVE)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('distance')
            ->take($limit)
            ->get();
    }

    public function getLatestEvents(int $limit = 8)
    {
        return Event::where('status', EventStatus::ACTIVE)
            ->latest()
            ->take($limit)
            ->get();
    }

    public function getPopularEvents(int $limit = 8)
    {
        return Event::withCount('bookings')
            ->where('status', EventStatus::ACTIVE)
            ->orderByDesc('bookings_count')
            ->take($limit)
            ->get();
    }

}
