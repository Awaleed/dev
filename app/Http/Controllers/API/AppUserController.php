<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\VendorResource;
use App\Models\AppUser;
use App\Models\Vendor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AppUserController extends Controller
{
    //
    public function index(Request $request)
    {
        $role = $request->role;
        $authUser = AppUser::find(Auth::id());
        if ($authUser->role("manager") && $role != "driver") {
            return response()->json([
                "message" => __("Not Authorised for the request"),
            ], 401);
        }

        if (!$authUser->hasAnyRole("manager", "admin")) {
            return response()->json([
                "message" => __("Not Authorised for the request"),
            ], 401);
        }

        $users = AppUser::with('roles')->when($role, function ($query) use ($role) {
            return $query->whereHas('roles', function ($query) use ($role) {
                return $query->where('name', $role);
            });
        })->when($role == "driver", function ($query) use ($request) {
            return $query->whereNull('vendor_id')
                ->when($request->vehicle_type_id, function ($query) use ($request) {
                    return $query->whereHas('vehicle', function ($q) use ($request) {
                        return $q->where('vehicle_type_id', $request->vehicle_type_id);
                    });
                }, function ($query) use ($request) {
                    return $query->whereDoesntHave('vehicle');
                });
        })->get();

        //
        if (!empty(Auth::user()->vendor_id) && $role == "driver") {
            $personalDrivers = AppUser::role('driver')->where('vendor_id', Auth::user()->vendor_id)->get();
            if (!empty($personalDrivers) && count($personalDrivers) > 0) {
                $users = $personalDrivers;
            }
        }

        return response()->json([
            "data" => $users,
        ], 200);
    }

    public function myProfile(Request $request)
    {
        // return AppUser::find(Auth::id());
        $user = AppUser::find(Auth::id());
        $vendor = Vendor::find($user->vendor_id);
        return response()->json([
            // "token" => $token,
            // "fb_token" => $this->fbToken($user),
            // "type" => "Bearer",
            // "message" => __("User login successful"),
            "user" => $user,
            "vendor" => $vendor ? VendorResource::make($vendor) : null,
        ]);
    }

    public function updateMyProfile(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'sometimes|string',
                'email' => 'sometimes|email|unique:app_users,email,' . Auth::id(),
                'phone' => 'sometimes|unique:app_users,phone,' . Auth::id(),
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                "message" => $this->readalbeError($validator),
            ], 400);
        }

        $user = AppUser::find(Auth::id());
        $vendor = Vendor::find($user->vendor_id);

        try {
            DB::beginTransaction();
            $user->fill($validator->validated());

            if ($request->hasFile("photo")) {
                $user->clearMediaCollection('profile');
                $user->addMediaFromRequest('photo')->toMediaCollection('profile');
            }

            $user->save();
            DB::commit();
            return response()->json([
                "user" => $user?->refresh(),
                "vendor" => $vendor?->refresh(),
            ]);
        } catch (Exception $error) {
            DB::rollback();
            return response()->json([
                "message" => $error //__('OTP failed to send to provided phone number'),
            ], 500);
        }
    }

    public function myTransactions()
    {
        return Auth::user()->wallet?->transactions ?? [];
    }
}
