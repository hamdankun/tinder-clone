<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'age' => $this->age,
            'location' => $this->location,
            'bio' => $this->bio,
            'pictures' => PictureResource::collection($this->pictures),
            'created_at' => $this->created_at,
        ];
    }
}
