<?php

namespace App\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Notifications\Notification;
use Filament\Forms;

use Filament\Pages\Actions\Action;
use Filament\Pages\Contracts\HasFormActions;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;

class SystemSettings extends Page
{

    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static string $view = 'filament.pages.custom-form';

    protected static ?int $navigationSort = 0;

    protected static function getNavigationGroup(): ?string
    {
        return __('System Settings');
    }

    protected function getTitle(): string
    {
        return __('System Settings');
    }

    protected static function getNavigationLabel(): string
    {
        return __('System Settings');
    }

    public function mount(): void
    {
        $this->form->fill([
            'enableReferSystem'     => setting('enableReferSystem', ''),
            'referRewardAmount'     => setting('referRewardAmount', ''),
            // 'enableDriverWallet'    => setting('enableDriverWallet', ''),
            // 'driverWalletRequired'  => setting('driverWalletRequired', ''),
            'defaultVendorRating'   => setting('defaultVendorRating', ''),
            'clearFirestore'        => setting('clearFirestore', ''),
            'vendorEarningEnabled'  => setting('vendorEarningEnabled', ''),
            'vendorsCommission'     => setting('vendorsCommission', ''),
            // 'driversCommission'     => setting('driversCommission', ''),
            // 'serviceKeyPath'        => setting('serviceKeyPath', ''),
            // 'projectId'             => setting('projectId', ''),
            // 'websiteLogo'           => setting('websiteLogo', ''),
            // 'minScheduledTime'      => setting('minScheduledTime', ''),
        ]);
    }

    protected function getFormSchema(): array
    {
        return [

            Forms\Components\Section::make(__('Financial settings'))
                ->collapsible()
                ->schema([
                    Forms\Components\Group::make()
                        ->columns(2)
                        ->schema([
                            Forms\Components\TextInput::make('vendorsCommission')
                                ->required()
                                ->integer()
                                ->minValue(0)
                                ->maxValue(100)
                                ->label(__('Vendors commission') . ' (%)'),
                            // Forms\Components\Toggle::make('vendorEarningEnabled')
                            //     ->required()
                            //     ->translateLabel(),
                        ]),
                ]),
            Forms\Components\Section::make(__('General settings'))
                ->collapsible()
                ->schema([
                    Forms\Components\Group::make()
                        ->columns(2)
                        ->schema([
                            Forms\Components\TextInput::make('defaultVendorRating')
                                ->required()
                                ->integer()
                                ->minValue(0)
                                ->maxValue(5)
                                ->translateLabel(),
                            Forms\Components\Toggle::make('clearFirestore')
                                ->required()
                                ->translateLabel(),

                        ]),
                ]),
        ];
    }

    protected function getActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament::resources/pages/edit-record.form.actions.save.label'))
                ->action('submit')
                ->keyBindings(['mod+s']),
        ];
    }

    public function submit(): void
    {
        try {
            setting([
                'enableReferSystem' => $this->enableReferSystem,
                'referRewardAmount' => $this->referRewardAmount,
                'defaultVendorRating' => $this->defaultVendorRating,
                'clearFirestore' => $this->clearFirestore,
                'vendorEarningEnabled' => $this->vendorEarningEnabled,
                'vendorsCommission' => $this->vendorsCommission,
            ])->save();
        } catch (Halt $exception) {
            return;
        }

        Notification::make()
            ->success()
            ->title(__('filament::resources/pages/edit-record.messages.saved'))
            ->send();
    }
}
