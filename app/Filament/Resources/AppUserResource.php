<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppUserResource\Pages;
use App\Filament\Resources\AppUserResource\RelationManagers;
use App\Filament\Resources\VendorResource\RelationManagers\OrdersRelationManager;
use App\Models\AppUser;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AppUserResource extends Resource
{
    protected static ?string $model = AppUser::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 70;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Photos'))
                    ->collapsible()
                    ->schema([
                        Forms\Components\SpatieMediaLibraryFileUpload::make('photo')
                            ->translateLabel()
                            ->collection('profile'),
                    ]),
                Forms\Components\Section::make(__('Name'))
                    ->collapsible()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->disableLabel()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->translateLabel()
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('phone')
                                    ->translateLabel()
                                    ->tel()
                                    ->required()
                                    ->maxLength(255),
                            ]),
                        Forms\Components\TextInput::make('email')
                            ->translateLabel()
                            ->email()
                            ->maxLength(255),
                    ]),
                Forms\Components\Section::make(__('System Settings'))
                    ->collapsible()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->disableLabel()
                            ->schema([
                                Forms\Components\TextInput::make('commission')
                                    ->translateLabel(),
                            ]),
                        Forms\Components\Toggle::make('is_active')
                            ->translateLabel()
                            ->required(),

                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('photo')
                    ->translateLabel()
                    ->collection('profile'),
                Tables\Columns\TextColumn::make('name')
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('email')
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('phone')
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('commission')
                    ->translateLabel(),
                Tables\Columns\IconColumn::make('is_active')
                    ->translateLabel()
                    ->boolean(),
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
            OrdersRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppUsers::route('/'),
            'create' => Pages\CreateAppUser::route('/create'),
            'edit' => Pages\EditAppUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPluralLabel(): ?string
    {
        return __('App users');
    }

    public static function getLabel(): ?string
    {
        return __('App user');
    }
}
