<?php

namespace App\Filament\Resources\PayoutResource\Pages;

use App\Filament\Resources\PayoutResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePayouts extends ManageRecords
{
    protected static string $resource = PayoutResource::class;

    protected function getActions(): array
    {
        return [];
    }
}
