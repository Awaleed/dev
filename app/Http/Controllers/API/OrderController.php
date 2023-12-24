<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Coupon;
use App\Models\AppUserCoupon;
use App\Models\AppUser;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderService;
use App\Models\OrderStop;
use App\Models\PaymentMethod;
use App\Models\Vendor;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\LocationDetailsService;
use App\Services\RouteService;
use App\Traits\OrderTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{

    use OrderTrait;
    //
    public function index(Request $request)
    {
        //
        $driverId = $request->driver_id;
        $vendorId = $request->vendor_id;
        $status = $request->status;
        $type = $request->type;


        $orders = Order::when(!empty($vendorId), function ($query) use ($vendorId) {
            return $query->orWhere('vendor_id', $vendorId);
        })
            ->when(!empty($driverId), function ($query) use ($driverId) {
                return $query->orWhere('driver_id', $driverId);
            })
            ->when(empty($vendorId) && empty($driverId), function ($query) {
                return $query->where('user_id', Auth::id());
            })
            ->when(!empty($status), function ($query) use ($status) {
                // return $query->where('status', $status);
                return $query->currentStatus($status);
            })
            ->when($type == "history", function ($query) {
                // return $query->whereIn('status', ['failed', 'cancelled', 'delivered']);
                return $query->currentStatus(['failed', 'cancelled', 'delivered']);
            })
            ->when($type == "assigned", function ($query) {
                // return $query->whereNotIn('status', ['failed', 'cancelled', 'delivered']);
                return $query->otherCurrentStatus(['failed', 'cancelled', 'delivered']);
            })
            ->otherCurrentStatus('created')
            ->orderBy('created_at', 'DESC')
            ->paginate();
        return OrderResource::collection($orders);
    }

    public function store(Request $request)
    {


        // //if the new order if for packages
        // if ($request->type == "package" || $request->type == "parcel") {
        //     return $this->processPackageDeliveryOrder($request);
        // } else if ($request->type == "service") {
        //     return $this->processServiceOrder($request);
        // }

        //regular order
        //validate request
        $validator = Validator::make($request->all(), [
            'vendor_id' => 'required|exists:vendors,id',
            // 'delivery_address_id' => 'sometimes|nullable|exists:delivery_addresses,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'sub_total' => 'required|numeric',
            'discount' => 'required|numeric',
            'delivery_fee' => 'required|numeric',
            'tax' => 'required|numeric',
            'total' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            logger()->error($validator->errors());
            return response()->json([
                "message" => $this->readalbeError($validator),
            ], 400);
        }


        //
        try {

            //check wallet balance if wallet is selected before going further
            $paymentMethod = PaymentMethod::find($request->payment_method_id);
            //wallet check
            if ($paymentMethod->is_cash && $paymentMethod->slug == "wallet") {

                $wallet = Wallet::mine()->first();
                if (empty($wallet) || $wallet->balance < $request->total) {
                    throw new \Exception(__("Wallet Balance is less than order total amount"), 1);
                }
            }

            $vendor = Vendor::distance($request->latitude, $request->longitude)->where('id', $request->vendor_id)->first();
            //
            DB::beginTransaction();
            $order = new order();
            $order->note = $request->note ?? '';
            $order->vendor_id = $request->vendor_id;
            // $order->delivery_address_id = $request->delivery_address_id;
            $order->payment_method_id = $request->payment_method_id;

            $order->note = $request->note;
            $order->delivery_note = $request->delivery_note;
            $order->latitude = $request->latitude;
            $order->longitude = $request->longitude;

            if ($request->latitude && $request->longitude) {
                $locDetails = LocationDetailsService::get($request->latitude, $request->longitude);
                $order->address =  $locDetails['label'];
            }

            $order->vendor_latitude = $vendor->latitude;
            $order->vendor_longitude = $vendor->longitude;
            $order->vendor_address = $vendor->address;

            $order->distance = $vendor->distance;

            $polyline = RouteService::getPolyline(
                $vendor->latitude,
                $vendor->longitude,
                $request->latitude,
                $request->longitude,
            );
            $order->polyline = $polyline ?? '';

            $order->tip = $request->tip ?? 0.00;
            $order->user_id = $request->user_id  ?? Auth::id();

            $order->pickup_date = $request->pickup_date;
            $order->pickup_time = $request->pickup_time;
            $order->payment_status = "pending";

            $order->sub_total = $request->sub_total;
            $order->discount = $request->discount;
            $order->delivery_fee = $request->delivery_fee;
            $order->tax = $request->tax;
            $order->total = $request->total;
            $order->save();
            $order->setStatus($this->getNewOrderStatus($request, $paymentMethod->slug));

            if (!$order->number && $order->status == 'pending') {
                $order->number = (Order::max('number') ?? 0) + 1;
                $order->save();
            }

            // save the coupon used
            $coupon = Coupon::where("code", $request->coupon_code)->first();
            if (!empty($coupon)) {
                $couponUser = new AppUserCoupon();
                $couponUser->coupon_id = $coupon->id;
                $couponUser->app_user_id = Auth::id();
                $couponUser->order_id = $order->id;
                $couponUser->save();
            }


            //products

            foreach ($request->products ?? [] as $product) {

                $orderProduct = new OrderProduct();
                $orderProduct->order_id = $order->id;
                $orderProduct->quantity = $product['selected_qty'];
                $orderProduct->product_id = $product['product_id'];
                $orderProduct->options_ar = $product['options_flatten_ar'];
                $orderProduct->options_en = $product['options_flatten_en'];
                $orderProduct->options_ids = implode(",", $product['options_ids'] ?? []);

                $orderProduct->price = $product['price'];

                $orderProduct->save();

                //reduce product qty
                $product = $orderProduct->product;
                if (!empty($product->available_qty)) {
                    $product->available_qty = $product->available_qty - $orderProduct->quantity;
                    $product->save();
                }
            }
            // photo for prescription
            if ($request->hasFile("photo")) {
                $order->clearMediaCollection();
                $order->addMedia($request->photo->getRealPath())->toMediaCollection();
            }

            //
            if ($request->type == "pharmacy" && $request->hasFile("photo")) {
                $order->payment_status = "review";
                // $order->saveQuietly();

            }


            //
            $paymentMethod = PaymentMethod::find($request->payment_method_id);
            $paymentLink = "";
            $message = "";

            if ($paymentMethod->is_cash) {

                //wallet check
                if ($paymentMethod->slug == "wallet") {
                    //
                    $wallet = Wallet::mine()->first();
                    if (empty($wallet) || $wallet->balance < $request->total) {
                        throw new \Exception(__("Wallet Balance is less than order total amount"), 1);
                    } else {
                        //
                        $wallet->balance -= $request->total;
                        $wallet->save();

                        //RECORD WALLET TRANSACTION
                        $this->recordWalletDebit($wallet, $request->total);
                    }
                }

                $order->payment_status = "successful";
                // $order->saveQuietly();
                $message = __("Order placed successfully. Relax while the vendor process your order");
            } else {
                $message = __("Order placed successfully. Please follow the link to complete payment.");
                if ($order->payment_status == "pending") {
                    $paymentLink = ''; // route('order.payment', ["code" => $order->code]);
                }
            }

            //
            $order->save();

            //
            DB::commit();

            return response()->json([
                "message" => $message,
                "link" => $paymentLink,
                "order" => OrderResource::make(Order::find($order->id)),
            ], 200);
        } catch (\Exception $ex) {
            Log::info([
                "Error" => $ex->getMessage(),
                "File" => $ex->getFile(),
                "Line" => $ex->getLine(),
            ]);
            DB::rollback();
            return response()->json([
                "message" => $ex->getMessage()
            ], 400);
        }
    }



    public function show(Request $request, $id)
    {
        //
        $order = Order::where('code', $id)->first();
        if ($order) {
            return OrderResource::make($order);
        } else {
            return response($status = 404);
        }
    }



    //
    public function update(Request $request, $id)
    {
        //
        $user = AppUser::find(Auth::id());
        $driver = AppUser::find($request->driver_id);
        $order = Order::where('code', $id)->first();
        $enableDriverWallet = (bool) setting('enableDriverWallet', "0");
        $driverWalletRequired = (bool) setting('driverWalletRequired', "0");

        if ($user->hasAnyRole('client') && $user->id != $order->user_id && !in_array($request->status, ['pending', 'cancelled'])) {
            return response()->json([
                "message" => "Order doesn't belong to you"
            ], 400);
        }
        //wallet system
        else if ($request->status == "enroute" && !empty($request->driver_id) && $enableDriverWallet) {

            //
            $driverWallet = $driver->wallet;
            if (empty($driverWallet)) {
                $driverWallet = $driver->updateWallet(0);
            }

            //allow if wallet has enough balance
            if ($driverWalletRequired) {
                if ($order->total > $driverWallet->balance) {
                    return response()->json([
                        "message" => __("Order not assigned. Insufficient wallet balance")
                    ], 400);
                }
            } else if ($order->payment_method->slug == "cash" && $order->total > $driverWallet->balance) {
                return response()->json([
                    "message" => __("Insufficient wallet balance, Wallet balance is less than order total amount")
                ], 400);
            } else if ($order->payment_method->slug != "cash" && $order->delivery_fee > $driverWallet->balance) {
                return response()->json([
                    "message" => __("Insufficient wallet balance, Wallet balance is less than order delivery fee")
                ], 400);
            }
        }


        //
        try {

            //fetch order
            DB::beginTransaction();
            $order = Order::where('code', $id)->first();
            ////prevent driver from accepting a cancelled order
            if (empty($order)) {
                throw new Exception(__("Order could not be found"));
            } else if (!empty($request->driver_id) && in_array($order->status, ["cancelled", "delivered", "failed"])) {
                throw new Exception(__("Order has already been") . " " . $order->status);
            } else if (empty($order) || (!empty($request->driver_id) && !empty($order->driver_id))) {
                throw new Exception(__("Order has been accepted already by another delivery boy"));
            }

            //
            if (!empty($request->driver_id)) {
                $order->driver_id = $request->driver_id;
                $order->save();
            }
            $order->update($request->all());

            //
            if (!empty($request->status)) {
                $order->setStatus($request->status);
            }

            DB::commit();

            return response()->json([
                "message" => __("Order placed ") . __($order->status) . "",
                "order" => OrderResource::make(Order::where('code', $id)->first()),
            ], 200);
        } catch (\Exception $ex) {
            logger("order error", [$ex]);
            DB::rollback();
            return response()->json([
                "message" => $ex->getMessage()
            ], 400);
        }
    }










    //


    public function recordWalletDebit($wallet, $amount)
    {
        $walletTransaction = new WalletTransaction();
        $walletTransaction->wallet_id = $wallet->id;
        $walletTransaction->amount = $amount;
        $walletTransaction->reason = __("New Order");
        $walletTransaction->status = "successful";
        $walletTransaction->is_credit = 0;
        $walletTransaction->save();
    }
}
