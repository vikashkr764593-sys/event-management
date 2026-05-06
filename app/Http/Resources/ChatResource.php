<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    public function toArray($request)
    {
        return ['id' => $this->id, 'booking_id' => $this->booking_id, 'sender' => new UserResource($this->whenLoaded('sender')), 'message' => $this->message, 'created_at' => $this->created_at];
    }
}
