<?php

namespace App\Http\Controllers;

use App\Services\LocationService;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Spatie\Geocoder\Facades\Geocoder;


class LocationController extends Controller
{
    public function test()
    {
        $userLat = session('user_latitude');
        $userLon = session('user_longitude');


        $apiKey = env('GOOGLE_MAPS_KEY');
        $url    = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$userLat,$userLon&key=$apiKey&language=tr";

        $response = Http::get($url);
        $data     = $response->json();
    }

    public function store(Request $request, LocationService $locationService)
    {
        $validated = $request->validate([
            'latitude'  => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $lat = $validated['latitude'];
        $lon = $validated['longitude'];

        $location = $locationService->getCityAndDistrict($lat, $lon);

        Session::put([
            'user_latitude'  => $lat,
            'user_longitude' => $lon,
            'user_city'      => $location['city'],
            'user_district'  => $location['district'],
            'user_address'   => $location['district'] . ', ' . $location['city'],
        ]);

        return response()->json(['status' => 'success']);
    }
}
