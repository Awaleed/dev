<?php

namespace App\Filament\Resources\OptionGroupResource\Pages;

use App\Filament\Resources\OptionGroupResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOptionGroups extends ListRecords
{
    protected static string $resource = OptionGroupResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
