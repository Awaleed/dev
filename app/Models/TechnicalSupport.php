<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TechnicalSupport extends Model
{
    use \Znck\Eloquent\Traits\BelongsToThrough;
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'body',
        'user_id',

        'admin_id',
        'status',
        'replay',
    ];

    public function user()
    {
        return $this->belongsTo(AppUser::class, 'user_id');
    }

    public function vendor()
    {
        return $this->belongsToThrough(Vendor::class, AppUser::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
