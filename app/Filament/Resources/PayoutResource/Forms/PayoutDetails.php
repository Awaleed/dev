<?php

namespace App\Filament\Resources\PayoutResource\Forms;

use Filament\Forms;
use Illuminate\Database\Eloquent\Model;

class PayoutDetails extends Forms\Components\Field
{
    protected string $view = 'forms::components.group';

    public $relationship = null;
    private $hideVendor = false;

    public function relationship(string | callable $relationship): static
    {
        // $this->relationship = $relationship;
        return $this;
    }

    public function saveRelationships(): void
    {
        // $state = $this->getState();
        // $record = $this->getRecord();
        // $relationship = $record->{$this->getRelationship()}();

        // if ($address = $relationship->first()) {
        //     $address->update($state);
        // } else {
        //     $relationship->updateOrCreate($state);
        // }

        // $record->touch();
    }

    public function getChildComponents(): array
    {
        return [
            Forms\Components\Grid::make(2)
                ->schema([
                    Forms\Components\TextInput::make('user_name')
                        ->translateLabel()
                        ->disabled(),
                    Forms\Components\TextInput::make('user_phone')
                        ->translateLabel()
                        ->disabled(),
                    Forms\Components\TextInput::make('vendor_name')
                        ->translateLabel()
                        ->hidden($this->hideVendor)
                        ->disabled(),
                    Forms\Components\TextInput::make('vendor_phone')
                        ->translateLabel()
                        ->hidden($this->hideVendor)
                        ->disabled(),

                ]),
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(function (PayoutDetails $component, ?Model $record) {
            $this->hideVendor = empty($record?->vendor);
            $component->state([
                'user_name' => $record?->user->name,
                'vendor_name' => $record?->vendor?->name_ar,
                'user_phone' => $record?->user->phone,
                'vendor_phone' => $record?->vendor?->phone,
            ]);
        });

        $this->dehydrated(false);
    }

    public function getRelationship(): string
    {
        return $this->evaluate($this->relationship) ?? $this->getName();
    }
}
