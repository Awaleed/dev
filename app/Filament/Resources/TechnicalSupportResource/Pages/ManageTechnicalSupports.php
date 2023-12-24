<?php

namespace App\Filament\Resources\TechnicalSupportResource\Pages;

use App\Filament\Resources\TechnicalSupportResource;
use Filament\Resources\Pages\ManageRecords;

class ManageTechnicalSupports extends ManageRecords
{
    protected static string $resource = TechnicalSupportResource::class;

    protected function getActions(): array
    {
        return [];
    }
}
