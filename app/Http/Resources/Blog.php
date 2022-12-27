<?php

namespace App\Http\Resources;

use App\Http\Resources\Comment as CommentResource;
use App\Http\Resources\Like as LikeResource;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Blog extends JsonResource
{
    public $preserveKeys = true;
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $comment = CommentResource::collection(
            $this->comments()
                ->orderBy('id', 'desc')
                ->paginate(8)
        )->resource;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'cover' => $this->cover,
            'content' => $this->content,
            'user' => User::find(
                $this->user_id
            ),
            'category' => Category::find(
                $this->category_id
            ),
            'comments' => $comment,
            'commentsAll' => CommentResource::collection($this->comments),
            'viewers' => $this->viewers,
            'likes' => LikeResource::collection(
                $this->likes->where('type', '=', 'like')
            ),
            'unlikes' => LikeResource::collection(
                $this->likes->where('type', '=', 'unlike')
            ),
            'created_at' => Carbon::now()
                ->diffForHumans($this->created_at),
            'updated_at' => Carbon::now()
                ->diffForHumans($this->updated_at),
        ];
    }
}
