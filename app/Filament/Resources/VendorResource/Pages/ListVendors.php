<?php

namespace App\Filament\Resources\VendorResource\Pages;

use App\Filament\Resources\VendorResource;
use App\Filament\Resources\VendorResource\Widgets\VendorsSummaryOverview;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVendors extends ListRecords
{
    protected static string $resource = VendorResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableContentGrid(): ?array
    {
        return [
            'md' => 2,
            'xl' => 3,
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            VendorsSummaryOverview::class
        ];
    }
}
