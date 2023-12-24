<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\AppUserResource;
use App\Filament\Resources\PayoutResource;
use App\Filament\Resources\ProductResource;
use App\Filament\Resources\TechnicalSupportResource;
use App\Models\AppUser;
use App\Models\Payout;
use App\Models\Product;
use App\Models\TechnicalSupport;
use App\Models\Vendor;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class StatsOverviewWidget extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 0;

    protected function getCards(): array
    {
        // $previousMonthUsers = Vendor::whereMonth('created_at', now()->month - 1)->count();
        // $thisMonthUsers = Vendor::whereMonth('created_at', now()->month)->count();
        // if ($previousMonthUsers > 0) {
        //     // If it has decreased then it will give you a percentage with '-'
        //     $differenceInpercentage = ($thisMonthUsers - $previousMonthUsers) * 100 / $previousMonthUsers;
        // } else {
        //     $differenceInpercentage = $thisMonthUsers > 0 ? '100%' : '0%';
        // }
        return [


            Card::make(__('Payouts'), Payout::currentStatus('pending')->count())
                ->url(PayoutResource::getUrl()),

            Card::make(__('Complaints & Inquiries'), TechnicalSupport::where('status', 'open')->count())
                ->url(TechnicalSupportResource::getUrl()),

            Card::make(__('Vendors'), Vendor::count())
                ->url(route('filament.resources.vendors.index'))
                ->description(Vendor::where('approval_status', 'pending')->count() . ' ' . __('Pending vendors'))
                ->color('warning'),

            Card::make(__('Products'), Product::count())
                ->url(ProductResource::getUrl())
                ->description(Product::where('approval_status', 'pending')->count() . ' ' . __('Pending Products'))
                ->color('warning'),

            Card::make(__('Clients'), AppUser::role('client')->count())
                ->url(AppUserResource::getUrl()),

            Card::make(__('Earnings'), '410 ر.س'),

        ];
    }
}
