<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EarningResource extends JsonResource
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
            'description_ar' => '$this->description_ar',
            'description_en' => '$this->description_en',
            'amount' => rand(-999, 999), //$this->amount,
            'created_at' => $this->created_at,
        ];
    }
}
