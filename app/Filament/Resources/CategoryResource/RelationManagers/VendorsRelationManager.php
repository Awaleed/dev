<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use App\Filament\Resources\VendorResource;
use App\Models\Vendor;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VendorsRelationManager extends RelationManager
{
    protected static string $relationship = 'vendors';

    protected static ?string $recordTitleAttribute = 'name_ar';


    public static function getRecordLabel(): string
    {
        return __('Vendor');
    }

    public static function getTitle(): string
    {
        return __('Vendors');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('logo')
                    ->translateLabel()
                    ->collection('logo'),
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
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn (Vendor $record): string => VendorResource::getUrl('view', ['record' => $record])),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make(),
            ]);
    }
}
