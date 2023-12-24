<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\MenuResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\ProductResource;
use App\Models\Menu;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Product;
use App\Models\AppUser;
use App\Models\VendorType;
use App\Services\LocationDetailsService;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VendorController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:api', ['only' => ['store']]);
    }

    public function index(Request $request)
    {

        //
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $categories_ids = $request->categories_ids;

        // all, bestDelivery, offers, delivery, fastest
        $type = $request->type;
        switch ($type) {
            case 'bestDelivery':
                return [];
        }

        //the rest
        $vendors = Vendor::active()
            ->when(
                $categories_ids,
                fn ($q) => $q->whereHas(
                    'categories',
                    fn ($q) => $q->whereIn('id', $categories_ids)
                )
            )
            // ->when($request->type == "top", function ($query) {
            //     return $query->withCount('sales')->orderBy('sales_count', 'DESC');
            // })
            // ->when($request->type == "you", function ($query) {
            //     return $query->inRandomOrder();
            // })
            // ->when($request->type == "rated", function ($query) {
            //     return $query->orderByPowerJoinsAvg('ratings.rating', 'desc');
            // })
            // ->when($oldVendorType == "package", function ($query) use ($parcelVendorTypeId) {
            //     return $query->where('vendor_type_id', $parcelVendorTypeId);
            // })
            // ->when($vendorTypeId, function ($query) use ($vendorTypeId) {
            //     return $query->where('vendor_type_id', $vendorTypeId);
            // })
            // ->when($request->package_type_id, function ($query) use ($request) {
            //     return $query->with(
            //         [
            //             'cities' => function ($query) {
            //                 $query->where('is_active', 1);
            //             },
            //             'states'  => function ($query) {
            //                 $query->where('is_active', 1);
            //             },
            //             'countries'  => function ($query) {
            //                 $query->where('is_active', 1);
            //             },
            //         ]
            //     )
            //         ->withAndWhereHas('package_types_pricing', function ($query) use ($request) {
            //             $query->where('package_type_id', $request->package_type_id);
            //         });
            // })
            ->when(
                $latitude,
                fn ($query) => $query->distance($latitude, $longitude)
                    ->orderBy('distance', 'ASC')
            )
            ->whereNotNull('address')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with(['categories'])
            ->paginate($this->perPage);

        return $vendors;
    }


    public function show(Request $request, $id)
    {
        $latitude = $request->latitude;
        $longitude = $request->longitude;

        return  Vendor::with('categories')->when(
            $latitude && $longitude,
            fn ($query) => $query->distance($latitude, $longitude)
        )->findOrFail($id);
        try {
            if (($request->type ?? "") == "small") {
                $vendor = Vendor::with(['menus' => function ($query) {
                    return $query->where('is_active', 1);
                }, 'categories.sub_categories'])->findorfail($id);
            } elseif (($request->type ?? "") == "brief") {
                $vendor = Vendor::findorfail($id);
            } else {
                $vendorId = $id;
                $vendor = Vendor::with(['menus.products' => function ($query) use ($vendorId) {
                    return $query->where('is_active', 1)->where('vendor_id', $vendorId);
                }, 'categories.sub_categories.products' => function ($query) use ($vendorId) {
                    return $query->where('is_active', 1)->where('vendor_id', $vendorId);
                }])->findorfail($id);
            }
            return $vendor->with('products.categories');
        } catch (\Exception $ex) {
            return response()->json([
                "message" => $ex->getMessage() ?? __("No Vendor Found")
            ], 400);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string',
                'description' => 'required|string',
                // 'email' => 'required|email|unique:users',
                // 'phone' => 'phone:' . setting('countryCode', 'SA') . '|unique:users',
                // 'password' => 'required',
            ],
            $messages = [
                // 'email.unique' => __('Email already associated with an account'),
                // 'phone.unique' => __('Phone already associated with an account'),
            ]
        );

        $user = AppUser::find(Auth::id());
        // TRANSLATE: Add Translation
        if ($validator->fails()) {
            return response()->json([
                "message" => $this->readalbeError($validator),
            ], 400);
        } elseif (!$user->hasRole('manager')) {
            return response()->json([
                "message" => __("Unauthorized Access"),
            ], 401);
        } elseif ($user->vendor_id) {
            return response()->json([
                "message" => __("Unauthorized Access"),
            ], 400);
        }


        try {
            DB::beginTransaction();
            //
            $vendor = new Vendor();
            $vendor->name = $request->name;
            $vendor->description = $request->description;
            $vendor->creator_id = $user->id;
            // $vendor->email = $request->email;
            // $vendor->phone = $phone;
            // $vendor->country_code = $request->country_code ?? "";
            // $vendor->password = Hash::make($request->password);
            // $vendor->is_active = true;
            $vendor->save();


            $user->vendor_id = $vendor->id;
            $user->save();



            DB::commit();

            return  $vendor;
        } catch (Exception $error) {
            DB::rollback();
            return response()->json([
                "message" => $error->getMessage()
            ], 500);
        }
    }



    public function update(Request $request, $id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                "name_ar" => "sometimes|string",
                "description" => "sometimes|string",
                "phone" => "sometimes|unique:vendors,phone," . $id,
                "email" => "sometimes|email|unique:vendors,email," . $id,
                "delivery_fee" => "sometimes",
                "min_order" => "sometimes",
                "latitude" => "sometimes|string",
                "longitude" => "sometimes|string",
                "is_open" => "sometimes|boolean",
                'categories_ids' => 'sometimes|array',
                'categories_ids.*' => 'exists:categories,id',
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                "message" => $this->readalbeError($validator),
            ], 400);
        }

        $vendor = Vendor::findOrFail($id);
        try {
            DB::beginTransaction();
            $vendor->update($request->all());
            if (
                $request->latitude &&
                $request->longitude
            ) {
                $locDetails = LocationDetailsService::get(
                    $request->latitude,
                    $request->longitude
                );
                $vendor->update(['address' => $locDetails['label']]);
            }

            $vendor->save();

            if ($request->categories_ids) {
                $vendor->categories()->sync($request->categories_ids);
            }

            if ($request->hasFile('logo')) {
                $vendor->clearMediaCollection('logo');
                $vendor
                    ->addMediaFromRequest('logo')
                    ->toMediaCollection('logo');
            }

            if ($request->hasFile('feature_image')) {
                $vendor->clearMediaCollection('feature_image');
                $vendor
                    ->addMediaFromRequest('feature_image')
                    ->toMediaCollection("feature_image");
            }

            DB::commit();
            return Vendor::findOrFail($id);
        } catch (Exception $error) {
            DB::rollback();
            logger('$error ' . $error);
            return response()->json([
                "message" => $error //__('OTP failed to send to provided phone number'),
            ], 502);
        }
    }

    public function toggleVendorAvailablity(Request $request, $id)
    {
        if ((auth()->user()->vendor_id ?? null) != $id) {
            return response()->json([
                "message" => __("Unauthorized Access")
            ], 400);
        }

        try {
            $vendor = Vendor::findorfail($id);
            $vendor->is_open = !$vendor->is_open;
            $vendor->save();

            return response()->json([
                "vendor" => $vendor,
                "message" => __("Status Updated Successfully"),
            ], 200);
        } catch (\Exception $ex) {
            return response()->json([
                "message" => $ex->getMessage() ?? __("No Vendor Found")
            ], 400);
        }
    }

    public function fullDeatils(Request $request, $id)
    {
        if ((auth()->user()->vendor_id ?? null) != $id) {
            return response()->json([
                "message" => __("Unauthorized Access")
            ], 400);
        }

        try {
            $vendor = Vendor::with('earning', 'menus')->withCount('sales')->findorfail($id);
            $weeklyReport = $this->ordersChart($vendor);
            return response()->json([
                "vendor" => $vendor,
                "total_earnig" => $vendor->earning->amount ?? 0.00,
                "total_orders" => $vendor->sales_count,
                "report" => $weeklyReport,
            ], 200);
        } catch (\Exception $ex) {
            return response()->json([
                "message" => $ex->getMessage() ?? __("No Vendor Found")
            ], 400);
        }
    }

    public function ordersChart($vendor)
    {
        $report = [];
        for ($loop = 0; $loop < 7; $loop++) {
            $date = Carbon::now()->startOfWeek()->addDays($loop);
            $formattedDate = $date->format("D");
            $data = Order::where('vendor_id', $vendor->id)->whereDate("created_at", $date)->count();

            array_push($report, ["date" => $formattedDate, "value" => $data]);
        }

        return $report;
    }

    public function products(Request $request, $id)
    {
        $q = Product::with("menus")->whereHas('vendor', function ($query) use ($id) {
            return $query->where('vendor_id', $id);
        })
            ->when(
                $request->approval_status,
                fn ($q) => $q->where('approval_status', $request->approval_status)
            )
            ->when($request->keyword, function ($query) use ($request) {
                return $query->where('name', "like", "%" . $request->keyword . "%");
            })
            ->latest();

        if ($request->t == 'all') {
            return ProductResource::collection($q->get());
        } else {
            return ProductResource::collection($q->paginate($this->perPage));
        }
    }

    public function menus($id)
    {
        $menus = Menu::active()->where('vendor_id', $id)
            ->with(['products' => fn (BelongsToMany $belongsToMany) => $belongsToMany->active()])
            ->get();

        return MenuResource::collection($menus);
    }


    public function summery($id)
    {
        $vendor = Vendor::findOrFail($id);

        // pending
        // preparing
        // ready
        // enroute
        // cancelled
        // delivered

        // return $vendor->orders;
        // return Order::currentStatus()->get();

        $users_ids = DB::table('orders')
            ->where('vendor_id', $id)
            ->pluck('user_id');


        return [
            [
                'type' => 'orders',
                'badge' =>  Order::otherCurrentStatus('created')->where('vendor_id', $vendor->id)->currentStatus('pending')->count(),
                'number' =>  Order::otherCurrentStatus('created')->where('vendor_id', $vendor->id)->count(),
                'percentage' => 0,
            ],
            [
                'type' => 'earnings',
                'badge' => 0,
                'number' => 0,
                'percentage' => 0,
            ],
            [
                'type' => 'customer',
                'badge' => 0,
                'number' => collect($users_ids)->unique()->count(),
                'percentage' => 0,
            ],
            [
                'type' => 'products',
                'badge' => 0,
                'number' => Product::where('vendor_id', $vendor->id)->count(),
                'percentage' => 0,
            ],

        ];
    }

    public function sales($id)
    {
        $now = Carbon::now();
        $last_sunday = $now->copy()->subtract('days', $now->copy()->dayOfWeek);
        $weekly = DB::table('orders')
            ->where(
                [
                    ['created_at', '>=', $last_sunday->format('Y-m-d')],
                    ['vendor_id', $id],
                ]
            )
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as date"),
                DB::raw('count(*) as value'),
            )
            ->groupBy('date')
            ->get();
        $yearly = DB::table('orders')
            ->where('vendor_id', $id)
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m-01') as date"),
                DB::raw('count(*) as value'),
            )
            ->groupBy('date')
            ->get();


        return [
            'weekly' => $weekly,
            'yearly' => $yearly,
        ];
    }
    public function customers($id)
    {

        $users_ids = Order::where('payment_status', 'successful')->otherCurrentStatus('created')
            ->where('vendor_id', $id)
            ->pluck('user_id');

        $users = AppUser::whereIn('id', $users_ids)
            ->withCount([
                'orders AS total_orders' => function ($query) {
                    $query->select(DB::raw("SUM(total) as orders"))->where('payment_status', 'successful')->otherCurrentStatus('created');
                }
            ])
            ->paginate();
        return $users;
    }

    public function customer($id, $userId)
    {


        return [
            'total_orders' => Order::otherCurrentStatus('created')->where([['vendor_id', '=', $id], ['user_id', '=', $userId], ['payment_status', '=', 'successful']])->sum('total'),
            'orders_count' => Order::otherCurrentStatus('created')->where([['vendor_id', '=', $id], ['user_id', '=', $userId], ['payment_status', '=', 'successful']])->count(),
            'orders' => OrderResource::collection(Order::otherCurrentStatus('created')->where([['vendor_id', '=', $id], ['user_id', '=', $userId], ['payment_status', '=', 'successful']])->latest()->limit(5)->get()),
        ];
    }
}
