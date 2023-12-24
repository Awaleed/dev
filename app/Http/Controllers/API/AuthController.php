<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\VendorResource;
use App\Models\AppUser;
use Illuminate\Support\Facades\Validator;
use App\Models\Vendor;
use App\Traits\FirebaseAuthTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    //traits
    use FirebaseAuthTrait;

    //
    public function login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'phone' => 'required|exists:app_users',
                // 'password' => 'required',
            ],
            $messages = [
                'phone.exists' => __('phone not associated with any account'),
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                "message" => $this->readalbeError($validator),
            ], 400);
        }

        //
        $user = AppUser::where('phone', $request->phone)->first();

        if (!empty($request->role) && !$user->hasAnyRole($request->role)) {
            return response()->json([
                "message" => __("Unauthorized Access. Please try with an authorized credentials")
            ], 401);
        } elseif (!$user->is_active) {
            return response()->json([
                "message" => __("Account is not active. Please contact us")
            ], 401);
        } elseif ($request->role == "manager" && empty($user->vendor_id)) {
            return response()->json([
                "message" => __("Manager is not assigned to a vendor. Please assign manager to vendor and try again")
            ], 401);
            // } elseif (Hash::check($request->password, $user->password)) {

            //     //generate tokens
            //     return $this->authObject($user);
        } else {
            return response()->json([
                "message" => __("Invalid credentials. Please change your password and try again")
            ], 401);
        }
    }


    public function register(Request $request)
    {
        $user_validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string',
                'type' => ['required', Rule::in(['client', 'vendor'])],
                'email' => 'required|email|unique:app_users',
                'phone' => 'required|unique:app_users',
            ],
            $messages = [
                'email.unique' => __('Email already associated with an account'),
                'phone.unique' => __('Phone already associated with an account'),
            ]
        );

        if ($user_validator->fails()) {
            return response()->json([
                "message" => $this->readalbeError($user_validator),
            ], 400);
        }

        if ($request->type == 'vendor') {
            $vendor_validator = Validator::make(
                $request->all(),
                [
                    "vendor_name_ar" => "required:string",
                    "vendor_name_en" => "nullable:string",
                    "vendor_email" => "required|email|unique:vendors,email",
                    "vendor_phone" => "required|unique:vendors,phone",
                ],
                $messages = [
                    'vendor_email.unique' => __('Email already associated with an account'),
                    'vendor_phone.unique' => __('Phone already associated with an account'),
                ]
            );

            if ($vendor_validator->fails()) {
                return response()->json(["message" => $this->readalbeError($vendor_validator)], 400);
            }
        }

        try {
            DB::beginTransaction();
            $user = null;
            if ($request->type == 'vendor') {
                $user = $this->vendorSignUp($request);
            } else {
                $user = $this->userSignUp($request);
            }
            DB::commit();
            return $this->authObject($user);
        } catch (Exception $error) {
            DB::rollback();
            return response()->json([
                "message" => $error->getMessage()
            ], 500);
        }
    }




    public function userSignUp(Request $request)
    {
        //
        $rawPhone = $request->phone;
        $phone = str_replace(' ', '', $rawPhone);
        logger("Phone", [$request->phone, $phone]);

        //
        $user = AppUser::where('phone', $phone)->first();
        if (!empty($user)) {
            throw new Exception(__("Account with phone already exists"), 1);
        }


        //
        $user = new AppUser();
        $user->name = $request->name ?? '';
        $user->email = $request->email ?? '';
        $user->phone = $phone;
        $user->country_code = $request->country_code ?? "";
        // $user->password = Hash::make($request->password ?? '');
        $user->is_active = true;
        $user->save();
        $user->syncRoles('client');

        //refer system is enabled
        $enableReferSystem = (bool) setting('enableReferSystem', "0");
        $referRewardAmount = (float) setting('referRewardAmount', "0");
        if ($enableReferSystem && !empty($request->code)) {
            //
            $referringUser = AppUser::where('code', $request->code)->first();
            if (!empty($referringUser)) {
                $referringUser->topupWallet($referRewardAmount);
            } else {
                throw new Exception(__("Invalid referral code"), 1);
            }
        }
        return $user;
    }

    public function vendorSignUp(Request $request)
    {
        //
        $phone = $request->phone;
        $vendorPhone = $request->vendor_phone;
        //
        $user = AppUser::where('phone', $phone)->first();
        if (!empty($user)) {
            throw new Exception(__("Account with phone already exists"), 1);
        }



        $user = $this->userSignUp($request);
        $user->syncRoles('manager');

        //create vendor
        $vendor = new Vendor();
        $vendor->name_ar = $request->vendor_name_ar;
        $vendor->name_en = $request->vendor_name_en ?? '';
        $vendor->email = $request->vendor_email;
        $vendor->phone = $vendorPhone;
        $vendor->is_active = false;
        $vendor->save();

        if ($request->vendor_document) {
            $vendor->clearMediaCollection("documents");
            $vendor->addMedia($request->vendor_document)->toMediaCollection("documents");
        }

        //assign manager to vendor
        $user->vendor_id = $vendor->id;
        $user->save();

        return $user;
    }


    //
    public function logout(Request $request)
    {
        $user = AppUser::find(Auth::id());
        if (!empty($user)) {
            if ($user->hasAnyRole('driver')) {
                $user->is_online = 0;
                $user->save();
            }
            Auth::logout();
        }
        return response()->json([
            "message" => "Logout successful"
        ]);
    }

    public function authObject($user)
    {
        if (!$user->is_active) {
            throw new Exception(__("User Account is inactive"), 1);
        }

        $user = AppUser::find($user->id);
        $vendor = Vendor::find($user->vendor_id);

        $token = $user->createToken($user->name)->accessToken;
        return response()->json([
            "token" => $token,
            "fb_token" => $this->fbToken($user),
            "type" => "Bearer",
            "message" => __("User login successful"),
            "user" => $user,
            "vendor" => $vendor ? VendorResource::make($vendor) : null,
        ]);
    }

    public function fbToken($user)
    {
        $uId = "user_id_" . $user->id . "";
        $firebaseAuth = $this->getFirebaseAuth();
        $customToken = $firebaseAuth->createCustomToken($uId);
        $customTokenString = $customToken->toString();
        return $customTokenString;
    }
}
