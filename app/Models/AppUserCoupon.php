<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class AppUserCoupon extends Model
{
    use HasFactory, PowerJoins;
    public $table = "app_user_coupon";
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(AppUser::class);
    }
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
