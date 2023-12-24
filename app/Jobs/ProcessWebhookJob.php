<?php

namespace App\Jobs;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob as SpatieProcessWebhookJob;

class ProcessWebhookJob extends SpatieProcessWebhookJob

{
    public function handle()
    {
        try {

            $data = json_decode($this->webhookCall, true);
            logger()->info('Processing webhook call', ['data' => $data]);
            $httpClint =   Http::withBasicAuth(env('MOYASAR_SECRET_KEY', ''), '');

            $response = $httpClint->get('https://api.moyasar.com/v1/payments/' . $data['payload']['data']['id']);

            $data = $response->json();
            $order = Order::where('code', $data['metadata']['order_code'])->first();

            if ($order->payment) {
                http_response_code(200);
                return;
            }

            $payment = new Payment();
            $payment->amount = $data['amount'] / 100;
            $payment->order_id = $order->id;
            $payment->ref = $data['id'];
            $payment->type = 'moyaser';
            $payment->raw_object = json_encode($data);

            $payment->status = $data['status'] == 'paid' ? 'successful' : 'failed';

            $payment->save();

            if ($data['status'] == 'paid') {
                $order->setStatus('pending');
                $order->update(['payment_status' => 'successful']);
            } else {
                $order->update(['payment_status' => 'failed']);
            }


            if (!key_exists('order_number', $data['metadata'])) {
                $response = $httpClint->put(
                    'https://api.moyasar.com/v1/payments/' . $payment->ref,
                    [
                        'description' => 'طلب دفع من تطبيق اسركم لفاتورة رقم ' . $order->number,
                        'metadata' => [
                            'payment_id' => $payment->id,
                            'order_number' => $order->number,
                        ]
                    ]
                );
                logger('Moyaser Update  $response', ['response' => $response->body()]);
            }
        } catch (\Exception $ex) {
        }
        http_response_code(200);
    }
}
