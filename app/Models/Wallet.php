<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;

class Wallet extends NoDeleteBaseModel
{

    // protected $with = ['user'];
    protected $fillable = ["balance", "user_id"];
    protected $casts = [
        'balance' => 'double',
    ];

    public function scopeMine($query)
    {
        return $query->where('user_id', Auth::id());
    }

    public function user()
    {
        return $this->belongsTo(AppUser::class, 'user_id', 'id');
    }
}
