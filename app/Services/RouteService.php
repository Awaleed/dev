<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RouteService
{



    //
    public static function getPolyline(
        $originLatitude,
        $originLongitude,
        $destinationLatitude,
        $destinationLongitude,
    ) {
        if ($originLatitude == null || $originLongitude == null || $destinationLatitude == null || $destinationLongitude == null) {
            return null;
        }

        $response =  Http::get('https://maps.googleapis.com/maps/api/directions/json', [
            'origin' => $originLatitude . ',' . $originLongitude,
            'destination' => $destinationLatitude . ',' . $destinationLongitude,
            'key' => env('GOOGLE_MAPS_API_KEY'),
        ]);
        if (!$response->successful()) {
            return null;
        }


        if ($response->status() == 200) {
            $parsedJson = json_decode($response->body());
            if ($parsedJson->status == 'OK' && !empty($parsedJson->routes)) {
                return $parsedJson->routes[0]->overview_polyline->points;
            } else {
                return null;
            }
        }
    }
}
