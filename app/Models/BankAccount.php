<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankAccount extends BaseModel
{

    protected $casts = [
        'id' => 'integer',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    public $fillable = [
        'bank_name',
        'account_holder_name',
        'iban',
        'vendor_id',
        'is_active',
        'is_default',
    ];


    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}
