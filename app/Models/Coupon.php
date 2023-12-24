<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Coupon extends BaseModel
{
    protected $casts = [
        'is_active' => 'boolean',
        'percentage' => 'boolean',
        'exclude_discounted' => 'boolean',
        'free_delivery' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected $fillable = [
        'code',
        'description',
        'discount',
        'maximum_discount',
        'percentage',
        'expires_on',
        'times',
        'times_per_user',
        'exclude_discounted',
        'free_delivery',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $appends = ['formatted_expires_on', 'use_left', 'expired', 'products_ids'];

    public function getFormattedExpiresOnAttribute()
    {
        return Carbon::parse($this->expires_on)->format('d M Y');
    }

    public function products()
    {
        return $this->belongsToMany('App\Models\Product');
    }

    public function vendors()
    {
        return $this->belongsToMany('App\Models\Vendor');
    }

    public function getUseLeftAttribute()
    {

        if (empty($this->times)) {
            return 1;
        }

        $couponUses = AppUserCoupon::where([
            'coupon_id' => $this->id,
            'app_user_id' => Auth::id(),
        ])->get()->count();
        //
        return $this->times - $couponUses;
    }

    public function getExpiredAttribute()
    {
        return $this->expires_on < now();
    }

    public function getProductsIdsAttribute()
    {
        return $this->products()->pluck('id');
    }

    public static function findByCode($code)
    {
        return self::where('code', $code)->first();
    }
}
