<?php

namespace App\Observers;

use App\Filament\Resources\VendorResource;
use App\Models\AppNotification;
use App\Models\User;
use App\Models\Vendor;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;


class VendorObserver
{
    /**
     * Handle the Vendor "created" event.
     *
     * @param  \App\Models\Vendor  $vendor
     * @return void
     */
    public function created(Vendor $vendor)
    {
        //
        Notification::make()
            ->title(__('New vendor'))
            ->body($vendor->name_ar)
            ->icon('heroicon-o-archive')
            ->iconColor('warning')
            ->actions([
                Action::make('view')
                    ->translateLabel()
                    ->button()
                    ->url(VendorResource::getUrl('edit', ['record' => $vendor]))
            ])
            ->sendToDatabase(User::all());

        $appNotification = new AppNotification();

        $appNotification->title_ar = 'مرحبا بكم في اسركم';
        $appNotification->title_en = 'Welcome to OSRKM';
        $appNotification->text_ar = 'حساب الاسرة المنتجة قيد المراجعة. سوف يقوم ممثل اسركم بمراجعة الحساب وتفعيله في غضون ذلك ، يمكنك تعديل منتجاتك وتفاصيل حسابك';
        $appNotification->text_en = 'Your account is under review. OSRKM representative will review and activate the account in the meantime, you can edit your products and account details';

        $vendor->sendNotificationToMangers($appNotification, $modelToAttach = $vendor);
    }

    /**
     * Handle the Vendor "updated" event.
     *
     * @param  \App\Models\Vendor  $vendor
     * @return void
     */
    public function updated(Vendor $vendor)
    {
        if ($vendor->isDirty('approval_status')) {

            $appNotification = new AppNotification();

            if ($vendor->approval_status == 'accepted') {
                $appNotification->title_ar = 'مرحبا بكم في اسركم';
                $appNotification->title_en = 'Welcome to OSRKM';
                $appNotification->text_ar = 'تم تفعيل حسابكم';
                $appNotification->text_en = 'Your account has been activated';
            } else if ($vendor->approval_status == 'rejected') {
                $appNotification->title_ar = 'تم رفض حسابكم';
                $appNotification->title_en = 'Your account has been rejected';
                $appNotification->text_ar = 'تم رفض حسابكم';
                $appNotification->text_en = 'Your account has been rejected';
            }

            $vendor->sendNotificationToMangers($appNotification, $modelToAttach = $vendor);
        }
    }

    /**
     * Handle the Vendor "deleted" event.
     *
     * @param  \App\Models\Vendor  $vendor
     * @return void
     */
    public function deleted(Vendor $vendor)
    {
        //
    }

    /**
     * Handle the Vendor "restored" event.
     *
     * @param  \App\Models\Vendor  $vendor
     * @return void
     */
    public function restored(Vendor $vendor)
    {
        //
    }

    /**
     * Handle the Vendor "force deleted" event.
     *
     * @param  \App\Models\Vendor  $vendor
     * @return void
     */
    public function forceDeleted(Vendor $vendor)
    {
        //
    }
}
