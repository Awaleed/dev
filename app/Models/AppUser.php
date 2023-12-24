<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;
use willvincent\Rateable\Rateable;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;

class AppUser extends Authenticatable implements HasMedia
{
    use HasFactory, Notifiable, InteractsWithMedia, HasRoles, HasApiTokens, Rateable, SoftDeletes;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        // 'password',
        'is_active',
        'country_code'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        // 'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'is_online' => 'boolean'
    ];

    protected $appends = [
        'role_name',
        'role_id',
        'formatted_date',
        'photo',
        'rating',
        'balance',
    ];

    /**
     * Get the value indicating whether the IDs are incrementing.
     *
     * @return bool
     */
    public function getIncrementing()
    {
        return false;
    }

    /**
     * Get the auto-incrementing key type.
     *
     * @return string
     */
    public function getKeyType()
    {
        return 'string';
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('profile')
            ->useFallbackUrl('' . url('') . '/images/user.png')
            ->useFallbackPath(public_path('/images/user.png'));
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function getPhotoAttribute()
    {
        return $this->getFirstMediaUrl('profile');
    }

    public function getRoleNameAttribute()
    {
        return $this->roles->first()->name ?? "";
    }

    public function getRoleIdAttribute()
    {
        return $this->roles->first()->id ?? "";
    }

    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('d M Y');
    }

    public function getRatingAttribute()
    {
        return  (int) ($this->averageRating ?? 3);
    }

    public function getBalanceAttribute()
    {
        return $this->wallet?->balance ?? 0;
    }

    public function getAssignedOrdersAttribute()
    {
        return null;
        // return  Order::where("driver_id", $this->id)->otherCurrentStatus(['failed', 'cancelled', 'delivered'])->count() ?? 0;
    }

    public function getDocumentsAttribute()
    {
        $mediaItems = $this->getMedia('documents');
        $photos = [];

        foreach ($mediaItems as $mediaItem) {
            array_push($photos, $mediaItem->getFullUrl());
        }
        return $photos;
    }

    public function scopeManager($query)
    {
        return $query->role('manager');
    }

    public function scopeAdmin($query)
    {
        return $query->role('admin');
    }

    public function scopeClient($query)
    {
        return $query->role('client');
    }

    //vendors
    public function vendors()
    {
        return $this->hasMany('App\Models\Vendor', 'creator_id', 'id');
    }

    public function vendor()
    {
        return $this->belongsTo('App\Models\Vendor');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function vehicle()
    {
        return $this->hasOne('App\Models\Vehicle', 'driver_id', 'id');
    }


    public function wallet()
    {
        return $this->hasOne(Wallet::class, 'user_id', 'id');
    }



    //NOTIFICATION
    public function syncFCMToken($token)
    {
        try {
            if (!empty($token)) {
                $userToken = UserToken::create([
                    "user_id" => Auth::id(),
                    "token" => $token
                ]);
            }
        } catch (\Exception $ex) {
            Log::debug([
                "Error" => $ex->getMessage()
            ]);
        }
    }

    //NOTIFICATION
    public function clearFCMToken()
    {
        UserToken::where("user_id", Auth::id())->delete();
    }


    //Wallet
    public function updateWallet($amount)
    {
        $wallet = Wallet::firstOrCreate(
            ['user_id' =>  $this->id],
            ['balance' => 0.00]
        );

        //
        $newAmount = $amount - $wallet->balance;
        $wallet->balance = $amount;
        $wallet->save();


        //
        $walletTransaction = new WalletTransaction();
        $walletTransaction->amount = $newAmount >= 0 ? $newAmount : ($newAmount * -1);
        $walletTransaction->wallet_id = $wallet->id;
        $walletTransaction->is_credit = $newAmount >= 0 ? 1 : 0;
        $walletTransaction->reason = $newAmount >= 0 ? "Topup" : "Debit";
        $walletTransaction->ref = Str::random(10);
        $walletTransaction->status = "successful";
        $walletTransaction->save();
        return $wallet;
    }

    public function topupWallet($amount)
    {
        $wallet = wallet::firstOrCreate(
            ['user_id' =>  $this->id],
            ['balance' => 0.00]
        );

        //
        $wallet->balance += $amount;
        $wallet->save();
        return $wallet;
    }



    public function commission(): Attribute
    {
        return new Attribute(
            get: fn ($value) => '',
            set: fn ($value) => $value,
        );
    }

    public function save(array $options = [])
    {
        return $this;
        // You can add a condition to allow save in some cases
        throw new \Exception('Editing users is currently disabled.');
    }

    public function update(array $attributes = [], array $options = [])
    {
        return $this;
        // You can add a condition to allow update in some cases
        throw new \Exception('Editing users is currently disabled.');
    }

    public function delete()
    {
        return $this;
        // You can add a condition to allow delete in some cases
        throw new \Exception('Editing users is currently disabled.');
    }
}
