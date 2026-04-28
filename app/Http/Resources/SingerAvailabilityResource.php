<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SingerAvailabilityResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'        => $this->id,
            'singer_id' => $this->singer_id,
            'date'      => $this->date,
            'status'    => $this->status,
        ];
    }
}