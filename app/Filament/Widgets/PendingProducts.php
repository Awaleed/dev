<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ProductResource;
use App\Models\Product;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class PendingProducts extends BaseWidget
{
    use HasWidgetShield;

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    protected function getTableHeading(): string
    {
        return __('Pending Products');
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
        return Product::where('approval_status', 'pending');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\SpatieMediaLibraryImageColumn::make('photo')
                ->translateLabel(),
            Tables\Columns\TextColumn::make('name_ar')
                ->searchable()
                ->translateLabel(),
            Tables\Columns\TextColumn::make('vendor.name_ar')
                ->searchable()
                ->translateLabel(),
            Tables\Columns\TextColumn::make('categories.name_ar')
                ->searchable()
                ->translateLabel(),
            Tables\Columns\TextColumn::make('price')
                ->translateLabel(),
            Tables\Columns\TextColumn::make('discount_price')
                ->translateLabel(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('open')
                ->translateLabel()
                ->url(fn (Product $record): string => ProductResource::getUrl('edit', ['record' => $record])),
        ];
    }
}
