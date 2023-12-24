<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\OptionGroup;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OptionGroupController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            ['product_id' => 'required|exists:products,id'],
            $messages = ['product_id.exists' => __('Product not associated with any account')]
        );

        if ($validator->fails()) {
            return response()->json(['message' => $this->readalbeError($validator)], 400);
        }

        return OptionGroup::where('product_id', $request->product_id)->get();
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name_ar' => 'required|string',
                // 'name_en' => 'required|string',
                'is_active' => 'required|boolean',
                'multiple' => 'required|boolean',
                'required' => 'required|boolean',
                'product_id' => 'required|exists:products,id',
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'message' => $this->readalbeError($validator),
            ], 400);
        }

        try {
            DB::beginTransaction();
            $model = OptionGroup::create(array_merge(
                $validator->validated(),
                ['name_en' => '']
            ));
            DB::commit();
            return OptionGroup::find($model->id);
        } catch (Exception $error) {
            DB::rollback();
            return response()->json([
                'message' => $error, //__('OTP failed to send to provided phone number'),
            ], 500);
        }
    }

    public function show(OptionGroup $optionGroup)
    {
        return $optionGroup;
    }

    public function update(Request $request, OptionGroup $optionGroup)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name_ar' => 'nullable|string',
                'name_en' => 'nullable|string',
                'is_active' => 'nullable|boolean',
                'multiple' => 'nullable|boolean',
                'required' => 'nullable|boolean',
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'message' => $this->readalbeError($validator),
            ], 400);
        }

        try {
            DB::beginTransaction();
            $optionGroup->update(
                $validator->validated()
            );
            DB::commit();
            return $optionGroup;
        } catch (Exception $error) {
            DB::rollback();
            return response()->json([
                'message' => $error, //__('OTP failed to send to provided phone number'),
            ], 500);
        }
    }

    public function destroy(OptionGroup $optionGroup)
    {
        // if ($optionGroup->vendor_id != Auth::user()->vendor_id) {
        //     return response()->json([
        //         'message' => __('Unauthorized Access'),
        //     ], 401);
        // }

        return $optionGroup->delete();
    }

    public function trash()
    {
        // return OptionGroup::onlyTrashed()->where('vendor_id', Auth::user()->vendor_id)->get();
    }

    public function restore($id)
    {
        $menu = OptionGroup::onlyTrashed()->findOrFail($id);
        // if ($menu->vendor_id != Auth::user()->vendor_id) {
        //     return response()->json([
        //         'message' => __('Unauthorized Access'),
        //     ], 401);
        // }

        return $menu->restore();
    }
}
