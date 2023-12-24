<?php

namespace App\Models;

use App\Http\Resources\VendorResource;

class Cart
{
    public $items = [];
    public $vendor;
    public $paymentMethod;
    public $coupon;
    public $note;

    public function setVendor(Vendor $vendor)
    {
        $this->vendor = $vendor;
    }

    public function setPaymentMethod(PaymentMethod $paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
    }

    public function setCoupon(Coupon $coupon)
    {
        $this->coupon = $coupon;
    }

    public function addItem(CartItem $item)
    {
        $this->items[] = $item;
    }

    public function getSubtotal()
    {
        $value = 0;
        foreach ($this->items as $item) {
            $value += $item->getTotal();
        }
        return $value;
    }

    public function getDiscountedSubtotal()
    {
        $value = $this->getSubtotal();
        $coupon = $this->coupon;

        if ($coupon != null) {
            $amount = null;
            if ($coupon->percentage) {
                $amount = ($coupon->discount / 100) * $value;
            } else {
                $amount = $coupon->discount;
            }
            if (
                $coupon->maximum_discount != null &&
                $amount > $coupon->maximum_discount
            ) {
                $amount = $coupon->maximum_discount;
            }

            $value -= $amount;

            $value = $value < 0 ? 0 : $value;
        }

        return $value;
    }

    public function getDeliveryFees()
    {
        $coupon = $this->coupon;
        $vendor = $this->vendor;

        if ($coupon == null || !$coupon->free_delivery) {
            return $vendor->delivery_fee ?? 0;
        } else {
            return 0;
        }
    }

    public function getTotal()
    {
        return $this->getDiscountedSubtotal() + $this->getDeliveryFees();
    }

    public function getDiscountAmount()
    {
        return  $this->getSubtotal() - $this->getDiscountedSubtotal();
    }

    public function getMinOrder()
    {
        return 0;
    }

    public function  getJson()
    {
        $items = [];

        foreach ($this->items as $item) {
            $items[] = $item->getJson();
        }

        return  [
            'items' => $items,
            'vendor' => VendorResource::make($this->vendor),
            'min_order' => $this->getMinOrder(),
            'payment_method' => $this->paymentMethod,
            'coupon' => $this->coupon,
            'note' => $this->note,
            'subtotal' => $this->getSubtotal(),
            'discounted_subtotal' => $this->getDiscountedSubtotal(),
            'delivery_fees' => $this->getDeliveryFees(),
            'total' => $this->getTotal(),
            'discount_amount' => $this->getDiscountAmount(),
        ];
    }
}
