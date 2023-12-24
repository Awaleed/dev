<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PayoutResource\Forms\PayoutDetails;
use App\Filament\Resources\PayoutResource\Pages;
use App\Models\Payout;
use Filament\Forms;
use Filament\Pages as FilamentPages;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class PayoutResource extends Resource
{
    protected static ?string $model = Payout::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-in';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                PayoutDetails::make('details')
                    ->columnSpan('full'),
                Forms\Components\TextInput::make('id')
                    ->translateLabel()
                    ->disabled(),

                Forms\Components\TextInput::make('amount')
                    ->translateLabel()
                    ->suffix('ر.س')
                    ->disabled(),

                Forms\Components\TextInput::make('bank_name')
                    ->translateLabel()
                    ->disabled(),
                Forms\Components\TextInput::make('account_holder_name')
                    ->translateLabel()
                    ->disabled(),
                Forms\Components\TextInput::make('iban')
                    ->columnSpan('full')
                    ->translateLabel()
                    ->disabled(),

                Forms\Components\Select::make('status')
                    ->translateLabel()
                    ->disabled(fn (Payout $record) => $record->status != 'pending')
                    ->options([
                        'accepted' => __('Accepted'),
                        'rejected' => __('Rejected'),
                    ])
                    ->required(),

                Forms\Components\Select::make('payout_method_id')
                    ->label(__('Payout method'))
                    ->disabled(fn (Payout $record) => $record->status != 'pending')
                    ->relationship('payout_method', 'name_ar')
                    ->required(),

                Forms\Components\Textarea::make('note')
                    ->translateLabel()
                    ->disabled(fn (Payout $record) => $record->status != 'pending')
                    ->columnSpan('full')
                    ->maxLength(65535),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('status')
                    ->translateLabel()
                    ->enum([
                        'pending' => __('Pending'),
                        'accepted' => __('Accepted'),
                        'rejected' => __('Rejected'),
                    ]),
                Tables\Columns\TextColumn::make('user.name')
                    ->description(fn (Payout $record) => $record->user->phone)
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('vendor.name_ar')
                    ->description(fn (Payout $record) => $record->vendor->phone)
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('payout_method.name')
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('amount')
                    ->suffix(' ر.س')
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('note')
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('bank_name')
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('account_holder_name')
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('iban')
                    ->translateLabel(),
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
            'index' => Pages\ManagePayouts::route('/'),
            'edit' => Pages\EditPayouts::route('/{record}/edit'),
        ];
    }

    protected static function getNavigationBadge(): ?string
    {
        $count = Payout::currentStatus('pending')->count();
        if ($count) {
            return $count;
        } else {
            return null;
        }
    }

    public static function getPluralLabel(): ?string
    {
        return __('Payouts');
    }

    public static function getLabel(): ?string
    {
        return __('Payout');
    }
}
