<?php

namespace App\Filament\Resources\ProductResource\Widgets;

use App\Models\Product;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;


class VendorOverview extends Widget
{
    use HasWidgetShield;

    protected static string $view = 'filament.resources.product-resource.widgets.vendor-overview';

    public ?Model $record = null;
    protected function getCards(): array
    {
        return [
            Card::make('Total Products', Product::count()),
            Card::make('Product Inventory', Product::sum('id')),
            Card::make('Average price', number_format(Product::avg('price'), 2)),
        ];
    }
}
