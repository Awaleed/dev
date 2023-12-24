<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
    public function index()
    {
        return Menu::where('vendor_id', Auth::user()->vendor_id)->get();
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name_ar' => 'required|string',
                // 'name_en' => 'required|string',
                "is_active" => "required|boolean",
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                "message" => $this->readalbeError($validator),
            ], 400);
        }

        try {
            DB::beginTransaction();
            $menu = Menu::create(array_merge(
                [
                    'vendor_id' => Auth::user()->vendor_id,
                    'name_en' => '',
                ],
                $validator->validated(),
            ));

            DB::commit();

            return $menu;
        } catch (Exception $error) {
            DB::rollback();
            return response()->json([
                "message" => __('OTP failed to send to provided phone number'),
            ], 400);
        }
    }

    public function show(Menu $menu)
    {
        return $menu;
    }

    public function update(Request $request, Menu $menu)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name_ar' => 'nullable|string',
                'name_en' => 'nullable|string',
                "is_active" => "nullable|boolean",
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                "message" => $this->readalbeError($validator),
            ], 400);
        } elseif ($menu->vendor_id != Auth::user()->vendor_id) {
            return response()->json([
                "message" => __("Unauthorized Access"),
            ], 401);
        }

        try {
            DB::beginTransaction();
            $menu->update($validator->validated());
            DB::commit();
            return $menu->refresh();
        } catch (Exception $error) {
            DB::rollback();
            return response()->json(["message" => $error], 400);
            //, __('OTP failed to send to provided phone number'),
        }
    }

    public function destroy(Menu $menu)
    {
        if ($menu->vendor_id != Auth::user()->vendor_id) {
            return response()->json([
                "message" => __("Unauthorized Access"),
            ], 401);
        }

        return $menu->delete();
    }

    public function trash()
    {
        return Menu::onlyTrashed()->where('vendor_id', Auth::user()->vendor_id)->get();
    }

    public function restore($id)
    {
        $menu = Menu::onlyTrashed()->findOrFail($id);
        if ($menu->vendor_id != Auth::user()->vendor_id) {
            return response()->json([
                "message" => __("Unauthorized Access"),
            ], 401);
        }

        return $menu->restore();
    }
}
