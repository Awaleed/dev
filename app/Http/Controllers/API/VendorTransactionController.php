<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\VendorTransaction;
use Illuminate\Http\Request;

class VendorTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return VendorTransaction::where('vendor_id', auth('api')->user()->vendor->id)->latest()->paginate();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\VendorTransaction  $vendorTransaction
     * @return \Illuminate\Http\Response
     */
    public function show(VendorTransaction $vendorTransaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\VendorTransaction  $vendorTransaction
     * @return \Illuminate\Http\Response
     */
    public function edit(VendorTransaction $vendorTransaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\VendorTransaction  $vendorTransaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, VendorTransaction $vendorTransaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\VendorTransaction  $vendorTransaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(VendorTransaction $vendorTransaction)
    {
        //
    }
}
