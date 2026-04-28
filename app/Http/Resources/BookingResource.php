<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
class BookingResource extends JsonResource {
    public function toArray($request) {
        return ['id' => $this->id, 'user_id' => $this->user_id, 'singer' => new SingerResource($this->whenLoaded('singer')), 'event_date' => $this->event_date, 'time_slot' => $this->time_slot, 'event_type' => $this->event_type, 'notes' => $this->notes, 'status' => $this->status, 'created_at' => $this->created_at];
    }
}