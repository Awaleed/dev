<?php

namespace App\Filament\Resources\VendorResource\RelationManagers;

use App\Models\Product;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    protected static ?string $recordTitleAttribute = 'name_ar';


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
                // Tables\Columns\IconColumn::make('with_option')
                //     ->translateLabel()
                //     ->boolean(),
                Tables\Columns\TextColumn::make('approval_status')
                    ->translateLabel()->enum([
                        'pending' =>  __('Pending'),
                        'accepted' =>  __('Accepted'),
                        'rejected' =>  __('Rejected'),
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('approval_status')
                    ->translateLabel()
                    ->options([
                        'pending' =>  __('Pending'),
                        'accepted' =>  __('Accepted'),
                        'rejected' =>  __('Rejected'),
                    ]),
            ])
            ->headerActions([])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->url(fn (Product $record): string => route('filament.resources.products.edit', $record)),
            ])
            ->bulkActions([]);
    }

    public static function getRecordLabel(): string
    {
        return __('Product');
    }

    public static function getTitle(): string
    {
        return __('Products');
    }
}
