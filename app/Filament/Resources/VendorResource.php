<?php

namespace App\Filament\Resources;

use App\Filament\Actions\ApproveBulkAction;
use App\Filament\Resources\VendorResource\Pages;
use App\Filament\Resources\VendorResource\RelationManagers;
use App\Models\Vendor;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VendorResource extends Resource
{
    protected static ?string $model = Vendor::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive';

    protected static ?int $navigationSort = 60;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Section::make(__('Photos'))
                    ->collapsible()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->disableLabel()
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('logo')
                                    ->translateLabel()
                                    ->collection('logo'),
                                SpatieMediaLibraryFileUpload::make('feature_image')
                                    ->translateLabel()
                                    ->collection('feature_image'),
                            ])

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
                        Forms\Components\Textarea::make('description')
                            ->translateLabel()
                            ->maxLength(65535),

                    ]),
                Forms\Components\Section::make(__('Location and contact info'))
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('address')
                            ->translateLabel()
                            ->maxLength(255),
                        Forms\Components\Grid::make(2)
                            ->disableLabel()
                            ->schema([
                                Forms\Components\TextInput::make('latitude')
                                    ->translateLabel()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('longitude')
                                    ->translateLabel()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('phone')
                                    ->translateLabel()
                                    ->tel()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->translateLabel()
                                    ->email()
                                    ->maxLength(255),
                            ]),

                    ]),
                Forms\Components\Section::make(__('Vendor Settings'))
                    ->collapsible()
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->disableLabel()
                            ->schema([
                                Forms\Components\Toggle::make('is_open')
                                    ->translateLabel()
                                    ->required(),
                                Forms\Components\Toggle::make('show_location')
                                    ->translateLabel()
                                    ->required(),
                                Forms\Components\Toggle::make('can_message_before_order')
                                    ->translateLabel()
                                    ->required(),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->disableLabel()
                            ->schema([
                                Forms\Components\TextInput::make('delivery_fee')
                                    ->translateLabel()
                                    ->numeric()
                                    ->required(),

                                Forms\Components\TextInput::make('min_order')
                                    ->translateLabel(),
                            ]),

                    ]),
                Forms\Components\Section::make(__('System Settings'))
                    ->collapsible()
                    ->schema([

                        Forms\Components\Grid::make(2)
                            ->disableLabel()
                            ->schema([
                                Forms\Components\TextInput::make('tax')
                                    ->translateLabel()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('commission')
                                    ->translateLabel()
                                    ->required(),

                                Forms\Components\Toggle::make('is_active')
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

                Tables\Columns\SpatieMediaLibraryImageColumn::make('logo')
                    ->translateLabel()
                    ->collection('logo'),
                // Tables\Columns\SpatieMediaLibraryImageColumn::make('feature_image')
                //     ->translateLabel()
                //     ->collection('feature_image'),
                Tables\Columns\TextColumn::make('name_ar')
                    ->searchable()
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->translateLabel(),
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
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                ApproveBulkAction::make(),
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }


    public static function getRelations(): array
    {
        return [
            RelationManagers\ManagersRelationManager::class,
            RelationManagers\BankAccountsRelationManager::class,
            RelationManagers\CategoriesRelationManager::class,
            RelationManagers\ProductsRelationManager::class,
            RelationManagers\OrdersRelationManager::class,
            RelationManagers\RatingsRelationManager::class,
            RelationManagers\MenusRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVendors::route('/'),
            'create' => Pages\CreateVendor::route('/create'),
            'view' => Pages\ViewVendor::route('/{record}'),
            'edit' => Pages\EditVendor::route('/{record}/edit'),
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
        $count = Vendor::where('approval_status', 'pending')->count();
        if ($count) {
            return $count;
        } else {
            return null;
        }
    }

    public static function getPluralLabel(): ?string
    {
        return __('Vendors');
    }

    public static function getLabel(): ?string
    {
        return __('Vendor');
    }
}
