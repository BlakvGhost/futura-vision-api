<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Post;
use App\Utils\Utils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class PostController extends BaseController
{
    private $rules = [
        'sup_title' => 'required',
        'sub_title' => 'required',
        'content' => 'required',
        'img' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ];

    public function __construct()
    {
        $this->authorizeResource(Post::class);
    }
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $post = Post::orderBy('id', 'desc')->get();
        return $this->handleResponse($post, 'Posts have been retrieved!');
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, $this->rules);
        if ($validator->fails()) {
            return $this->handleError($validator->errors(), [], 302);
        }

        $image = Utils::store_image($request, $input['sup_title'], 'post', 'img');

        $post = Post::create(array_merge($input, [
                'cover' => $image, 'user_id' => Auth::user()->id,
            ]
        ));
        return $this->handleResponse($post, 'Post created!');
    }


    /**
     * @param $id
     * @return JsonResponse
     */
    public function show(Post $post): JsonResponse
    {
        return $this->handleResponse($post, 'Post retrieved.');
    }


    public function update(Request $request, Post $post): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'sup_title' => 'required',
            'sub_title' => 'required',
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->handleError($validator->errors());
        }

        $image = $post->cover;
        if ($request->file('img')) {
            $image = Utils::store_image($request, $input['sup_title'], 'post', 'img');
        }

        $post->update(array_merge($input, ['cover' => $image]));

        return $this->handleResponse($post, 'Post successfully updated!');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return $this->handleResponse([], $post->sup_title . ' deleted!');
    }
}
