<?php

namespace App\Observers;

use App\Filament\Resources\ProductResource;
use App\Models\AppNotification;
use App\Models\Product;
use App\Models\User;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function created(Product $product)
    {
        //
        Notification::make()
            ->title(__('New product from') . ' ' . $product->vendor->name_ar)
            ->body($product->name_ar)
            ->icon('heroicon-o-archive')
            ->iconColor('warning')
            ->actions([
                Action::make(__('View'))
                    ->button()
                    ->url(ProductResource::getUrl('edit', ['record' => $product]))
            ])
            ->sendToDatabase(User::all());

        $appNotification = new AppNotification();

        $appNotification->title_ar = 'تم اضافة منتج جديد';
        $appNotification->title_en = 'New Product Added';
        $appNotification->text_ar = 'المنتج قيد المراجعة. سوف يقوم ممثل اسركم بمراجعة المنتج وتفعيله في غضون ذلك يمكنك اضافة خيارات المنتج';
        $appNotification->text_en = 'Your account is under review. OSRKM representative will review and activate the account in the meantime, you can edit your products and account details';

        $product->vendor->sendNotificationToMangers($appNotification, $modelToAttach = $product);
    }

    /**
     * Handle the Product "updated" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function updated(Product $product)
    {
        //

        if ($product->isDirty('approval_status')) {

            $appNotification = new AppNotification();
            $appNotification->title_ar = 'تحديث حالة المنتج';
            $appNotification->title_en = 'Welcome to OSRKM';

            if ($product->approval_status == 'accepted') {
                $appNotification->text_ar = 'تم قبول المنتج';
                $appNotification->text_en = 'Your account has been activated';
            } else if ($product->approval_status == 'rejected') {
                $appNotification->text_ar = 'تم رفض المنتج';
                $appNotification->text_en = 'Your account has been rejected';
            }

            $product->vendor->sendNotificationToMangers($appNotification, $modelToAttach = $product);
        }
    }

    /**
     * Handle the Product "deleted" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function deleted(Product $product)
    {
        //
    }

    /**
     * Handle the Product "restored" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function restored(Product $product)
    {
        //
    }

    /**
     * Handle the Product "force deleted" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function forceDeleted(Product $product)
    {
        //
    }
}
