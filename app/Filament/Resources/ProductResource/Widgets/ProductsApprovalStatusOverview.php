<?php

namespace App\Filament\Resources\ProductResource\Widgets;

use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\Widget;

class ProductsApprovalStatusOverview extends StatsOverviewWidget
{

    protected function getCards(): array
    {

        $totalCount = Product::count();
        $pendingCount = Product::where('approval_status', 'pending')->count();
        $acceptedCount = Product::where('approval_status', 'accepted')->count();
        $rejectedCount = Product::where('approval_status', 'rejected')->count();
        return [
            Card::make(__('Total Products'), $totalCount)
                ->color('success'),
            Card::make(__('Pending Products'), $pendingCount)
                ->color('success'),
            Card::make(__('Accepted Products'), $acceptedCount)
                ->color('danger'),
            Card::make(__('Rejected Products'), $rejectedCount)
                ->color('success'),
        ];
    }
}
