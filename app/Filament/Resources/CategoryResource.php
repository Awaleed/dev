<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-view-grid';
    // heroicon-s-squares-2x2
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\SpatieMediaLibraryFileUpload::make('photo')
                    ->translateLabel(),
                Forms\Components\Fieldset::make('الاسم')
                    ->schema([
                        Forms\Components\TextInput::make('name_ar')
                            ->translateLabel()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('name_en')
                            ->translateLabel()
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Toggle::make('is_active')
                    ->translateLabel()
                    ->required(),
            ]);
    }

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
            RelationManagers\VendorsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }


    public static function getGloballySearchableAttributes(): array
    {
        return ['name_ar', 'name_en'];
    }

    // public static function getGlobalSearchResultDetails($record): array
    // {
    //     return [
    //         'Brand' => optional($record->brand)->name,
    //     ];
    // }

    public static function getPluralLabel(): ?string
    {
        return __('Categories');
    }

    public static function getLabel(): ?string
    {
        return __('Category');
    }
}
