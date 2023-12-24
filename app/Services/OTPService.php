<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use TaqnyatSms;

class OTPService
{
    static $testPhones = [
        // '0511111111',
        // '0502000022',
        // '0500000001',
        // '0544245003',
        // '0500000002',
        // '0500000003',
        // '0500000004',
        // '0500000022',
        // '0506168409',
        // '0555820068',
    ];




    static function inTestPhones($phone)
    {
        return true;
        return in_array($phone, OTPService::$testPhones);
    }

    static function sendOTP($phone, $message)
    {
        return;
        if (in_array($phone, static::$testPhones)) {
        }

        $search = '/' . preg_quote(0, '/') . '/';
        // $nPhone = preg_replace($search, 966, $phone, 1);
        // logger()->info($nPhone);

        $bearer = '9aa9f8c6197ad7c4f6ee48838e6a73d1';
        $taqnyt = new TaqnyatSms($bearer);

        $body = $message;
        $recipients = [$phone];
        $sender = 'OSRKM';

        $message = $taqnyt->sendMsg($body, $recipients, $sender);
        logger()->info($message);
    }
}
