<?php

namespace App\Filament\Resources\VendorResource\Widgets;

use App\Models\Vendor;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class VendorsSummaryOverview extends StatsOverviewWidget
{

    protected function getCards(): array
    {

        $totalCount = Vendor::count();
        $pendingCount = Vendor::where('approval_status', 'pending')->count();
        $acceptedCount = Vendor::where('approval_status', 'accepted')->count();
        $rejectedCount = Vendor::where('approval_status', 'rejected')->count();
        return [
            Card::make(__('Total vendors'), $totalCount)
                ->color('success'),
            Card::make(__('Pending vendors'), $pendingCount)
                ->color('success'),
            Card::make(__('Accepted vendors'), $acceptedCount)
                ->color('danger'),
            Card::make(__('Rejected vendors'), $rejectedCount)
                ->color('success'),
        ];
    }
}
