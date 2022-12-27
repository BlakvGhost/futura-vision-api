<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\Blog as BlogResource;
use App\Models\Blog;
use App\Utils\Utils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class BlogController extends BaseController
{
    private $rules = [
        'title' => 'required',
        'category_id' => 'required',
        'content' => 'required',
    ];

    public function __construct()
    {
        $this->authorizeResource(Blog::class);
    }
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $blog = Blog::orderBy('id', 'desc')->get();
        return $this->handleResponse($blog, 'Blogs have been retrieved!');
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

        $image = Utils::store_image($request, $input['title'], 'blog', 'screen');

        $blog = Blog::create(array_merge($input, [
                'cover' => $image, 'user_id' => Auth::user()->id,
            ]
        ));
        return $this->handleResponse(new BlogResource($blog), 'Blog created!');
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function show(Blog $blog): JsonResponse
    {
        return $this->handleResponse(new BlogResource($blog), 'Blog retrieved.');
    }


    public function update(Request $request, Blog $blog): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, $this->rules);

        if ($validator->fails()) {
            return $this->handleError($validator->errors());
        }

        $image = $blog->cover;
        if ($request->file('screen')) {
            $image = Utils::store_image($request, $input['title'], 'blog', 'img');
        }

        $blog->update(array_merge($input, ['cover' => $image]));

        return $this->handleResponse(new BlogResource($blog), 'Blog successfully updated!');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Blog $blog)
    {
        $blog->delete();
        return $this->handleResponse([], $blog->title . ' deleted!');
    }
}
