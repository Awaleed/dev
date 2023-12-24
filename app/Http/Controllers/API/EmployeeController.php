<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Product;
use Propaganistas\LaravelPhone\PhoneNumber;
use App\Models\AppUser;
use App\Models\VendorType;
use Exception;
use Illuminate\Queue\Queue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['*']);
    }

    public function index()
    {
        return AppUser::role(['employee', 'driver'])->where('vendor_id', Auth::user()->vendor_id)->get();
    }


    public function show($id)
    {
        $user = AppUser::findOrFail($id);
        if ($user->vendor_id != Auth::user()->vendor_id) {
            return response()->json([
                "message" => __("Unauthorized Access"),
            ], 401);
        }
        return AppUser::findOrFail($id);
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string',
                'email' => 'nullable|email|unique:app_users',
                'phone' => 'required|unique:app_users',
            ],
            $messages = [
                'email.unique' => __('Email already associated with an account'),
                'phone.unique' => __('Phone already associated with an account'),
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                "message" => $this->readalbeError($validator),
            ], 400);
        }



        try {

            //
            // $phone = PhoneNumber::make($request->phone);
            // $rawPhone = PhoneNumber::make($request->phone, setting('countryCode', 'SA'))->formatNational();
            // $phone = str_replace(' ', '', $rawPhone);
            $phone = $request->phone;
            // logger("Phone", [$request->phone, $phone]);

            //
            $user = AppUser::where('phone', $phone)->first();
            if (!empty($user)) {
                throw new Exception(__("Account with phone already exists"), 1);
            }


            DB::beginTransaction();
            //
            $user = new AppUser();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $phone;
            $user->vendor_id = Auth::user()->vendor_id;
            $user->country_code = $request->country_code ?? "";
            // $user->password = Hash::make($request->password);
            $user->is_active = $request->is_active ?? 1;
            $user->save();


            $user->roles()->sync(Role::where('name', $request->role)->first()->id);

            DB::commit();
            return $user->refresh();
        } catch (Exception $error) {
            DB::rollback();
            return response()->json([
                "message" => $error->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        logger("Update", $request->all());
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'nullable|string',
                'email' => 'nullable|email|unique:app_users,email,' . $id,
                'phone' => 'nullable|unique:app_users,phone,' . $id,
                "is_active" => "nullable|boolean",
            ],
            $messages = [
                'email.unique' => __('Email already associated with an account'),
                'phone.unique' => __('Phone already associated with an account'),
            ]
        );
        $user = AppUser::findOrFail($id);

        if ($validator->fails()) {
            return response()->json([
                "message" => $this->readalbeError($validator),
            ], 400);
        } elseif ($user->vendor_id != Auth::user()->vendor_id) {
            return response()->json([
                "message" => __("Unauthorized Access"),
            ], 401);
        }


        try {
            DB::beginTransaction();
            //
            $user->update($validator->validated());
            //
            if (!empty($request->role)) {
                $user->roles()->sync(Role::where('name', $request->role)->first()->id);
            }

            DB::commit();
            //generate tokens
            return $user->refresh();
        } catch (Exception $error) {
            DB::rollback();
            return response()->json([
                "message" => $error->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        //

        try {
            DB::beginTransaction();
            AppUser::destroy($id);
            DB::commit();
            return response()->json([
                "message" => __("Product deleted successfully"),
            ]);
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json([
                "message" => $ex->getMessage()
            ], 400);
        }
    }
}
