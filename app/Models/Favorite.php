<?php

namespace App\Models;

class Favorite extends BaseModel
{

    public function product()
    {
        return $this->belongsTo(Product::class, 'model_id', 'id');
    }

    public function vendor()
    {
        return $this->belongsTo('App\Models\Vendor', 'model_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(AppUser::class, 'user_id', 'id');
    }
}
