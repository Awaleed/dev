<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppNotification extends BaseModel
{
    protected $fillable = [
        'user_id',
        'title_ar',
        'title_en',
        'url',
        'text_ar',
        'text_en',
        'read_at',
    ];

    protected $casts = [
        // 'read' => '',
    ];

    public function icon(): Attribute
    {
        return new Attribute(
            get: fn ($value) => 'strtoupper($value)',
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(AppUser::class);
    }

    public function notifiable()
    {
        return $this->morphTo();
    }
}
