<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VendorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {


        $delivery_fee = $this->delivery_fee;

        // if (!auth('api')->check() || auth('api')->user()->hasRole('client')) {

        //     $offer = $this->offers()
        //         ->active()
        //         ->hasDelivery()
        //         ->latest()
        //         ->first();
        //     $delivery_fee = $offer->delivery_fee ?? $this->delivery_fee;
        // }
        return [
            "id" => $this->id,
            "name_ar" => $this->name_ar,
            "name_en" => $this->name_en,
            "description" => $this->description,
            "phone" => $this->phone,
            "email" => $this->email,
            "address" => $this->address,
            "latitude" => $this->latitude,
            "longitude" => $this->longitude,
            "delivery_time" => $this->delivery_time,
            "is_open" => $this->is_open,
            "is_active" => $this->is_active,
            "min_order" => $this->min_order,
            "show_location" => $this->show_location,
            "can_message_before_order" => $this->can_message_before_order,
            "distance" => $this->distance,
            "logo" => $this->logo,
            "feature_image" => $this->feature_image,
            "rating" => $this->rating,
            "ratings_count" => $this->ratings_count,
            "can_rate" => $this->can_rate,
            "is_favorite" => $this->is_favorite,
            "categories" => $this->categories,
            "approval_status" => $this->approval_status,
            "promotional_text" => $this->promotional_text,
            "delivery_fee" => $delivery_fee,
        ];
    }
}
