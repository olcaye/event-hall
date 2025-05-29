<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LocationService
{
    const DEFAULT_LAT = 41.0082;
    const DEFAULT_LON = 28.9784;

    public function getDefaultCoordinates(): array
    {
        return [self::DEFAULT_LAT, self::DEFAULT_LON];
    }

    public function getCityAndDistrict(float $lat, float $lon): array
    {
        $city     = null;
        $district = null;

        try {
            $apiKey = env('GOOGLE_MAPS_KEY');
            $url    = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$lat,$lon&key=$apiKey&language=tr";

            $response = Http::timeout(5)->get($url);
            $data     = $response->json();

            if (!empty($data['results'][0]['address_components'])) {
                foreach ($data['results'][0]['address_components'] as $component) {
                    if (in_array('administrative_area_level_1', $component['types'])) {
                        $city = $component['long_name'];
                    }
                    if (in_array('administrative_area_level_2', $component['types'])) {
                        $district = $component['long_name'];
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Geocoding failed: ' . $e->getMessage());
        }

        return [
            'city'     => $city ?? 'Unknown',
            'district' => $district ?? 'Unknown',
        ];
    }
}
