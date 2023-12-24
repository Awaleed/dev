<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\FavoriteResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\VendorResource;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'type' => ['required', Rule::in(['product', 'vendor'])],
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                "message" => $this->readalbeError($validator),
            ], 400);
        }

        $favorites = Favorite::where('user_id', "=", Auth::id());
        if ($request->type == 'product') {
            $favorites
                ->where('model_name', 'App\Models\Product')
                ->with('product')
                ->whereHas('product', fn ($q) => $q->active());
        } else {
            $favorites
                ->where('model_name', 'App\Models\Vendor')
                ->with('vendor')
                ->whereHas('vendor', fn ($q) => $q->active());
        }

        return FavoriteResource::collection($favorites->paginate($this->perPage));
    }

    public function toggleFavorite(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'id' => 'required|integer',
                'type' => ['required', Rule::in(['product', 'vendor'])],
            ]
        );

        if ($validator->fails()) {
            return response()->json(["message" => $this->readalbeError($validator)], 400);
        }

        try {
            $model = Favorite::where('user_id', Auth::id())->where('model_id', $request->id)->where('model_name', ($request->type == 'product' ? 'App\Models\Product' : 'App\Models\Vendor'))->first();
            if ($model) {
                $model->delete();
                return false;
            }

            $model = new Favorite();
            $model->user_id = Auth::id();
            $model->model_id =  $request->id;
            $model->model_name =  $request->type == 'product' ? 'App\Models\Product' : 'App\Models\Vendor';
            $model->save();

            return true;
        } catch (\Exception $ex) {
            return $ex;
            return response()->json([
                "message" => __("No Favorite Found")
            ], 400);
        }
    }
}
