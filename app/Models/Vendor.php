<?php

namespace App\Models;

use Laravel\Scout\Searchable;

use Malhal\Geographical\Geographical;
use Illuminate\Support\Facades\Auth;
use willvincent\Rateable\Rateable;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use GuzzleHttp\Client;

use Illuminate\Support\Facades\Schema;
use Kirschbaum\PowerJoins\PowerJoins;
use Pnlinh\GoogleDistance\Facades\GoogleDistance;
use willvincent\Rateable\Rating;

class Vendor extends BaseModel
{
    use  Geographical, Rateable, PowerJoins;

    // use Searchable;
    protected static $kilometers = true;

    protected $casts = [
        'id' => 'integer',
        'pickup' => 'boolean',
        'delivery' => 'boolean',
        'is_active' => 'boolean',
        'is_open' => 'boolean',
        'is_favorite' => 'boolean',
        'auto_assignment' => 'boolean',
        'auto_accept' => 'boolean',
        'allow_schedule_order' => 'boolean',
        'has_sub_categories' => 'boolean',
        'use_subscription' => 'boolean',
        'can_rate' => 'boolean',
        'is_package_vendor' => 'boolean',
        'has_subscription' => 'boolean',
        'show_location' => 'boolean',
        'can_message_before_order' => 'boolean',
    ];

    protected $appends = [
        'formatted_date',
        'logo',
        'feature_image',
        'rating',
        'ratings_count',
        'can_rate',
        // 'is_open',
        // 'has_subscription',
        'is_favorite',
        'promotional_text',
        'delivery_time',
    ];

    // protected $with = ['categories'];

    protected $fillable = [
        "id",
        "name_ar",
        "name_en",
        "description",
        "delivery_range",
        "min_order",
        "tax",
        "phone",
        "email",
        "address",
        "latitude",
        "longitude",
        "commission",
        "pickup",
        "delivery",
        "delivery_time",
        "is_active",
        "charge_per_km",
        "is_open",
        'show_location',
        'can_message_before_order',
        'approval_status',
    ];

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('logo')
            ->useFallbackUrl('' . url('') . '/images/default.png')
            ->useFallbackPath(public_path('/images/default.png'));
        $this
            ->addMediaCollection('feature_image')
            ->useFallbackUrl('' . url('') . '/images/default.png')
            ->useFallbackPath(public_path('/images/default.png'));
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function scopeIsPackageDelivery($query)
    {
        return $query->where('is_package_vendor', 1);
    }

    public function scopeRegularVendor($query)
    {
        return $query->where('is_package_vendor', 0);
    }

    public function scopeMine($query)
    {
        $user = AppUser::find(Auth::id());
        return $query->when($user->hasRole('manager'), function ($query) use ($user) {
            return $query->where('id', $user->vendor_id);
        })->when($user->hasRole('city-admin'), function ($query) use ($user) {
            return $query->where('creator_id', $user->id);
        });
    }

    public function getLogoAttribute()
    {
        return $this->getFirstMediaUrl('logo');
    }
    public function getFeatureImageAttribute()
    {
        return $this->getFirstMediaUrl('feature_image');
    }


    public function getRatingAttribute()
    {
        return  (float) ($this->averageRating() ?? setting("defaultVendorRating", 3));
    }
    public function getRatingsCountAttribute()
    {
        return  (int) ($this->timesRated() ?? 0);
    }

    public function getIsFavoriteAttribute()
    {
        try {
            if (auth('api')->user()) {
                return $this->favorite ? true : false;
            } else {
                return false;
            }
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function favorite()
    {
        return $this->belongsTo('App\Models\Favorite', 'id', 'model_id')->where([["user_id", auth('api')->id()], ['model_name', 'App\Models\Vendor']]);
    }


    // public function getIsOpenAttribute($value)
    // {
    //     $value = $this->getRawOriginal('is_open');
    //     if ($this->id == 175) {
    //         logger("openNow", [$this->openNow]);
    //     }
    //     if (!$value) {
    //         return false;
    //     } elseif (count($this->days) == 0) {
    //         return true;
    //     } elseif (count($this->openToday) > 0 && count($this->openNow) > 0) {
    //         return true;
    //     } else {
    //         return false;
    //     }
    // }

    //
    public function getCanRateAttribute()
    {

        if (empty(Auth::user())) {
            return false;
        }
        //

        return !Rating::query()
            ->where('rateable_type', '=', $this->getMorphClass())
            ->where('rateable_id', '=', $this->id)
            ->where('user_id', '=', Auth::id())
            ->first();
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


    public function earning()
    {
        return $this->hasOne(Earning::class);
    }

    public function managers()
    {
        return $this->hasMany(AppUser::class, 'vendor_id', 'id');
    }

    public function sales()
    {
        return $this->hasMany('App\Models\Order', 'vendor_id', 'id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(BankAccount::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'vendor_id', 'id');
    }

    public function menus()
    {
        return $this->hasMany(Menu::class)->where('is_active', 1);
    }

    public function districts()
    {
        return $this->belongsToMany('App\Models\District');
    }

    public function payment_methods()
    {
        return $this->belongsToMany('App\Models\PaymentMethod');
    }

    public function package_types_pricing()
    {
        return $this->hasMany('App\Models\PackageTypePricing', 'vendor_id', 'id');
    }

    public function options()
    {
        return $this->hasMany(Option::class);
    }

    public function offers()
    {
        return $this->belongsToMany(Offer::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function promotionalText(): Attribute
    {
        return new Attribute(
            get: function () {
                $values = array(...$this->offers->pluck('promotional_text'));
                if ($values)
                    return implode(', ', $values);
                else
                    return null;
            }
        );
    }


    public function deliveryTime(): Attribute
    {
        return new Attribute(
            get: function () {

                $latitude = request()->latitude;
                $longitude = request()->longitude;
                $key = env('GOOGLE_MAPS_API_KEY', '');

                if (
                    !$latitude ||
                    !$longitude ||
                    !$key ||
                    !$this->latitude ||
                    !$this->longitude
                ) {
                    return '-';
                }

                if ($this->distance > 50)
                    return '--';
                try {
                    $httpClint = new Client();
                    $response = $httpClint->get(
                        'https://maps.googleapis.com/maps/api/distancematrix/json',
                        [
                            'query' => [
                                "key" => $key,
                                "origins" => $latitude . "," . $longitude,
                                "destinations" => $this->latitude . "," . $this->longitude,
                                "language" => 'ar',
                            ]
                        ]
                    );

                    $data = json_decode($response->getBody()->getContents(), true);
                    logger('res', ['$data' => $data]);
                    return $data['rows'][0]['elements'][0]['duration']['text'];
                } catch (\Throwable $th) {
                    logger()->error('', ['error' => $th]);
                    return '-';
                }
            },
        );
    }

    public function notifications(): MorphMany
    {
        return $this->morphMany(AppNotification::class, 'notifiable');
    }

    public function sendNotificationToMangers(AppNotification $appNotification, $modelToAttach = null)
    {
        $users = $this->managers()->role('manager')->get();


        foreach ($users as $user) {
            $newAppNotification = $appNotification->replicate();
            $newAppNotification->user_id = $user->id;
            $newAppNotification->save();

            if ($modelToAttach) {
                $modelToAttach->notifications()->save($newAppNotification);
            }
        }
    }
}
