<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PayoutMethod extends Model
{

    protected $fillable = [
        'name_ar',
        'name_en',
        'slug',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->slug = Str::slug($model->name);
            $model->save();
        });
    }
}
