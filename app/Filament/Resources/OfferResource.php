<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\RelationManagers\VendorsRelationManager;
use App\Filament\Resources\OfferResource\Pages;
use App\Filament\Resources\OfferResource\RelationManagers;
use App\Filament\Resources\VendorResource\RelationManagers\ProductsRelationManager;
use App\Models\Offer;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OfferResource extends Resource
{
    protected static ?string $model = Offer::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-tax';

    protected static ?int $navigationSort = 9;

    public static function getPluralLabel(): ?string
    {
        return __('Offers');
    }

    public static function getLabel(): ?string
    {
        return __('Offer');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Section::make(__('Photo'))
                    ->collapsible()
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('photo')
                            ->required()
                            ->translateLabel()

                    ]),
                Forms\Components\Section::make(__('Name and description'))
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->translateLabel()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->translateLabel()
                            ->maxLength(65535),

                    ]),
                Forms\Components\Section::make(__('Details'))
                    ->collapsible()
                    ->schema([

                        Forms\Components\Grid::make(2)
                            ->disableLabel()
                            ->schema([
                                Forms\Components\TextInput::make('promotional_text')
                                    ->translateLabel()
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\Select::make('type')
                                    ->translateLabel()
                                    ->required()
                                    ->options([
                                        'vendors' => __('Vendors'),
                                        'products' => __('Products'),
                                        'url' => __('Url'),
                                        'photo' => __('Photo'),
                                    ]),
                                Forms\Components\TextInput::make('url')
                                    ->translateLabel()
                                    ->columnSpanFull(),


                                Forms\Components\DateTimePicker::make('starting_at')
                                    ->translateLabel()
                                    ->required(),
                                Forms\Components\DateTimePicker::make('ending_at')
                                    ->translateLabel(),
                            ]),

                    ]),
                Forms\Components\Section::make(__('Delivery'))
                    ->collapsible()
                    ->schema([
                        Forms\Components\Toggle::make('has_delivery')
                            ->required()
                            ->translateLabel(),
                        Forms\Components\TextInput::make('delivery_fee')
                            ->translateLabel(),
                    ]),
                Forms\Components\Toggle::make('is_active')
                    ->translateLabel()
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('description')
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('promotional_text')
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('url')
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('type')
                    ->translateLabel(),
                Tables\Columns\IconColumn::make('is_active')
                    ->translateLabel()
                    ->boolean(),
                Tables\Columns\TextColumn::make('starting_at')
                    ->translateLabel()
                    ->dateTime(),
                Tables\Columns\TextColumn::make('ending_at')
                    ->translateLabel()
                    ->dateTime(),
                // Tables\Columns\TextColumn::make('created_at')
                //     ->translateLabel()
                //     ->dateTime(),
                // Tables\Columns\TextColumn::make('updated_at')
                //     ->translateLabel()
                //     ->dateTime(),
                // Tables\Columns\TextColumn::make('deleted_at')
                //     ->translateLabel()
                //     ->dateTime(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
            VendorsRelationManager::class,
            ProductsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOffers::route('/'),
            'create' => Pages\CreateOffer::route('/create'),
            'edit' => Pages\EditOffer::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
