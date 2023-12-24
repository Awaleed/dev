<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\VendorResource;
use App\Models\Vendor;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class PendingVendors extends BaseWidget
{
    use HasWidgetShield;

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    protected function getTableHeading(): string
    {
        return __('Pending vendors');
    }

    public function getDefaultTableRecordsPerPageSelectOption(): int
    {
        return 5;
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'created_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    protected function getTableQuery(): Builder
    {
        return Vendor::where('approval_status', 'pending');
    }

    protected function getTableColumns(): array
    {
        return [

            Tables\Columns\SpatieMediaLibraryImageColumn::make('logo')
                ->translateLabel()
                ->collection('logo'),
            Tables\Columns\TextColumn::make('name_ar')
                ->searchable()
                ->translateLabel(),
            Tables\Columns\TextColumn::make('phone')
                ->searchable()
                ->translateLabel(),
            Tables\Columns\TextColumn::make('email')
                ->searchable()
                ->translateLabel(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('open')
                ->translateLabel()
                ->url(fn (Vendor $record): string => VendorResource::getUrl('edit', ['record' => $record])),
        ];
    }
}
