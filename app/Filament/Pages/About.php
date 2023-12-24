<?php

namespace App\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Notifications\Notification;
use Filament\Forms;

use Filament\Pages\Actions\Action;
use Filament\Pages\Contracts\HasFormActions;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;

class About extends Page
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.custom-form';

    protected static ?int $navigationSort = 20;

    protected static function getNavigationGroup(): ?string
    {
        return __('System Settings');
    }

    protected function getTitle(): string
    {
        return __('About the app');
    }

    protected static function getNavigationLabel(): string
    {
        return __('About the app');
    }

    public function mount(): void
    {
        $this->form->fill([
            'content' => setting('about', ''),
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
            setting(['about' => $this->content])->save();
        } catch (Halt $exception) {
            return;
        }

        Notification::make()
            ->success()
            ->title(__('filament::resources/pages/edit-record.messages.saved'))
            ->send();
    }
}
