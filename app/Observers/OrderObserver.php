<?php

namespace App\Observers;

use App\Models\AppNotification;
use App\Models\AppUser;
use App\Models\Order;
use App\Models\Vendor;
use App\Services\OrderEarningService;
use App\Traits\FirebaseAuthTrait;
use App\Traits\FirebaseMessagingTrait;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class OrderObserver
{
    use FirebaseMessagingTrait, FirebaseAuthTrait;

    public function creating(Order $model)
    {
        $model->code = Str::random(10);
        $model->verification_code = Str::random(5);
        if (empty($model->user_id)) {
            $model->user_id = Auth::id();
        }
        $this->setOrderNumber($model);
    }

    public function created(Order $model)
    {
        $this->sendOrderStatusChangeNotification($model);
        // $this->sendOrderUpdateMail($model);
        // $this->autoMoveToReady($model);
        // $this->autoMoveToPreparing($model);
        // $this->clearAutoAssignment($model);

        $this->setOrderNumber($model);
    }

    public function updating(Order $model)
    {
        $this->setOrderNumber($model);
    }


    public function updated(Order $model)
    {
        if ($model->isDirty('driver_id')) {
            $this->sendOrderNotificationToDriver($model);
        }

        $model->refresh();
        $this->sendOrderStatusChangeNotification($model);
        $orderEarningService = new OrderEarningService();
        $orderEarningService->updateEarning($model);
        $model->refundUser();
        $this->setOrderNumber($model);

        // $this->autoMoveToReady($model);
        // $this->autoMoveToPreparing($model);
        // $this->clearAutoAssignment($model);
        // $this->clearFirestore($model);
    }

    //
    // public function sendOrderUpdateMail($model)
    // {
    //     //only delivered
    //     if (in_array($model->status, ['delivered'])) {
    //         //send mail
    //         try {
    //             \Mail::to($model->user->email)
    //                 ->cc([$model->vendor->email])
    //                 ->send(new OrderUpdateMail($model));
    //         } catch (\Exception $ex) {
    //             // logger("Mail Error", [$ex]);
    //             logger("Mail Error");
    //         }
    //     }
    // }

    // public function autoMoveToReady(Order $order)
    // {

    //     //
    //     $packageTypePricing = PackageTypePricing::where([
    //         "vendor_id" => $order->vendor_id,
    //         "package_type_id" => $order->package_type_id,
    //     ])->first();
    //     //
    //     if (
    //         in_array($order->status, ["pending", "preparing"])
    //         && ($packageTypePricing->auto_assignment ?? 0)
    //         && $order->payment_status == "successful"
    //     ) {
    //         // logger("Auto move to ready kicked in");
    //         $order->setStatus("ready");
    //     }
    // }

    // public function autoMoveToPreparing(Order $order)
    // {

    //     if (
    //         in_array($order->status, ["pending"])
    //         && ($order->vendor->auto_accept ?? 0)
    //         && $order->payment_status == "successful"
    //     ) {
    //         $order->setStatus("preparing");
    //     }
    // }

    // public function clearAutoAssignment(Order $order)
    // {
    //     //
    //     $order->refresh();
    //     if (in_array($order->status, ["ready", "enroute"])) {
    //         $autoAssignments = AutoAssignment::where('order_id', $order->number)->get();
    //         if (count($autoAssignments) > 0) {
    //             AutoAssignment::where('order_id', $order->number)->delete();
    //         }
    //     }
    // }

    // public function clearFirestore(Order $order)
    // {
    //     //
    //     $canClearFirestore = (bool) setting('clearFirestore', 1);
    //     //
    //     if (in_array($order->status, ['failed', 'cancelled', 'delivered', 'completed']) && $canClearFirestore) {
    //         $firestoreClient = $this->getFirebaseStoreClient();
    //         $firestoreClient->deleteDocument("orders/" . $order->code . "");
    //     }
    // }

    public function setOrderNumber(Order $order)
    {
        if (!$order->number && $order->status == 'pending') {
            $order->number = (Order::max('number') ?? 0) + 1;
            $order->save();
        }
    }


    //
    public function sendOrderStatusChangeNotification(Order $order)
    {
        if ($order->status == "created") return;
        //
        $pendingMsg =        __("Your order is pending");
        $preparingMsg =      __("Your order is now being prepared");
        $readyMsg =          __("Your order is now ready for delivery/pickup");
        $enrouteMsg =        __("Order #") . $order->number . __(" has been assigned to a delivery boy");
        $completedMsg =      __("Order #") . $order->number . __(" has been delivered");
        $cancelledMsg =      __("Order #") . $order->number . " " . __("cancelled");
        $failedMsg =         __("Trip failed");

        // managers message
        $managerPendingMsg = __("Order #") . $order->number . __(" has just been placed with you");
        $managerEnrouteMsg = __("Order #") . $order->number . __(" has been assigned to a delivery boy");
        $notificationTitle = __("Order Status updated");


        // 'pending','preparing','ready','enroute','delivered','failed','cancelled'
        if ($order->status == "pending") {
            $this->sendUserNotification($order->user_id, $notificationTitle, $pendingMsg, $order);
            $this->sendVendorNotification($order->vendor, $notificationTitle, $managerPendingMsg, $order);
        } else if ($order->status == "preparing") {
            $this->sendUserNotification($order->user_id, $notificationTitle, $preparingMsg, $order);
        } else if ($order->status == "ready") {
            $this->sendUserNotification($order->user_id, $notificationTitle, $readyMsg, $order);
        } else if ($order->status == "enroute") {
            $this->sendUserNotification($order->user_id, $notificationTitle, $enrouteMsg, $order);
        } else if ($order->status == "delivered") {
            $this->sendUserNotification($order->user_id, $notificationTitle, $completedMsg, $order);
            // vendor
            $this->sendVendorNotification($order->vendor, $notificationTitle, $completedMsg, $order);
            // driver
            if (!empty($order->driver_id)) {
                $this->sendUserNotification($order->driver_id, $notificationTitle, $completedMsg, $order);
            }
        } else if ($order->status == "failed") {
            $this->sendUserNotification($order->user_id, $notificationTitle, $failedMsg, $order);
        } else if ($order->status == "cancelled") {
            $this->sendUserNotification($order->user_id, $notificationTitle, $cancelledMsg, $order);
        } else if (!empty($order->status)) {
            $this->sendUserNotification($order->user_id, $notificationTitle, __("Order #") . $order->number . __(" has been ") . __($order->status) . "", $order);
        }
    }


    public function sendOrderNotificationToDriver(Order $order)
    {
        $locale = AppUser::find($order->driver_id)?->locale;

        //
        $this->sendUserNotification(
            $order->driver_id,
            __("Order Update", locale: $locale),
            __("Order #", locale: $locale) . $order->number . __(" has been assigned to you", locale: $locale),
            $order,
            false
        );
    }

    public function sendUserNotification(int $user_id, string $title, string $msg, Order $order)
    {
        $appNotification = new AppNotification();

        $appNotification->title_ar = $title;
        $appNotification->title_en = $title;
        $appNotification->text_ar = $msg;
        $appNotification->text_en = $msg;
        $appNotification->user_id = $user_id;
        $appNotification->save();

        $order->notifications()->save($appNotification);
    }

    public function sendVendorNotification(Vendor $vendor, string $title, string $msg, Order $order)
    {
        $appNotification = new AppNotification();

        $appNotification->title_ar = $title;
        $appNotification->title_en = $title;
        $appNotification->text_ar = $msg;
        $appNotification->text_en = $msg;

        $vendor->sendNotificationToMangers($appNotification, $modelToAttach = $order);
    }
}
