<?php

namespace App\Http\Resources;

use App\Models\Earn;
use App\Models\Invoice;
use App\Models\Lesson;
use App\Models\Order;
use Illuminate\Http\Resources\Json\JsonResource;

class AppNotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $payload = null;
        $type = 'general';

        try {
            if ($this->notifiable_type == Order::class) {
                $type    = 'order';
                $payload = OrderResource::make(Order::find($this->notifiable_id));
            } else if ($this->notifiable_type == Earn::class) {
                $type = 'earn';
            } else if ($this->notifiable_type == Lesson::class) {
                $type = 'lesson';
                $payload = Order::with('package', 'pdoption', 'son', 'subjects', 'educationLevel', 'time', 'education')
                    ->with('lessons', fn ($query) => $query->latest()->limit(3))
                    ->find($this->notifiable->order_id);
            } else if ($this->notifiable_type == Invoice::class) {
                $type = 'invoice';
                $payload = [
                    'invoice' => $this->notifiable,
                    'order' => Order::with('package', 'pdoption', 'son', 'subjects', 'educationLevel', 'time', 'education')
                        ->with('lessons', fn ($query) => $query->latest()->limit(3))
                        ->find($this->notifiable->order_id),
                ];
            }
        } catch (\Throwable $th) {
            //throw $th;
        }

        return [
            'id'  => $this->id,
            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,
            'text_ar' => $this->text_ar,
            'text_en' => $this->text_en,
            'icon'  => $this->icon,
            'url' => $this->url,
            'read_at' => $this->read_at,
            'created_at' => $this->created_at,
            'payload' => $payload,
            'type' => $type,
        ];
    }
}
