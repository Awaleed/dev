<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PayoutResource\Forms\PayoutDetails;
use App\Filament\Resources\TechnicalSupportResource\Pages;
use App\Filament\Resources\TechnicalSupportResource\RelationManagers;
use App\Models\TechnicalSupport;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TechnicalSupportResource extends Resource
{
    protected static ?string $model = TechnicalSupport::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-alt-2';

    protected static ?int $navigationSort = 50;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                PayoutDetails::make('details')
                    ->columnSpan('full'),

                Forms\Components\TextInput::make('id')
                    ->translateLabel()
                    ->disabled(),
                Forms\Components\TextInput::make('type')
                    ->translateLabel()
                    ->disabled(),
                Forms\Components\TextInput::make('title')
                    ->translateLabel()
                    ->disabled(),
                Forms\Components\TextInput::make('body')
                    ->columnSpan('full')
                    ->translateLabel()
                    ->disabled(),

                Forms\Components\Select::make('status')
                    ->translateLabel()
                    ->options([
                        'open' => __('Open ticket'),
                        'closed' => __('Closed ticket'),
                    ])
                    ->required(),

                Forms\Components\Textarea::make('replay')
                    ->translateLabel()
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
                    ->description(fn (TechnicalSupport $record) => $record->user->phone)
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('type')
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('title')
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('body')
                    ->translateLabel(),

                Tables\Columns\TextColumn::make('created_at')
                    ->translateLabel()
                    ->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->translateLabel()
                    ->dateTime(),

            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    protected static function getNavigationBadge(): ?string
    {
        $count = TechnicalSupport::where('status', 'open')->count();
        if ($count) {
            return $count;
        } else {
            return null;
        }
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTechnicalSupports::route('/'),
        ];
    }


    public static function getPluralLabel(): ?string
    {
        return __('Technical Supports');
    }

    public static function getLabel(): ?string
    {
        return __('Technical Support');
    }
}
