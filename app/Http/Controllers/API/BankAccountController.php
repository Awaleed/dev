<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return auth('api')->user()->vendor->bankAccounts;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'bank_name'             => 'required|string',
            'account_holder_name'   => 'required|string',
            'iban'                  => 'required|string',
            'is_default'            => 'nullable|boolean',
        ]);

        //
        $createdBankAccount = new BankAccount();
        $createdBankAccount->vendor_id = auth('api')->user()->vendor->id;

        $createdBankAccount->bank_name           = $request->bank_name;
        $createdBankAccount->account_holder_name = $request->account_holder_name;
        $createdBankAccount->iban                = $request->iban;
        $createdBankAccount->is_default          = $request->is_default ?? false;

        $createdBankAccount->save();

        return $createdBankAccount;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function show(BankAccount $bankAccount)
    {
        //
        return $bankAccount;
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BankAccount $bankAccount)
    {
        //
        $request->validate([
            'bank_name'             => 'nullable|string',
            'account_holder_name'   => 'nullable|string',
            'iban'                  => 'nullable|string',
            'is_default'            => 'nullable|boolean',
        ]);

        //
        $bankAccount->bank_name           = $request->bank_name           ?? $bankAccount->bank_name;
        $bankAccount->account_holder_name = $request->account_holder_name ?? $bankAccount->account_holder_name;
        $bankAccount->iban                = $request->iban                ?? $bankAccount->iban;
        $bankAccount->is_default          = $request->is_default          ?? $bankAccount->is_default;


        $bankAccount->save();

        return $bankAccount;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function destroy(BankAccount $bankAccount)
    {
        //
        return $bankAccount->delete();
    }
}
