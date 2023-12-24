<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Http\Requests\StorePaymentMethodRequest;
use App\Http\Requests\UpdatePaymentMethodRequest;

class PaymentMethodController extends Controller
{
    public function index()
    {
        return PaymentMethod::active()->get();
    }
}
