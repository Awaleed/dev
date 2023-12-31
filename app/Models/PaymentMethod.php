<?php

namespace App\Models;

use Illuminate\Support\Str;

class PaymentMethod extends BaseModel
{

    protected $fillable = ["slug", "name_ar", "name_en", "is_active", "is_cash"];

    protected $hidden = ["secret_key", "hash_key"];

    protected $casts = [
        'is_active' => 'boolean',
        'is_cash' => 'boolean',
        'use_taxi' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->slug = Str::slug($model->name);
            $model->save();
        });
    }


    public function scopeTopUp($query)
    {
        return $query->where('is_cash', 0);
    }

    public function scopeSub($query)
    {
        return $query->where('is_cash', 0)->where('slug', "!=", "wallet");
    }
}
