<?php

namespace App\Providers;

use App\Listeners\OrderStatusEventSubscriber;
use App\Models\AppNotification;
use App\Models\Order;
use App\Models\Payout;
use App\Models\Product;
use App\Models\Vendor;
use App\Observers\AppNotificationObserver;
use App\Observers\OrderObserver;
use App\Observers\PayoutObserver;
use App\Observers\ProductObserver;
use App\Observers\VendorObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];
    protected $subscribe = [
        OrderStatusEventSubscriber::class,
    ];
    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
        Payout::observe(PayoutObserver::class);
        Product::observe(ProductObserver::class);
        Vendor::observe(VendorObserver::class);
        Order::observe(OrderObserver::class);
        AppNotification::observe(AppNotificationObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
