<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;

class Payment extends NoDeleteBaseModel
{
    public $appends = [
        'status_message'
    ];


    public function statusMessage(): Attribute
    {
        return new Attribute(
            get: fn () => 'strtoupper($value)',
        );
    }
}
