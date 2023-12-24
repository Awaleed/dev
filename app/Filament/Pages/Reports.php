<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\PendingVendors;
use App\Filament\Widgets\SalesChart;
use App\Filament\Widgets\StatsOverviewWidget;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Filament;
use Filament\Pages\Page;

class Reports extends Page
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    protected static string $view = 'filament.pages.reports';

    protected static ?int $navigationSort = 40;

    protected static function getNavigationLabel(): string
    {
        return __('Reports');
    }

    protected function getTitle(): string
    {
        return __('Reports');
    }

    protected function getWidgets(): array
    {
        return [
            StatsOverviewWidget::class,
            SalesChart::class,
        ];
    }

    protected function getColumns(): int | array
    {
        return 1;
    }
}
