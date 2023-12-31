<?php

namespace App\Filament\Resources\AppUserResource\Pages;

use App\Filament\Resources\AppUserResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAppUser extends EditRecord
{
    protected static string $resource = AppUserResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
