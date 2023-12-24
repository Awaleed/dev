<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        return Category::active()->get();
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                "name" => "required|string",
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
            $category = new Category();
            $category->name = $request->name;
            $category->is_active = $request->is_active;
            $category->save();

            // if ($request->photo) {
            //     $category->clearMediaCollection();
            //     $category
            //         ->addMedia($request->photo->getRealPath())
            //         ->toMediaCollection();
            // }

            DB::commit();

            return $category;
        } catch (Exception $error) {
            DB::rollback();
            return response()->json([
                "message" => __('OTP failed to send to provided phone number'),
            ], 400);
        }
    }


    public function show(Category $category)
    {
        return $category;
    }

    public function update(Request $request, Category $category)
    {
        $validator = Validator::make(
            $request->all(),
            [
                "name" => "nullable|string",
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
            $category->name = $request->name ?? $category->name;
            $category->is_active = $request->is_active ?? $category->is_active;
            $category->save();
            DB::commit();

            return $category;
        } catch (Exception $error) {
            DB::rollback();
            return response()->json([
                "message" => __('OTP failed to send to provided phone number'),
            ], 400);
        }
    }

    public function destroy(Category $category)
    {
        return $category->delete();
    }
}
