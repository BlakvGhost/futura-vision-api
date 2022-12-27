<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Like extends JsonResource
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
            'type' => $this->type,
            'user' => ['id' => $this->user_id],
            'created_at' => Carbon::now()->diffForHumans($this->created_at),
            'updated_at' => Carbon::now()->diffForHumans($this->updated_at),
        ];
    }
}
