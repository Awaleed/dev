<?php

namespace App\Observers;

use App\Models\AppNotification;
use App\Services\OTPService;
use App\Traits\FirebaseMessagingTrait;

class AppNotificationObserver
{
    use FirebaseMessagingTrait;

    public $afterCommit = true;


    public function created(AppNotification $model)
    {
        logger('AppNotification created', ['notification' => $model]);

        $this->sendAppNotification($model);
        $this->sendSMS($model);
    }

    function sendAppNotification(AppNotification $notification)
    {
        $this->sendFirebaseNotification(
            '' . $notification->user_id,
            $notification->title_ar,
            $notification->text_ar,
            [],
            false,
        );
    }

    public function sendSMS(AppNotification $notification)
    {
        return null;
        OTPService::sendOTP($notification->user->phone, $notification->text_ar);
    }
}
