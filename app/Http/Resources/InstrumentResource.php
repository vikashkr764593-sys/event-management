<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
class InstrumentResource extends JsonResource {
    public function toArray($request) {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'category' => $this->category,
            'price' => $this->price,
            'stock_status' => $this->stock_status,
        ];
    }
}