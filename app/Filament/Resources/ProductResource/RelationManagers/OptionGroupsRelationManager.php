<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use App\Models\OptionGroup;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OptionGroupsRelationManager extends RelationManager
{
    protected static string $relationship = 'option_groups';

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
                // Tables\Columns\TextColumn::make('name_en')
                //     ->translateLabel(),
                Tables\Columns\TextColumn::make('options.name_ar')
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
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->url(fn (OptionGroup $record): string => route('filament.resources.option-groups.edit', $record)),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRecordLabel(): string
    {
        return __('Option Group');
    }

    public static function getTitle(): string
    {
        return __('Option Groups');
    }
}
