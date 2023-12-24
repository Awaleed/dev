<?php

namespace App\Observers;

use App\Filament\Resources\PayoutResource;
use App\Models\AppNotification;
use App\Models\Payout;
use App\Models\User;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;

class PayoutObserver
{
    /**
     * Handle the Payout "created" event.
     *
     * @param  \App\Models\Payout  $payout
     * @return void
     */
    public function created(Payout $payout)
    {
        //
        //
        Notification::make()
            ->title(__('New payout from') . ' ' . $payout->vendor->name_ar)
            ->body($payout->amount  . ' ر.س')
            ->icon('heroicon-o-archive')
            ->iconColor('warning')
            ->actions([
                Action::make(__('View'))
                    ->button()
                    ->url(PayoutResource::getUrl('edit', ['record' => $payout]))
            ])
            ->sendToDatabase(User::all());

        $appNotification = new AppNotification();

        $appNotification->title_ar = 'تم انشاء طلب دفع';
        $appNotification->title_en = 'New payout Added';
        $appNotification->text_ar = 'الرقم المرجعي: ' . $payout->id . '
المبلغ: ' . $payout->amount . ' ر.س
التاريخ: ' . $payout->created_at;
        $appNotification->text_en = 'Your account is under review. OSRKM representative will review and activate the account in the meantime, you can edit your products and account details';

        $payout->vendor->sendNotificationToMangers($appNotification, $modelToAttach = $payout);
    }

    /**
     * Handle the Payout "updated" event.
     *
     * @param  \App\Models\Payout  $payout
     * @return void
     */
    public function updated(Payout $payout)
    {
        //
        $appNotification = new AppNotification();

        $appNotification->title_ar = 'تم تحديث طلب دفع رقم ' . $payout->id;
        $appNotification->title_en = 'New payout Added';
        $appNotification->text_ar = 'الرقم المرجعي: ' . $payout->id . '
المبلغ: ' . $payout->amount . ' ر.س
الحالة: ' . __(ucfirst($payout->status)) . '
تاريخ الانشاء: ' . $payout->created_at . '
تاريخ التحديث: ' . $payout->updated_at;
        $appNotification->text_en = 'Your account is under review. OSRKM representative will review and activate the account in the meantime, you can edit your products and account details';

        $payout->vendor->sendNotificationToMangers($appNotification, $modelToAttach = $payout);

        if ($payout->isDirty('status') && $payout->status == 'rejected') {
            $payout->refund();
        }
    }

    /**
     * Handle the Payout "deleted" event.
     *
     * @param  \App\Models\Payout  $payout
     * @return void
     */
    public function deleted(Payout $payout)
    {
        //
    }

    /**
     * Handle the Payout "restored" event.
     *
     * @param  \App\Models\Payout  $payout
     * @return void
     */
    public function restored(Payout $payout)
    {
        //
    }

    /**
     * Handle the Payout "force deleted" event.
     *
     * @param  \App\Models\Payout  $payout
     * @return void
     */
    public function forceDeleted(Payout $payout)
    {
        //
    }
}
