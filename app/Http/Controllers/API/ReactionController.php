<?php

namespace App\Http\Controllers\API;

use App\Models\Blog;
use App\Models\Forum;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Blog as BlogResource;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\Comment as ResourcesComment;
use App\Models\Like;
use App\Models\Viewer;

class ReactionController extends BaseController
{
    
    private function getPost($input)
    {
        $model = $input['model'];
        $post_id = $input['post_id'];

        if ($model === 'comment') {
            return Comment::find($post_id);
        }

        return $model === 'blog'?
         Blog::find($post_id):
         Forum::find($post_id);
    }
    public function comment(Request $request)
    {
        Gate::authorize('comment');

        $input = $request->post();

        $validator = Validator::make($input, [
            'content' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->handleError($validator->errors(), [], 302);
        }
        $post = $this->getPost($input);

        $post->comments()->save(new Comment([
            'content' => $input['content'],
            'user_id' => $request->user()->id,
        ]));

        return $this->handleResponse(new BlogResource($post), 
            $post->title ?? $post->content . ' commented!');
    }

    public function react(Request $request) 
    {
        Gate::authorize('comment');

        $user_id = $request->user()->id;
        $input = $request->post();
        $post = $this->getPost($input);
        $post->likes()->where('user_id', $user_id)->delete();

        $post->likes()->save(new Like([
            'type' => $input['type'],
            'user_id' => $user_id,
        ]));
        $data = $input['model'] === 'comment'?
                new ResourcesComment($post):
                new BlogResource($post);
        
        return $this->handleResponse($data ,
         $post->title ?? $post->content . ' a bien subit la reaction!'); 
    }

    public function seen(Request $request)
    {   
        $input = $request->post();
        $post = $this->getPost($input);

        if($post->viewers
        ->where('ipaddress', $request->ip())
        ->where('user_agent', $request->userAgent())
        ->count()){
            return true;
        }  

        $post->viewers()->save(new Viewer([
            'ipaddress' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]));

        return $this->handleResponse(new BlogResource($post), true);
    }
}
