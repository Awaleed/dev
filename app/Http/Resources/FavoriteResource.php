<?php

namespace App\Http\Resources;

use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if ($this->model_name == 'App\Models\Product') {
            return ProductResource::make($this->product);
        } else if ($this->model_name == 'App\Models\Vendor') {
            return VendorResource::make($this->vendor);
        }
        return  parent::toArray($request);
    }
}
