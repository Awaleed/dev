<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\VendorResource;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Photos'))
                    ->collapsible()
                    ->schema([
                        Forms\Components\SpatieMediaLibraryFileUpload::make('photos')
                            ->translateLabel()
                            ->multiple()
                            ->enableReordering(),
                    ]),
                Forms\Components\Section::make(__('Name and description'))
                    ->collapsible()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->disableLabel()
                            ->schema([
                                Forms\Components\TextInput::make('name_ar')
                                    ->translateLabel()
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('name_en')
                                    ->translateLabel()
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Textarea::make('description_ar')
                            ->translateLabel()
                            ->maxLength(65535),
                        Forms\Components\Textarea::make('description_en')
                            ->translateLabel()
                            ->maxLength(65535),

                    ]),

                Forms\Components\Section::make(__('Product details'))
                    ->collapsible()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->disableLabel()
                            ->schema([
                                Forms\Components\TextInput::make('price')
                                    ->translateLabel()
                                    ->required(),
                                Forms\Components\TextInput::make('discount_price')
                                    ->translateLabel()
                                    ->required(),
                                Forms\Components\TextInput::make('sku')
                                    ->translateLabel()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('preparation_time')
                                    ->translateLabel(),
                                Forms\Components\Toggle::make('featured')
                                    ->label(__('Offer'))
                                    ->required(),
                                Forms\Components\Toggle::make('deliverable')
                                    ->translateLabel()
                                    ->required(),
                                Forms\Components\Toggle::make('is_active')
                                    ->translateLabel()
                                    ->required(),
                                Forms\Components\Toggle::make('with_option')
                                    ->translateLabel()
                                    ->required(),
                            ]),

                    ]),
                Forms\Components\Select::make('approval_status')
                    ->translateLabel()
                    ->required()
                    ->options([
                        'pending' =>  __('Pending'),
                        'accepted' =>  __('Accepted'),
                        'rejected' =>  __('Rejected'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('photo')
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('name_ar')
                    ->searchable()
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('categories.name_ar')
                    ->searchable()
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('price')
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('discount_price')
                    ->translateLabel(),

                Tables\Columns\IconColumn::make('is_active')
                    ->translateLabel()
                    ->boolean(),
                Tables\Columns\IconColumn::make('with_option')
                    ->translateLabel()
                    ->boolean(),
                Tables\Columns\TextColumn::make('approval_status')
                    ->translateLabel()->enum([
                        'pending' =>  __('Pending'),
                        'accepted' =>  __('Accepted'),
                        'rejected' =>  __('Rejected'),
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->translateLabel()
                    ->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->translateLabel()
                    ->dateTime(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->translateLabel()
                    ->dateTime(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('approval_status')
                    ->translateLabel()
                    ->options([
                        'pending' =>  __('Pending'),
                        'accepted' =>  __('Accepted'),
                        'rejected' =>  __('Rejected'),
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            RelationManagers\OptionGroupsRelationManager::class,
            VendorResource\RelationManagers\CategoriesRelationManager::class,
            RelationManagers\MenusRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            // 'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
            'view' => Pages\ViewProduct::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    protected static function getNavigationBadge(): ?string
    {
        $count = Product::where('approval_status', 'pending')->count();
        if ($count) {
            return $count;
        } else {
            return null;
        }
    }

    public static function getPluralLabel(): ?string
    {
        return __('Products');
    }

    public static function getLabel(): ?string
    {
        return __('Product');
    }
}
