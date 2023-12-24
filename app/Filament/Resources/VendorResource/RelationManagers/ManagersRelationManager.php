<?php

namespace App\Filament\Resources\VendorResource\RelationManagers;

use App\Filament\Resources\AppUserResource;
use App\Filament\Resources\UserResource;
use App\Models\AppUser;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManagersRelationManager extends RelationManager
{
    protected static string $relationship = 'managers';

    protected static ?string $recordTitleAttribute = 'name_ar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name_ar')
                    ->required()
                    ->maxLength(255),
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
                Tables\Columns\TextColumn::make('phone')
                    ->translateLabel(),
                Tables\Columns\IconColumn::make('is_active')
                    ->translateLabel()
                    ->boolean(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->translateLabel(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->url(fn (AppUser $record): string => AppUserResource::getUrl('edit', ['record' => $record])),

                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRecordLabel(): string
    {
        return __('Employee');
    }

    public static function getTitle(): string
    {
        return __('Employees');
    }
}
