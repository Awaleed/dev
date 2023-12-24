<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\AppUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['store']);
    }

    public function index(Request $request)
    {
        $products = null;
        if ($request->type == "vendor") {
            $products = Product::with("categories", "sub_categories", "menus")->whereHas('vendor', function ($query) use ($request) {
                return $query->where('vendor_id', "=", auth('api')->user()->vendor_id);
            })
                ->when($request->keyword, function ($query) use ($request) {
                    return $query->where('name', "like", "%" . $request->keyword . "%");
                })->latest()->paginate($this->perPage);
        }
        $products = Product::active()->when($request->type == "best", function ($query) {
            return $query->withCount('sales')->orderBy('sales_count', 'DESC');
        })
            ->when($request->keyword, function ($query) use ($request) {
                return $query->where('name', "like", "%" . $request->keyword . "%");
            })
            ->when($request->category_id, function ($query) use ($request) {
                return $query->where('category_id', "=", $request->category_id);
            })
            //show products tied to a certain sub cateogry
            ->when($request->sub_category_id, function ($query) use ($request) {
                return $query->whereHas('sub_categories', function ($query) use ($request) {
                    return $query->where('subcategory_id', $request->sub_category_id);
                });
            })
            //show products tied to a certain menu
            ->when($request->menu_id, function ($query) use ($request) {
                return $query->whereHas('menus', function ($query) use ($request) {
                    return $query->where('menu_id', $request->menu_id);
                });
            })
            ->when($request->is_open, function ($query) use ($request) {
                return $query->where('is_open', "=", $request->is_open);
            })
            ->when($request->type == "you", function ($query) {
                if (auth('sanctum')->user()) {
                    return $query->whereHas('purchases')->withCount('purchases')->orderBy('purchases_count', 'DESC');
                } else {
                    return $query->inRandomOrder();
                }
            })

            ->when($request->vendor_id, function ($query) use ($request) {
                return $query->active()->where('vendor_id', $request->vendor_id);
            })
            ->paginate($this->perPage);

        return ProductResource::collection($products);
    }

    public function show(Request $request, $id)
    {
        try {
            return ProductResource::make(Product::findOrFail($id));
        } catch (\Exception $ex) {
            return response()->json([
                "message" => $ex->getMessage() ?? __("No Product Found")
            ], 400);
        }
    }

    public function store(Request $request)
    {
        $user = AppUser::find(Auth::id());
        if (!$user->hasRole('manager')) {
            return response()->json([
                "message" => __("You are not allowed to perform this operation")
            ], 400);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'name_ar' => 'required|string',
                // 'name_en' => 'required|string',
                'price' => 'required|numeric',
                // 'category_id' => 'exists:categories,id',
                // 'photos' => 'required|array',
                // 'photos.*' => 'file',

            ],
        );

        // TRANSLATE: Add Translation
        if ($validator->fails()) {
            return response()->json([
                "message" => $this->readalbeError($validator),
            ], 400);
        }

        try {
            DB::beginTransaction();
            $product = Product::create(array_merge(
                $request->all(),
                [
                    'name_en' => '',
                    'description_en' => '',
                    'vendor_id' => $user->vendor_id,
                    'deliverable' => $request->deliverable == 1 || $request->deliverable == "true",
                    'is_active' => $request->is_active == 1 || $request->is_active == "true",
                    'with_option' => $request->with_option == 1 || $request->with_option == "true",
                ]
            ));
            $product->save();

            //categories
            if (!empty($request->category_id)) {
                $product->categories()->sync($request->category_id);
            }
            //sub_category_ids
            if (!empty($request->sub_category_ids)) {
                $product->sub_categories()->attach($request->sub_category_ids);
            }
            //menus
            if (!empty($request->menu_ids)) {
                $product->menus()->attach($request->menu_ids);
            }

            //photo
            if ($request->hasFile("photo")) {
                $product->clearMediaCollection('default');
                $product->addMediaFromRequest('photo')->toMediaCollection('default');
            }

            if ($request->photos)
                $product->clearMediaCollection();
            foreach ($request->photos as $photo) {
                $product->addMedia($photo->getRealPath())->toMediaCollection();
            }

            DB::commit();
            return ProductResource::make(Product::find($product->id));
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json([
                "message" => $ex->getMessage() ?? __("Product Creation failed")
            ], 400);
        }
    }

    public function update(Request $request, $id)
    {
        // return $request->all();
        try {
            $product = Product::find($id);
            $user = AppUser::find(auth('api')->id());
            //
            if (!$user->hasAnyRole('manager') || $user->vendor_id != $product->vendor_id) {
                return response()->json([
                    "message" => __("You are not allowed to perform this operation")
                ], 400);
            }

            DB::beginTransaction();
            $product->update(array_merge(
                $request->all(),
                [
                    'deliverable' => $request->deliverable == 1 || $request->deliverable == "true",
                    'is_active' => $request->is_active == 1 || $request->is_active == "true",
                    'with_option' => $request->with_option == 1 || $request->with_option == "true",
                ]
            ));

            //categories
            if (!empty($request->category_id)) {
                $product->categories()->sync($request->category_id);
            }
            //sub_category_ids
            if (!empty($request->sub_category_ids)) {
                $product->sub_categories()->sync($request->sub_category_ids);
            }
            //menus
            if (!empty($request->menu_ids)) {
                $product->menus()->sync($request->menu_ids);
            }

            if ($request->hasFile("photo")) {
                $product->clearMediaCollection('default');
                $product->addMediaFromRequest('photo')->toMediaCollection('default');
            }


            // if ($request->photos)
            //     $product->clearMediaCollection();
            // foreach ($request->photos as $photo) {
            //     $product->addMedia($photo->getRealPath())->toMediaCollection();
            // }
            $product->save();
            DB::commit();
            return ProductResource::make(Product::find($product->id));

            // return response()->json([
            //     "message" => __("Product updated successfully"),
            // ]);
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json([
                "message" => $ex->getMessage()
            ], 400);
        }
    }

    public function menus(Request $request, $id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'menus_ids' => 'required|array',
                'menus_ids.*' => 'exists:menus,id',
            ],
        );


        // TRANSLATE: Add Translation
        if ($validator->fails()) {
            return response()->json([
                "message" => $this->readalbeError($validator),
            ], 400);
        }


        try {
            $product = Product::find($id);
            DB::beginTransaction();
            $product->menus()->sync($request->menus_ids);
            DB::commit();
            return $product->menus;
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json([
                "message" => $ex->getMessage()
            ], 400);
        }
    }

    public function photos($id)
    {
        $product = Product::findOrFail($id);
        $product->setHidden([]);
        return $product->toArray()['media'];
    }

    public function addPhoto(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        if ($request->hasFile("photo")) {
            $product->addMediaFromRequest('photo')->toMediaCollection('default');
        }
    }

    public function deletePhoto($imageId)
    {
        Media::find($imageId)->delete();
    }

    public function orderPhotos(Request $request)
    {
        Media::setNewOrder($request->ids);
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        $user = AppUser::find(auth('api')->id());
        //
        if (!$user->hasAnyRole('manager') || $user->vendor_id != $product->vendor_id) {
            return response()->json([
                "message" => __("You are not allowed to perform this operation")
            ], 400);
        }

        try {
            DB::beginTransaction();
            Product::destroy($id);
            DB::commit();

            return response()->json([
                "message" => __("Product deleted successfully"),
            ]);
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json([
                "message" => $ex->getMessage()
            ], 400);
        }
    }
}
