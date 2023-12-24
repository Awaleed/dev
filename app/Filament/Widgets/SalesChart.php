<?php

namespace App\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Widgets\LineChartWidget;

class SalesChart extends LineChartWidget
{
    use HasPageShield;

    protected static ?int $sort = 1;

    protected function getHeading(): ?string
    {
        return __('Sales');
    }


    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => __('Sales'),
                    'data' => [2433, 3454, 4566, 2342, 5545, 5765, 6787, 8767, 7565, 8576, 9686, 8996],
                ],
            ],
            'labels' => array_map(fn ($e) => __($e), ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']),
        ];
    }
}
