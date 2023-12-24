<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\VendorResource;
use App\Models\Order;
use App\Models\AppUser;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function vendorOrProduct(Request $request)
    {
        $vendors = Vendor::active()
            ->where(
                fn ($q) => $q
                    ->where('name_ar', 'like', '%' . $request->q . '%')
                    ->orWhere('name_en', 'like', '%' . $request->q . '%')
            )
            ->limit(15)
            ->get();

        $products = Product::active()
            ->where(
                fn ($q) => $q
                    ->where('name_ar', 'like', '%' . $request->q . '%')
                    ->orWhere('name_en', 'like', '%' . $request->q . '%')
            )
            ->limit(15)
            ->get();

        return [
            'products' => ProductResource::collection($products),
            'vendors' => VendorResource::collection($vendors)
        ];

        $q = $request->q;

        $fields = ['id', 'name_ar', 'name_en', 'created_at'];
        $vendors = Vendor::active()
            ->select(array_merge($fields, [DB::raw('"vendors" as source')]))
            ->when($q, function ($query) use ($q) {
                $query->where(function ($query) use ($q) {
                    $query
                        ->where('name_ar', 'like', '%' . $q . '%')
                        ->orWhere('name_en', 'like', '%' . $q . '%');
                });
            })
            ->orderBy('created_at', 'DESC');

        $data = Product::active()->select(array_merge($fields, [DB::raw('"products" as source')]))
            ->union($vendors)
            ->when($q, function ($query) use ($q) {
                $query->where(function ($query) use ($q) {
                    $query
                        ->where('name_ar', 'like', '%' . $q . '%')
                        ->orWhere('name_en', 'like', '%' . $q . '%');
                });
            })
            ->orderBy('created_at', 'DESC')
            ->paginate($this->perPage);

        $products = [];
        $vendors = [];

        foreach ($data->getCollection() as $value) {
            if ($value->source == 'vendors')
                array_push($vendors, $value->id);
            else if ($value->source == 'products')
                array_push($products, $value->id);
        }

        $vendors = Vendor::whereIn('id', $vendors)->select('*', DB::raw('"vendors" as source'))->get();
        $products = Product::whereIn('id', $products)->select('*', DB::raw('"products" as source'))->get();

        // $collection = (new Collection([...$products, ...$vendors]))->sortByDesc('created_at')->values();

        // $data->setCollection($collection);

        return ['products' => $products, 'vendors' => $vendors];

        // $data;
    }


    public function vendorOrProductAutocomplete(Request $request)
    {
        $q = $request->q;

        $fields = ['id', 'name_ar', 'name_en', 'created_at'];

        $vendors = Vendor::select(array_merge($fields, [DB::raw('"vendors" as source')]))
            ->when($q, function ($query) use ($q) {
                $query->where(function ($query) use ($q) {
                    $query
                        ->where('name_ar', 'like', $q . '%')
                        ->orWhere('name_en', 'like', $q . '%');
                });
            })
            ->orderBy('created_at', 'DESC');

        $data = Product::select(array_merge($fields, [DB::raw('"products" as source')]))
            ->union($vendors)
            ->when($q, function ($query) use ($q) {
                $query->where(function ($query) use ($q) {
                    $query
                        ->where('name_ar', 'like', $q . '%')
                        ->orWhere('name_en', 'like', $q . '%');
                });
            })
            ->orderBy('created_at', 'DESC')
            ->limit(15)
            ->get();


        $values = [];

        foreach ($data as $value) {
            array_push($values, $value->name_ar, $value->name_en);
        }
        $collection = (new Collection($values))->shuffle()->values();

        return array_unique($collection->toArray());
    }

    public function orderOrProduct(Request $request)
    {
        $products = Product::active()
            ->where(
                fn ($q) => $q
                    ->where('name_ar', 'like', '%' . $request->q . '%')
                    ->orWhere('name_en', 'like', '%' . $request->q . '%')
            )
            ->limit(15)
            ->get();

        $orders = Order::where(fn ($q) => $q->where('id', 'like', '%' . $request->q . '%'))
            ->limit(15)
            ->get();

        $users = AppUser::active()
            ->where(
                fn ($q) => $q
                    ->where('name', 'like', '%' . $request->q . '%')
                    ->orWhere('email', 'like', '%' . $request->q . '%')
                    ->orWhere('phone', 'like', '%' . $request->q . '%')
            )
            ->limit(15)
            ->get();

        return [
            'products' => ProductResource::collection($products),
            'orders' => OrderResource::collection($orders),
            'users' => $users,
        ];
    }


    public function orderOrProductAutocomplete(Request $request)
    {
        $q = $request->q;

        $fields = ['id', 'name', 'name_ar', 'name_en', 'created_at'];

        $vendors = AppUser::select(array_merge($fields, [DB::raw('"users" as source')]))
            ->when($q, function ($query) use ($q) {
                $query->where(function ($query) use ($q) {
                    $query
                        ->where('name', 'like', $q . '%');
                });
            })
            ->orderBy('created_at', 'DESC');

        $data = Product::select(array_merge($fields, [DB::raw('"products" as source')]))
            ->union($vendors)
            ->when($q, function ($query) use ($q) {
                $query->where(function ($query) use ($q) {
                    $query
                        ->where('name_ar', 'like', $q . '%')
                        ->orWhere('name_en', 'like', $q . '%');
                });
            })
            ->orderBy('created_at', 'DESC')
            ->limit(15)
            ->get();


        $values = [];

        foreach ($data as $value) {
            array_push($values, $value->name ?? $value->name_ar);
        }
        $collection = (new Collection($values))->shuffle()->values();

        return array_unique($collection->toArray());
    }
}
