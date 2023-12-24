<?php

namespace App\Filament\Resources\OptionGroupResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'options';

    protected static ?string $recordTitleAttribute = 'name_ar';

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
                Forms\Components\Textarea::make('description')
                    ->translateLabel()
                    ->maxLength(65535),
                Forms\Components\TextInput::make('price')
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
                // Tables\Columns\TextColumn::make('name_en')
                //     ->translateLabel(),
                Tables\Columns\TextColumn::make('description')
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('price')
                    ->translateLabel(),
                Tables\Columns\IconColumn::make('is_active')
                    ->translateLabel()
                    ->boolean(),
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
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }


    public static function getRecordLabel(): string
    {
        return __('Option');
    }

    public static function getTitle(): string
    {
        return __('Options');
    }
}
