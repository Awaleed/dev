<?php

namespace App\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Notifications\Notification;
use Filament\Forms;

use Filament\Pages\Actions\Action;
use Filament\Pages\Contracts\HasFormActions;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;

class PrivacyPolicy extends Page
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.custom-form';

    protected static ?int $navigationSort = 40;

    protected static function getNavigationGroup(): ?string
    {
        return __('System Settings');
    }

    protected function getTitle(): string
    {
        return __('Privacy policy');
    }

    protected static function getNavigationLabel(): string
    {
        return __('Privacy policy');
    }

    public function mount(): void
    {
        $this->form->fill([
            'content' => setting('privacyPolicy', ''),
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\RichEditor::make('content')
                ->disableLabel(),
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
            setting(['privacyPolicy' => $this->content])->save();
        } catch (Halt $exception) {
            return;
        }

        Notification::make()
            ->success()
            ->title(__('filament::resources/pages/edit-record.messages.saved'))
            ->send();
    }
}
