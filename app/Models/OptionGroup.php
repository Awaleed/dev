<?php

namespace App\Models;

class OptionGroup extends BaseModel
{

    // with
    protected $with = ['options'];
    protected $fillable = [
        'name_ar',
        'name_en',
        'multiple',
        'required',
        'is_active',
        'product_id',
    ];

    protected $casts = [
        'multiple' => 'boolean',
        'required' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function options()
    {
        return $this->hasMany(Option::class, 'option_group_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
