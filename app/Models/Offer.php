<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Offer extends BaseModel
{
    protected $fillable = [
        'title',
        'description',
        'promotional_text',
        'url',
        'type',
        'delivery_fee',
        'delivery',
        'is_active',
        'starting_at',
        'ending_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'delivery' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', '=', 1);
    }

    public function scopeHasDelivery($query)
    {
        return $query->whereNotNull('delivery_fee')->where('delivery', 1);
    }

    public function vendors(): BelongsToMany
    {
        return $this->belongsToMany(Vendor::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
}
