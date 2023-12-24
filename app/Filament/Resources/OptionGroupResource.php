<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OptionGroupResource\Pages;
use App\Filament\Resources\OptionGroupResource\RelationManagers;
use App\Filament\Resources\OptionGroupResource\RelationManagers\OptionsRelationManager;
use App\Models\OptionGroup;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OptionGroupResource extends Resource
{
    protected static ?string $model = OptionGroup::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name_ar')
                    ->translateLabel()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('name_en')
                    ->translateLabel()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('multiple')
                    ->translateLabel()
                    ->required(),
                Forms\Components\Toggle::make('required')
                    ->translateLabel()
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->translateLabel()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name_ar')
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('name_en')
                    ->translateLabel(),
                Tables\Columns\IconColumn::make('multiple')
                    ->translateLabel()
                    ->boolean(),
                Tables\Columns\IconColumn::make('required')
                    ->translateLabel()
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->translateLabel()
                    ->boolean(),
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
            OptionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOptionGroups::route('/'),
            'create' => Pages\CreateOptionGroup::route('/create'),
            'edit' => Pages\EditOptionGroup::route('/{record}/edit'),
        ];
    }

    protected static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function getPluralLabel(): ?string
    {
        return __('Option Groups');
    }

    public static function getLabel(): ?string
    {
        return __('Option Group');
    }
}
