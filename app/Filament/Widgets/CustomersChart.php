<?php

namespace App\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Widgets\LineChartWidget;

class CustomersChart extends LineChartWidget
{
    use HasPageShield;

    protected function getHeading(): ?string
    {
        return __('Clients');
    }

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => __('Clients'),
                    'data' => [4344, 5676, 6798, 7890, 8987, 9388, 10343, 10524, 13664, 14345, 15753],
                ],
            ],
            'labels' => array_map(fn ($e) => __($e), ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']),
        ];
    }
}
