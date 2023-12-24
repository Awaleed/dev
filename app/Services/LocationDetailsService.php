<?php

namespace App\Services;

use Geocoder\Geocoder;
use Geocoder\Query\ReverseQuery;

class LocationDetailsService
{

    static  public function get($latitude, $longitude, $locale = 'ar')

    {
        if (doubleval($latitude) == null || doubleval($longitude) == null)
            return null;

        $response = app('geocoder')->reverseQuery(
            ReverseQuery::fromCoordinates(
                doubleval($latitude),
                doubleval($longitude),
            )->withLocale(
                request()->header('Accept-Language', $locale)
            )
        )
            ->dump('geojson');

        if ($response->isEmpty()) {
            return [

                'active' => false,
                'label_ar' => '',
                'label_en' => '',
                'label' => '',
                'locality' => '',
                'sub_locality' => '',
                'street_name' => '',
                'latitude' => doubleval($latitude),
                'longitude' => doubleval($longitude),
            ];
        }


        $address = json_decode($response->first());
        foreach ($response as $add) {
            $add = json_decode($add);
            try {
                if ($add->properties->locality && $add->properties->subLocality &&    $add->properties->streetName) {
                    $address = $add;
                }
            } catch (\Throwable $th) {
                //throw $th;
            }
        }

        $locality = '';
        $subLocality = '';
        $streetName = '';


        try {
            $locality = $address->properties->locality;
        } catch (\Throwable) {
        }
        try {
            $subLocality = $address->properties->subLocality;
        } catch (\Throwable) {
        }
        try {
            $streetName = $address->properties->streetName;
        } catch (\Throwable) {
        }



        $addressString  = implode(', ', [
            $locality ? $locality : null,
            $subLocality ? $subLocality : null,
            $streetName ? $streetName : null,
        ]);

        return [
            'active' => true,
            'label_ar' => $addressString,
            'label_en' => $addressString,
            'label' => $addressString,
            'locality' => $locality,
            'sub_locality' => $subLocality,
            'street_name' => $streetName,
            'latitude' => doubleval($latitude),
            'longitude' => doubleval($longitude),
        ];
    }
}
