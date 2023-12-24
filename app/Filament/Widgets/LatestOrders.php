<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestOrders extends BaseWidget
{
    use HasWidgetShield;

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    protected function getTableHeading(): string
    {
        return __('Latest Orders');
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
        return OrderResource::getEloquentQuery();
    }

    protected function getTableColumns(): array
    {
        return [

            Tables\Columns\TextColumn::make('number')
                ->translateLabel()
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('created_at')
                ->translateLabel()
                ->label('Order Date')
                ->date()
                ->sortable(),
            Tables\Columns\TextColumn::make('user.name')
                ->translateLabel()
                ->searchable()
                ->sortable(),
            Tables\Columns\BadgeColumn::make('status')
                ->translateLabel()
                ->colors([
                    'danger' => 'cancelled',
                    'warning' => 'processing',
                    'success' => fn ($state) => in_array($state, ['delivered', 'shipped']),
                ]),

            Tables\Columns\TextColumn::make('total')
                ->translateLabel()
                ->suffix(' ر.س')
                ->searchable()
                ->sortable(),

        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('open')
                ->url(fn (Order $record): string => OrderResource::getUrl('edit', ['record' => $record])),
        ];
    }
}
