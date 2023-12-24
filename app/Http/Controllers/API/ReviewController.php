<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Order;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ReviewController extends Controller
{

    public function addReview(Request $request)
    {
        try {
            // order_code
            // type
            // order_rating
            // product_quality
            // delivery
            // vendor
            // note

            logger()->info('message', ['request', $request->all()]);
            $model = null;
            if ($request->type == 'vendor') {
                $model = Vendor::findOrFail($request->id);
                $model->rateOnce($request->rating, $request->note);
            } else  if ($request->type == 'product') {
                $model = Product::findOrFail($request->id);
                $model->rateOnce($request->rating, $request->note);
            } else  if ($request->type == 'order') {
                $order = Order::where('code', $request->order_code)->first();
                $order->rateOnce($request->order_rating, $request->note);

                foreach ($order->products as $product) {
                    $product->product->rateOnce($request->product_quality, $request->note);
                }

                $order->vendor?->rateOnce($request->vendor, $request->note);
                $order->driver?->rateOnce($request->delivery, $request->note);
            }
        } catch (\Exception $ex) {
            return response()->json([
                "message" => __("No Favorite Found")
            ], 400);
        }
    }

    public function getVendorReviews($id)
    {
        try {
            $vendor = Vendor::findOrFail($id);
            return $vendor->ratings()
                ->where('comment', '!=', '')
                ->with('user')
                ->latest()
                ->get();
        } catch (\Exception $ex) {
            return $ex;
            return response()->json([
                "message" => __("No Favorite Found")
            ], 400);
        }
    }
}
