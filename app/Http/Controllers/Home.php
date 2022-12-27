<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Post;
use App\Models\Team;
use App\Models\Forum;
use App\Models\Exploit;
use App\Models\Partner;
use App\Models\Project;
use App\Models\Service;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Technology;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Blog as BlogResource;
use App\Http\Resources\Post as ResourcesPost;
use App\Http\Controllers\API\BaseController as BaseController;

class Home extends BaseController
{

    public function index()
    {
        $config = json_decode(Storage::get('setting/ui.json')); 
        $context = [
            'carousel' => ResourcesPost::collection(Post::orderBy('id', 'desc')->get()),
            'partners' => Partner::orderBy('id', 'desc')->get(),
            'services' => Service::orderBy('id', 'desc')->get()->take(6),
            'projects' => Project::orderBy('id', 'desc')->get(),
            'teams' => Team::orderBy('id', 'desc')->get(),
            'technologies' => Technology::orderBy('id', 'desc')->get(),
            'clients' => Customer::orderBy('id', 'desc')->get(),
            'exploits' => Exploit::orderBy('id', 'desc')->get(),
            'config' => $config,           
        ];

        return $this->handleResponse($context, 'All home data has been retrieved!');
    }

    public function blog() {

        $populars = Blog::all()->sortByDesc(function($post) {
            return $post->viewers->count();
        })->take(2);

        $latest = BlogResource::collection(Blog::orderBy('id', 'desc')
                ->paginate(15))
                ->resource;
        $context = [
            'populars' => BlogResource::collection($populars),
            'latests' => $latest,
        ];

        return $this->handleResponse($context, 'All Blog data has been retrieved!');
    }
    
    public function blogForum() {
        $blog = BlogResource::collection(Blog::orderBy('id', 'desc')->get()->take(6));
        $forum = BlogResource::collection(Forum::orderBy('id', 'desc')->get()->take(6));
        $context = [
            'forum' => $forum,
            'blog' => $blog,
        ];

        return $this->handleResponse($context, 'All Blog data has been retrieved!');
    }

    public function showBlog(Blog $blog)
    {
        $post = Blog::all()->where('category_id', $blog->category_id)
        ->take(10)->filter(fn ($value, $key) => $value->id != $blog->id);
        return $this->handleResponse([
          'post' =>  new BlogResource($blog),
          'similars' =>  BlogResource::collection($post),
        ], 'Blog retrieved.');
    }

    public function showForum(Forum $forum)
    {
        $post = Forum::all()->where('category_id', $forum->category_id)
        ->take(10)->filter(fn ($value, $key) => $value->id != $forum->id);
        return $this->handleResponse([
          'post' =>  new BlogResource($forum),
          'similars' =>  BlogResource::collection($post),
        ], 'Forum retrieved.');
    }

    public function forum() {
        $context = [
            'categories' => Category::orderBy('id', 'desc')->get(),
            'latests' => BlogResource::collection(
                Forum::orderBy('id', 'desc')->paginate(15)
                )->resource,
        ];

        return $this->handleResponse($context, 'All Forum data has been retrieved!');
    }

    public function filterBasedCat($model, $id)
    {
        $model = $model === 'blog'?
            Blog::where('category_id', $id)->paginate(20):
            Forum::where('category_id', $id)->paginate(20);

        $context = [
            'posts' => BlogResource::collection($model)
                    ->resource,
            'category' => Category::find($id),
        ];

        return $this->handleResponse($context, 'All data has been retrieved!');
    }
    
    public function filterBasedWords($model, $q)
    {
        $field = $model === 'blog'? 'title': 'content';

        $model = $model === 'blog'?
            Blog::where($field, 'LIKE', '%'.$q.'%')->get()->take(5):
            Forum::where($field, 'LIKE', '%'.$q.'%')->get()->take(5);

        $context = BlogResource::collection($model);

        return $this->handleResponse($context, 'All data has been retrieved!');
    }

}
