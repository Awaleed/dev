<?php

namespace App\Traits;

use App\Models\Order;
use App\Models\User;
use App\Models\UserToken;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\WebPushConfig;

trait FirebaseMessagingTrait
{

    use FirebaseAuthTrait;


    //
    private function sendFirebaseNotification(
        $topic,
        $title,
        $body,
        array $data = null,
        bool $onlyData = true,
        string $channel_id = "basic_channel",
        bool $noSound = false
    ) {

        //getting firebase messaging
        $messaging = $this->getFirebaseMessaging();
        $messagePayload = [
            'topic' => $topic,
            'notification' => $onlyData ? null : [
                'title' => $title,
                'body' => $body,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                "channel_id" => $channel_id,
                "sound" => $noSound ? "" : "alert.aiff",
            ],
            'data' => $data,
        ];

        if (!$onlyData) {
            $messagePayload = [
                'topic' => $topic,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    "channel_id" => $channel_id,
                    "sound" => $noSound ? "" : "alert.aiff",
                ],
            ];
        } else {

            if (empty($data["title"])) {
                $data["title"] = $title;
                $data["body"] = $body;
            }
            $messagePayload = [
                'topic' => $topic,
                'data' => $data,
            ];
        }
        $message = CloudMessage::fromArray($messagePayload);

        //android configuration
        $androidConfig = [
            'ttl' => '3600s',
            'priority' => 'high',
            'data' => $data,
            'notification' => [
                'title' => $title,
                'body' => $body,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                "channel_id" => $channel_id,
                "sound" => $noSound ? "" : "alert",
            ],
        ];

        if ($onlyData) {
            if (empty($data["title"])) {
                $data["title"] = $title;
                $data["body"] = $body;
            }
            $androidConfig = [
                'ttl' => '3600s',
                'priority' => 'high',
                'data' => $data,
            ];
        }
        $config = AndroidConfig::fromArray($androidConfig);

        $message = $message->withAndroidConfig($config);
        $messaging->send($message);
    }

    private function sendFirebaseNotificationToTokens(array $tokens, $title, $body, array $data = null)
    {
        if (!empty($tokens)) {
            //getting firebase messaging
            $messaging = $this->getFirebaseMessaging();
            $message = CloudMessage::new();
            //
            $config = WebPushConfig::fromArray([
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                    // 'icon' => setting('websiteLogo', asset('images/logo.png')),
                ],
                // 'fcm_options' => [
                // 'link' => $data[0],
                // ],
            ]);
            //
            $message = $message->withWebPushConfig($config);
            $messaging->sendMulticast($message, $tokens);
        }
    }
}
