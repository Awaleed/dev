<?php

namespace App\Filament\Pages;

use App\Models\AppNotification;
use App\Models\AppUser;
use App\Traits\FirebaseMessagingTrait;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use App\Models\User;
use Exception;
use Filament\Notifications\Notification;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Wizard;
use Filament\Pages\Actions\Action;
use Filament\Pages\Contracts\HasFormActions;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;

class SendNotification extends Page
{
    use HasPageShield;
    use FirebaseMessagingTrait;

    protected static ?string $navigationIcon = 'heroicon-o-chat-alt';

    protected static string $view = 'filament.pages.custom-form';

    protected static ?int $navigationSort = 80;

    protected function getTitle(): string
    {
        return __('Send notification');
    }

    protected static function getNavigationLabel(): string
    {
        return __('Send notification');
    }




    protected function getActions(): array
    {
        return [
            Action::make(__('Send notification'))
                ->label(__('Send notification'))
                ->action('submit'),
        ];
    }


    public function mount(): void
    {
        $this->form->fill([
            'roles' => [],
            'notification_title' => '',
            'notification_body' => '',
        ]);
    }


    protected function getFormSchema(): array
    {
        return [

            Select::make('roles')
                ->label(__('User type'))
                ->multiple()
                ->translateLabel()
                ->required()
                ->options([
                    'manager' => __('Manager'),
                    'client' => __('Client'),
                    'driver' => __('Driver'),
                    'employee' => __('Employee'),
                ]),
            Forms\Components\TextInput::make('notification_title')
                ->label(__('Title'))
                ->translateLabel()
                ->required()
                ->maxLength(255),
            Forms\Components\Textarea::make('notification_body')
                ->label(__('Body'))
                ->translateLabel()
                ->required()
                ->maxLength(3660),


        ];
    }



    public function submit(): void
    {



        // try {
        $roles = $this->roles;
        $notification_title = $this->notification_title;
        $notification_body = $this->notification_body;

        //
        $notificationData = [
            "title" => $notification_title,
            "body" => $notification_body,
        ];

        // map roles to Role with guard_name api

        // get all users with role
        $users = AppUser::role($roles)->select('id')->get();

        foreach ($users as $user) {
            $appNotification = new AppNotification();

            $appNotification->title_ar = $notification_title;
            $appNotification->title_en = $notification_title;
            $appNotification->text_ar = $notification_body;
            $appNotification->text_en = $notification_body;
            $appNotification->user_id = $user->id;
            $appNotification->save();

            // $order->notifications()->save($appNotification);
        }

        Notification::make()
            ->success()
            ->title(__('Notification sent'))
            ->send();

        //     Notification::make()
        //         ->error()
        //         ->title(__('Notification failed'))
        //         ->send();
        // }
    }
}
