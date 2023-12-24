<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Earning;
use App\Models\Payout;
use App\Models\VendorTransaction;
use Illuminate\Http\Request;

class PayoutController extends Controller
{
    //
    public function index(Request $request)
    {
        return auth('api')->user()->vendor?->earning?->amount ?? 0;
    }

    public function store(Request $request)
    {
        $earning = auth('api')->user()->vendor->earning;

        $request->validate([
            "amount" => "required|numeric|max:" . $earning->amount . "",
            'bank_account_id' => 'required|exists:bank_accounts,id',
        ]);

        //
        $createdPayout                         = new Payout();
        $createdPayout->amount                 = $request->amount;
        $createdPayout->earning_id             = $earning->id;
        $createdPayout->user_id                = auth('api')->user()->id;
        // $createdPayout->payment_method_id      = 1;

        $bankAccount = BankAccount::find($request->bank_account_id);
        $createdPayout->bank_name           = $bankAccount->bank_name;
        $createdPayout->account_holder_name = $bankAccount->account_holder_name;
        $createdPayout->iban                = $bankAccount->iban;

        $createdPayout->save();


        $earning->amount -= $request->amount;
        $earning->Save();

        $createdVendorTransaction =  new VendorTransaction();
        $createdVendorTransaction->amount = $request->amount * -1;
        $createdVendorTransaction->balance = $earning->amount;
        $createdVendorTransaction->reason = 'طلب سحب اموال';
        $createdVendorTransaction->vendor_id = auth('api')->user()->vendor->id;
        $createdVendorTransaction->save();


        return $createdPayout->refresh();
    }
}
