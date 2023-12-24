<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\DeliveryAddress;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Vendor;
use App\Services\TamaraService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PaymentsController extends Controller
{
    public function moyaserCallback(Request $request,  $code)
    {
        return '';
    }
}
