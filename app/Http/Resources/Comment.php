<?php

namespace App\Http\Resources;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Like as LikeResource;

class Comment extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'user' => User::find($this->user_id),
            'likes' => LikeResource::collection(
                $this->likes->where('type', '=', 'like')
            ),
            'unlikes' => LikeResource::collection(
                $this->likes->where('type', '=', 'unlike')
            ),
            'created_at' => Carbon::now()->diffForHumans($this->created_at),
            'updated_at' => Carbon::now()->diffForHumans($this->updated_at),
        ];
    }
}
