<?php

namespace App\Models;

class Option extends BaseModel
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'description',
        'price',
        'option_group_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'double',
    ];

    public function option_group()
    {
        return $this->belongsTo('App\Models\OptionGroup', 'option_group_id', 'id');
    }
}
