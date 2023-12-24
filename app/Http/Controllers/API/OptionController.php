<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Option;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OptionController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            ['option_group_id' => 'required|exists:option_groups,id'],
            $messages = ['option_group_id.exists' => __('option_group not associated with any account')]
        );

        if ($validator->fails()) {
            return response()->json(['message' => $this->readalbeError($validator)], 400);
        }

        return Option::where('option_group_id', $request->option_group_id)->get();
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                // "product_ids" => "required|array|min:1",
                // "product_ids.*" => "exists:products,id",
                'name_ar' => 'required|string',
                // 'name_en' => 'required|string',
                "price" => "required|numeric",
                "description" => "nullable|string",
                "is_active" => "required|boolean",
                "option_group_id" => "required|exists:option_groups,id",
                // "photo" => "sometimes|nullable|image|max:1024",
            ]
        );
        if ($validator->fails()) {
            return response()->json(["message" => $this->readalbeError($validator)], 400);
        }

        try {
            DB::beginTransaction();
            $model = Option::create(array_merge(
                $validator->validated(),
                ['name_en' => '']
            ));
            DB::commit();
            return Option::find($model->id);
        } catch (Exception $error) {
            DB::rollback();
            return response()->json(["message" => $error], 500);
        }
    }

    public function show(Option $option)
    {
        return $option;
    }

    public function update(Request $request, Option $option)
    {
        $validator = Validator::make(
            $request->all(),
            [
                // "product_ids" => "nullable|array",
                // "product_ids.*" => "exists:products,id",
                'name_ar' => 'nullable|string',
                'name_en' => 'nullable|string',
                "price" => "nullable|numeric",
                "description" => "nullable|string",
                "is_active" => "nullable|boolean",
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                "message" => $this->readalbeError($validator),
            ], 400);
        }
        try {
            DB::beginTransaction();
            $option->update($validator->validated());

            // if (!empty($request->product_ids)) {
            //     $option->products()->sync($request->product_ids);
            // }

            DB::commit();
            return $option;
        } catch (Exception $error) {
            DB::rollback();
            return response()->json([
                "message" => $error, //__('OTP failed to send to provided phone number'),
            ], 500);
        }
    }

    public function destroy(Option $option)
    {
        // if ($option->vendor_id != Auth::user()->vendor_id) {
        //     return response()->json([
        //         "message" => __("Unauthorized Access"),
        //     ], 401);
        // }

        return $option->delete();
    }

    public function trash()
    {
        return Option::onlyTrashed()->where('vendor_id', Auth::user()->vendor_id)->get();
    }

    public function restore($id)
    {
        $menu = Option::onlyTrashed()->findOrFail($id);
        if ($menu->vendor_id != Auth::user()->vendor_id) {
            return response()->json([
                "message" => __("Unauthorized Access"),
            ], 401);
        }

        return $menu->restore();
    }
}
