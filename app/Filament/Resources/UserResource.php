<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\BelongsToManyMultiSelect;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 10;

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
                                Forms\Components\TextInput::make('password')
                                    ->translateLabel()
                                    ->password()
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->translateLabel()
                                    ->email()
                                    ->maxLength(255),
                            ]),
                    ]),
                Forms\Components\Section::make(__('System Settings'))
                    ->collapsible()
                    ->schema([

                        Forms\Components\Toggle::make('is_active')
                            ->translateLabel()
                            ->required(),

                        Forms\Components\Select::make('roles')
                            ->label(__('Roles'))
                            ->multiple()
                            ->relationship('roles', 'name'),
                    ]),



            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('email')
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->translateLabel()
                    ->dateTime(),
                Tables\Columns\TextColumn::make('created_at')
                    ->translateLabel()
                    ->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->translateLabel()
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }


    protected static function getNavigationGroup(): ?string
    {
        return __('System Settings');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Employees');
    }

    public static function getLabel(): ?string
    {
        return __('Employees');
    }
}
