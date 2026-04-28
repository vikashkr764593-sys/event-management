<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
class OrderItemResource extends JsonResource {
    public function toArray($request) {
        return ['id' => $this->id, 'instrument' => new InstrumentResource($this->whenLoaded('instrument')), 'quantity' => $this->quantity, 'price' => $this->price];
    }
}