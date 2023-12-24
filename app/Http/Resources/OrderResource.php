<?php

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "number"           => $this->number,
            "code"             => $this->code,
            "note"             => $this->note,
            "delivery_note"    => $this->delivery_note,
            "latitude"         => $this->latitude,
            "longitude"        => $this->longitude,
            "vendor_latitude"  => $this->vendor_latitude,
            "vendor_longitude" => $this->vendor_longitude,
            "reason"           => $this->reason,
            "payment_status"   => $this->payment_status,
            "polyline"         => $this->polyline,
            "sub_total"        => $this->sub_total,
            "discount"         => $this->discount,
            "delivery_fee"     => $this->delivery_fee,
            "total"            => $this->total,
            "tax"              => $this->tax,
            "driver"           => $this->driver,
            "created_at"       => $this->created_at,
            "updated_at"       => $this->updated_at,
            "can_rate"         => $this->can_rate,
            "can_rate_driver"  => $this->can_rate_driver,
            "status"           => $this->status,
            "user"             => $this->user,
            "payment"          => $this->payment,
            "vendor"           => VendorResource::make($this->vendor),
            "payment_method"   => $this->payment_method,
            "statuses"         => $this->statuses,
            "address"          => $this->address,
            "vendor_address"   => $this->vendor_address,
            "distance"         => $this->distance,
            "products"         => OrderItemResource::collection($this->products),
        ];
    }
}
class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'quantity'                => $this->quantity,
            'price'                   => $this->price,
            'options_ar'              => $this->options_ar,
            'options_en'              => $this->options_en,
            'options_ids'             => $this->options_ids,
            'product'                 => ProductResource::make($this->product),
        ];
    }
}
