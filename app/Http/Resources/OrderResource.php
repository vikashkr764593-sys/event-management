<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
class OrderResource extends JsonResource {
    public function toArray($request) {
        return ['id' => $this->id, 'user_id' => $this->user_id, 'total_amount' => $this->total_amount, 'razorpay_order_id' => $this->razorpay_order_id, 'razorpay_payment_id' => $this->razorpay_payment_id, 'payment_status' => $this->payment_status, 'status' => $this->status, 'items' => OrderItemResource::collection($this->whenLoaded('items')), 'created_at' => $this->created_at];
    }
}