<?php

namespace App\Models;

class Menu extends BaseModel
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'vendor_id',
        'is_active'
    ];
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
