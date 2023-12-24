<?php

namespace App\Services;

use App\Models\Earning;
use App\Models\Remittance;
use App\Models\SystemEarningTransaction;
use App\Models\Wallet;
use App\Models\User;
use App\Models\VendorTransaction;

class OrderEarningService
{
    public function __constuct()
    {
        //
    }


    public function updateEarning($order)
    {
        $isCashOrder = $order->payment_method->slug == "cash";
        // 'pending','preparing','ready','enroute','delivered','failed','cancelled'
        if ($order->status == 'delivered') {
            // only if online or driver wallet
            $generalVendorCommission = setting('vendorsCommission', "0");
            // update vendor earning

            $earning = Earning::firstOrCreate(
                ['vendor_id' => $order->vendor_id],
                ['amount' => 0]
            );

            $vendorCommission = $order->vendor->commission;
            if (empty($vendorsCommission)) {
                $vendorCommission = $generalVendorCommission;
            }

            $systemCommission = ($vendorCommission / 100) * $order->sub_total;

            // minus our commission
            $amount = $order->sub_total - $systemCommission - ($order->discount ?? 0);
            if ($isCashOrder) {
                $amount = $amount * -1;
            }
            $earning->amount += $amount;

            $earning->save();

            $createdVendorTransaction = new VendorTransaction();
            $createdVendorTransaction->amount = $amount;
            $createdVendorTransaction->balance = $earning->amount;

            if ($isCashOrder) {
                $createdVendorTransaction->reason = 'خصم نسبة اسركم من طلب رقم' . $order->number;
            } else {
                $createdVendorTransaction->reason = 'ايراد جديد لطلب رقم' . $order->number;
            }

            $createdVendorTransaction->vendor_id = auth('api')->user()->vendor->id;
            $createdVendorTransaction->save();

            $createdSystemEarningTransaction                            = new SystemEarningTransaction();
            $createdSystemEarningTransaction->amount                    = $systemCommission;
            $createdSystemEarningTransaction->order_id                  = $order->id;
            $createdSystemEarningTransaction->vendor_transaction_id     = $createdVendorTransaction->id;
            $createdSystemEarningTransaction->save();
        }
    }
}
