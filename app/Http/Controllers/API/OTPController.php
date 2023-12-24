<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AppUser;
use App\Models\Otp;
use App\Services\OTPService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class OTPController extends Controller
{
    public function sendOTP(Request $request)
    {
        // if (!OTPService::inTestPhones($request->phone)) {
        //     return response()->json(["message" => __('OTP sent successfully')], 200);
        // }

        $user = AppUser::where('phone', $request->phone)->first();

        if ($request->is_login == 1 && !$user) {
            return response()->json(["message" => __('لست مسجل لدينا، قم بإنشاء حسابك الان')], 404);
        }

        // verification code
        $code = rand(1111, 9999);

        // create or update otp record
        Otp::updateOrCreate(
            ["phone" => $request->phone],
            ["code" => $code]
        );

        // send the verification code
        $message = 'كلمة السر لمرة واحدة من أسركم
        code: ' . $code . '';

        try {
            OTPService::sendOTP($request->phone, $message);
            return response()->json(["message" => __('OTP sent successfully')], 200);
        } catch (\Exception $ex) {
            return response()->json([
                "message" => __('OTP failed to send to provided phone number'),
            ], 400);
        }
    }

    public function verifyOTP(Request $request)
    {
        // if (OTPService::inTestPhones($request->phone)) {
        //     if ($request->is_login == 1) {
        //         return $this->getUser($request);
        //     } else {
        //         return response()->json(["message" => __('OTP sent successfully')], 200);
        //     }
        // }


        // $otp = Otp::where([
        //     ["phone", '=', $request->phone],
        //     // ["code",  '=', $request->code],
        // ])->first();


        // // invalid
        // if (empty($otp)) {
        //     return response()->json(["message" => __('Invalid OTP')], 400);
        // }

        // $otp->delete();
        if ($request->is_login == 1) {
            return $this->getUser($request);
        } else {
            return response()->json(["message" => __('OTP sent successfully')], 200);
        }
    }


    private function getUser(Request $request)
    {
        try {
            $user = AppUser::where('phone', $request->phone)->first();
            $authController = new AuthController();
            if (!$user) {
                return $authController->register($request);
            } else {
                return $authController->authObject($user);
            }
        } catch (Exception $error) {
            return response()->json([
                "message" => $error->getMessage()
            ], 500);
        }
    }
}
