<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Like;

class LikeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Check if mutual like exists
        $isMutual = Like::where('from_user_id', $this->to_user_id)
            ->where('to_user_id', $this->from_user_id)
            ->exists();

        return [
            'id' => $this->id,
            'to_user' => new UserResource($this->whenLoaded('toUser')),
            'is_matched' => $isMutual,
            'liked_at' => $this->created_at,
        ];
    }
}
