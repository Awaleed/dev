<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Option;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\Request;

class CartInfoController extends Controller
{
    //
    public function getCartInfo(Request $request)
    {
        $cart = new Cart();

        $vendor = Vendor::findOrFail($request->vendor_id);
        $cart->setVendor($vendor);

        if ($request->payment_method_id) {
            $paymentMethod = PaymentMethod::find($request->payment_method_id);
            if ($paymentMethod) $cart->setPaymentMethod($paymentMethod);
        }

        if ($request->coupon_code) {
            $coupon = Coupon::where('code', $request->coupon_code)->whereHas('vendors', fn ($q) => $q->where('id', $request->vendor_id))->first();
            if ($coupon) $cart->setCoupon($coupon);
        }

        foreach ($request->products as $product) {
            $cartItem = new CartItem();
            if (array_key_exists('id', $product)) $cartItem->setProduct(Product::findOrFail($product['id']));
            if (array_key_exists('uuid', $product)) $cartItem->setUuid($product['uuid']);
            if (array_key_exists('options_ids', $product)) $cartItem->setOptions(Option::whereIn('id', $product['options_ids'])->get());
            if (array_key_exists('quantity', $product)) $cartItem->setQuantity($product['quantity']);

            $cart->addItem($cartItem);
        }

        // CartItem
        return $cart->getJson();
    }
}
