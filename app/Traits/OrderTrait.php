<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Carbon\Carbon;


trait OrderTrait
{
    public function getNewOrderStatus(Request $request, $slug)
    {

        $orderDate = Carbon::parse("" . $request->pickup_date . " " . $request->pickup_time . "");
        $hoursDiff = Carbon::now()->diffInHours($orderDate);

        if ($hoursDiff > setting('minScheduledTime', 2)) {
            return "scheduled";
        } else if (in_array($slug, ['cash', 'transfer'])) {
            return "pending";
        } else {
            return 'created';
        }
    }
}
