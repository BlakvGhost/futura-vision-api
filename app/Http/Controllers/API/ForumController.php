<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\Blog as ForumResource;
use App\Models\Forum;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class ForumController extends BaseController
{
    private $rules = [
        'category_id' => 'required',
        'content' => 'required',
    ];

    public function __construct()
    {
        $this->authorizeResource(Forum::class);
    }
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $forum = Forum::orderBy('id', 'desc')->get();
        return $this->handleResponse($forum, 'Forums have been retrieved!');
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

        $forum = Forum::create(array_merge($input, [
                'user_id' => $request->user()->id,
            ]
        ));
        return $this->handleResponse(new ForumResource($forum), 'Forum created!');
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function show(Forum $forum): JsonResponse
    {
        return $this->handleResponse(new ForumResource($forum), 'Forum retrieved.');
    }


    public function update(Request $request, Forum $forum): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, $this->rules);

        if ($validator->fails()) {
            return $this->handleError($validator->errors());
        }

        $forum->update($input);

        return $this->handleResponse(new ForumResource($forum), 'Forum successfully updated!');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Forum $forum)
    {
        $forum->delete();
        return $this->handleResponse([], $forum->title . ' deleted!');
    }
}
