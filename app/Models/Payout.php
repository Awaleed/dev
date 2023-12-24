<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Arr;
use Spatie\ModelStatus\HasStatuses;

class Payout extends Model
{
    use \Znck\Eloquent\Traits\BelongsToThrough;

    protected $fillable = [
        'id',
        'amount',
        'bank_name',
        'account_holder_name',
        'iban',
        'payout_method_id',
        'status',
        'note',
    ];
    protected $casts = [
        'id' => 'integer',
        'is_active' => 'boolean',
        'amount' => 'float',
    ];

    public function scopeCurrentStatus(Builder $builder, ...$names)
    {
        $names = is_array($names) ? Arr::flatten($names) : func_get_args();
        $builder->whereIn('status', $names);
    }

    public function user()
    {
        return $this->belongsTo(AppUser::class);
    }

    public function earning()
    {
        return $this->belongsTo(Earning::class);
    }

    public function payout_method(): BelongsTo
    {
        return $this->belongsTo(PayoutMethod::class);
    }
    public function vendor()
    {
        return $this->belongsToThrough(Vendor::class, Earning::class);
    }

    public function notifications(): MorphMany
    {
        return $this->morphMany(AppNotification::class, 'notifiable');
    }

    public function refund()
    {
        //'pending','preparing','ready','enroute','delivered','failed','cancelled'
        if ($this->status == 'rejected') {

            $this->earning->amount -=  $this->amount;
            $this->earning->Save();

            $createdVendorTransaction =  new VendorTransaction();
            $createdVendorTransaction->amount =  $this->amount;
            $createdVendorTransaction->balance = $this->earning->amount;
            $createdVendorTransaction->reason = 'طلب سحب اموال مرفوض';
            $createdVendorTransaction->vendor_id =  $this->vendor->id;
            $createdVendorTransaction->save();
        }
    }
}
