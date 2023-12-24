<?php

namespace App\Filament\Resources\VendorResource\Pages;

use App\Filament\Resources\VendorResource;
use App\Filament\Resources\VendorResource\Widgets\UpdateApprovalStatus;
use Filament\Pages\Actions;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewVendor extends ViewRecord
{
    protected static string $resource = VendorResource::class;

    // protected function getFooterWidgets(): array
    // {
    //     return [
    //         UpdateApprovalStatus::class
    //     ];
    // }
}
