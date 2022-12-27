<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Technology;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class TechnologyController extends BaseController
{
    private $rules = [
        'name' => 'required',
        'content' => 'required',
        'icon' => 'required',
    ];

    public function __construct()
    {
        $this->authorizeResource(Technology::class);
    }
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $technology = Technology::orderBy('id', 'desc')->get();
        return $this->handleResponse($technology, 'Technologies have been retrieved!');
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $input = $request->post();

        $validator = Validator::make($input, $this->rules);
        if ($validator->fails()) {
            return $this->handleError($validator->errors(), [], 302);
        }

        $technology = Technology::create($input);
        return $this->handleResponse($technology, 'Technology created!');
    }


    /**
     * @param $id
     * @return JsonResponse
     */
    public function show(Technology $technology): JsonResponse
    {
        return $this->handleResponse($technology, 'Technology retrieved.');
    }


    public function update(Request $request, Technology $technology): JsonResponse
    {
        $input = $request->post();

        $validator = Validator::make($input, $this->rules);

        if ($validator->fails()) {
            return $this->handleError($validator->errors());
        }

        $technology->update($input);

        return $this->handleResponse($technology, 'Technology successfully updated!');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Technology $technology)
    {
        $technology->delete();
        return $this->handleResponse([], $technology->name . ' deleted!');
    }
}
