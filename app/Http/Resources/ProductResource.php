<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name_ar' => $this->name_ar,
            'sku' => $this->sku,
            'description_ar' => $this->description_ar,
            'price' => $this->price,
            'discount_price' => $this->discount_price,
            'available_qty' => $this->available_qty,
            'preparation_time' => $this->preparation_time,
            'featured' => $this->featured,
            'is_active' => $this->is_active,
            'photo' => $this->photo,
            'is_favorite' => $this->is_favorite,
            'option_groups' => $this->option_groups()->where('product_id', "=", $this->id)->with('options')->get(),
            'photos' => $this->photos,
            'menus' => $this->menus,
            'vendor_id' => $this->vendor_id,
            'vendor' => VendorResource::make($this->vendor),
            'category' => $this->category,
            'approval_status' => $this->approval_status,
        ];
    }
}
