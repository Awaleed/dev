<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;
use willvincent\Rateable\Rateable;

class Product extends BaseModel
{
    use Rateable;

    protected $fillable = [
        "id",
        "sku",
        "name_ar",
        "name_en",
        "description_ar",
        "description_en",
        "preparation_time",
        "price",
        "discount_price",
        "capacity",
        "unit",
        "package_count",
        "available_qty",
        "featured",
        "deliverable",
        "is_active",
        "vendor_id",
        "with_option",
        'approval_status',
    ];

    protected $casts = [
        'id' => 'integer',
        'is_active' => 'boolean',
        'featured' => 'boolean',
        'deliverable' => 'boolean',
        'with_option' => 'boolean',
    ];

    protected $appends = [
        'formatted_date',
        'photo',
        'photos',
        'is_favorite',
    ];

    public function scopeActive($query)
    {
        return $query
            ->where('is_active', 1)
            ->where('approval_status', 'accepted')
            ->whereHas('vendor', function ($q) {
                $q->where('is_active', 1);
            });
    }



    // public function scopeMine($query)
    // {
    //     return $query->when(Auth::user()->hasRole('manager'), function ($query) {
    //         return $query->where('vendor_id', Auth::user()->vendor_id);
    //     })->when(Auth::user()->hasRole('city-admin'), function ($query) {
    //         return $query->where('creator_id', Auth::id());
    //     });
    // }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function menus()
    {
        return $this->belongsToMany('App\Models\Menu');
    }

    public function sales()
    {
        return $this->hasMany('App\Models\OrderProduct', 'product_id', 'id');
    }

    public function purchases()
    {
        return $this->hasMany('App\Models\OrderProduct')->whereHas(
            "order",
            function ($query) {
                return $query->where("user_id", auth('sanctum')->user()->id);
            }
        );
    }

    public function getIsFavoriteAttribute()
    {
        return false;
        if (auth('api')->user()) {
            return $this->favorite ? true : false;
        } else {
        }
    }

    public function favorite()
    {
        return $this->belongsTo('App\Models\Favorite', 'id', 'model_id'); //->where([["user_id", auth('api')->id()], ['model_name', 'App\Models\Product']]);;
    }


    public function getPhotosAttribute()
    {
        $mediaItems = $this->getMedia('default');
        $photos = [];

        foreach ($mediaItems as $mediaItem) {
            array_push($photos, $mediaItem->getFullUrl());
        }
        return $photos;
    }

    public function option_groups(): HasMany
    {
        return $this->hasMany(OptionGroup::class);
    }

    public function notifications()
    {
        return $this->morphMany(AppNotification::class, 'notifiable');
    }
}
