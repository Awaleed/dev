<?php

namespace App\Filament\Resources\VendorResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\HasManyRelationManager;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BankAccountsRelationManager extends RelationManager
{
    protected static string $relationship = 'bankAccounts';

    protected static ?string $recordTitleAttribute = 'iban';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('bank_name')
                    ->translateLabel()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('account_holder_name')
                    ->translateLabel()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('iban')
                    ->translateLabel()
                    ->required()
                    ->maxLength(255),
                // Forms\Components\Toggle::make('is_active')
                //     ->translateLabel()
                //     ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('bank_name')
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('account_holder_name')
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('iban')
                    ->translateLabel(),
                // Tables\Columns\IconColumn::make('is_active')
                //     ->translateLabel()
                //     ->boolean(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                // Tables\Actions\AssociateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Tables\Actions\DissociateAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\DissociateBulkAction::make(),
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRecordLabel(): string
    {
        return __('Bank Account');
    }

    public static function getTitle(): string
    {
        return __('Bank Accounts');
    }
}
