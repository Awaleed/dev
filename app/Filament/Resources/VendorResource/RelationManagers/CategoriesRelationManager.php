<?php

namespace App\Filament\Resources\VendorResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'categories';

    protected static ?string $recordTitleAttribute = 'name_ar';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('photo')
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('name_ar')
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('name_en')
                    ->translateLabel(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make(),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make(),
            ]);
    }

    public static function getRecordLabel(): string
    {
        return __('Category');
    }

    public static function getTitle(): string
    {
        return __('Categories');
    }
}
