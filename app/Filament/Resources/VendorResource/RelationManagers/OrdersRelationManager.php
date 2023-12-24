<?php

namespace App\Filament\Resources\VendorResource\RelationManagers;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    protected static ?string $recordTitleAttribute = 'number';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('number')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->translateLabel()
                    ->label('Order Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('number')
                    ->translateLabel()
                    ->searchable()
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
                    ])->enum([
                        'pending' => __('Pending')
                    ]),

                Tables\Columns\TextColumn::make('total')
                    ->translateLabel()
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->url(fn (Order $record): string => OrderResource::getUrl('edit', ['record' => $record])),
            ])
            ->bulkActions([
            ]);
    }

    public static function getRecordLabel(): string
    {
        return __('Order');
    }

    public static function getTitle(): string
    {
        return __('Orders');
    }
}
