<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
class SingerResource extends JsonResource {
    public function toArray($request) {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user')),
            'genre' => $this->genre,
            'experience' => $this->experience,
            'rating' => $this->rating,
            'about' => $this->about,
        ];
    }
}