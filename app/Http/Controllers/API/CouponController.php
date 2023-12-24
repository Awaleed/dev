<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CouponController extends Controller
{
    public function index()
    {
        return Coupon::whereHas('vendors', fn ($q) => $q->where('id',  Auth::user()->vendor_id))->get();
    }



    public function store(Request $request)
    {
        // return $request->all();
        $validator = Validator::make(
            $request->all(),
            [
                'code'                  => 'required|string',
                'description'           => 'nullable|string',
                'percentage'            => 'required|boolean',
                'discount'              => 'required|numeric',
                'maximum_discount'      => 'nullable|numeric',
                'expires_on'            => 'required|date',
                'times'                 => 'nullable|integer',
                'times_per_user'        => 'nullable|integer',
                'exclude_discounted'    => 'required|boolean',
                'free_delivery'         => 'required|boolean',
                'is_active'             => 'required|boolean',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['message' => $this->readalbeError($validator)], 400);
        }

        try {
            DB::beginTransaction();
            $coupon = Coupon::create(array_merge(
                $validator->validated(),
                [
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ],
            ));
            $coupon->vendors()->sync(Auth::user()->vendor_id);
            $coupon->products()->sync($request->products_ids);

            DB::commit();
            return $coupon->refresh();
        } catch (Exception $error) {
            DB::rollback();
            return response()->json(['message' => $error], 400);
        }
    }


    public function show($id, Request $request)
    {
        $coupon =  Coupon::where('code', $id)->whereHas('vendors', fn ($q) => $q->where('id',  $request->vendor_id))->first();
        if ($coupon) {
            return $coupon;
        } else {
            return response('', 404);
        }
    }


    public function update(Request $request, Coupon $coupon)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'code'                  => 'nullable|string',
                'description'           => 'nullable|string',
                'percentage'            => 'nullable|boolean',
                'discount'              => 'nullable|numeric',
                'maximum_discount'      => 'nullable|numeric',
                'expires_on'            => 'nullable|date',
                'times'                 => 'nullable|integer',
                'times_per_user'        => 'nullable|integer',
                'exclude_discounted'    => 'nullable|boolean',
                'free_delivery'         => 'nullable|boolean',
                'is_active'             => 'nullable|boolean',
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'message' => $this->readalbeError($validator),
            ], 400);
        }

        try {
            DB::beginTransaction();
            $coupon->update(array_merge(
                $validator->validated(),
                [
                    'updated_by' => Auth::id(),
                ],
            ));

            if ($request->products_ids)
                $coupon->products()->sync($request->products_ids);

            DB::commit();
            return $coupon->refresh();
        } catch (Exception $error) {
            DB::rollback();
            return response()->json([
                'message' => $error, //__('OTP failed to send to provided phone number'),
            ], 400);
        }
    }

    public function destroy(Coupon $coupon)
    {
        return $coupon->delete();
    }
}
