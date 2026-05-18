<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SingerResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user')),
            'genre' => $this->genre,
            'experience' => $this->experience_years,
            'rating' => $this->rating,
            'about' => $this->biography,
            'languages' => $this->languages,
            'travel_available' => $this->travel_available,
            'instagram_link' => $this->instagram_link,
            'youtube_link' => $this->youtube_link,
            'spotify_link' => $this->spotify_link,
            'cover_image' => $this->cover_image,
            'sample_audio' => $this->sample_audio,
            'sample_video' => $this->sample_video,
            'starting_price' => $this->fee,
        ];
    }
}
