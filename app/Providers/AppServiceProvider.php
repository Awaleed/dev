<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        ini_set('memory_limit', '-1');
        JsonResource::withoutWrapping();
        Filament::registerStyles([
            asset('css/app.css'),
        ]);
    }
}
