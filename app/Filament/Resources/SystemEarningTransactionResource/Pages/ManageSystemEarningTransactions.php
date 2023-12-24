<?php

namespace App\Filament\Resources\SystemEarningTransactionResource\Pages;

use App\Filament\Resources\SystemEarningTransactionResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSystemEarningTransactions extends ManageRecords
{
    protected static string $resource = SystemEarningTransactionResource::class;

    protected function getActions(): array
    {
        return [];
    }
}
