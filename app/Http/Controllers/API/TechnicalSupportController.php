<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\TechnicalSupport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TechnicalSupportController extends Controller
{
    public function store(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'type'  => 'required|string',
                'title' => 'required|string',
                'body'  => 'required|string',
            ]
        );

        if ($validator->fails()) {
            return response()->json(["message" => $this->readalbeError($validator)], 400);
        }

        try {
            TechnicalSupport::create(array_merge(
                $validator->validated(),
                ['user_id' => Auth::id()]
            ));
        } catch (\Exception $ex) {
            return $ex;
            return response()->json(["message" => __("No Favorite Found")], 400);
        }
    }
}
