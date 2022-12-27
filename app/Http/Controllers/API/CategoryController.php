<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class CategoryController extends BaseController
{
    private $rules = [
        'name' => 'required',
        'icon' => 'required',
        'desc' => 'required',
    ];

    public function __construct()
    {
        $this->authorizeResource(Category::class);
    }
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $category = Category::orderBy('id', 'desc')->get();
        return $this->handleResponse($category, 'Technologies have been retrieved!');
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

        $category = Category::create($input);
        return $this->handleResponse($category, 'Category created!');
    }


    /**
     * @param $id
     * @return JsonResponse
     */
    public function show(Category $category): JsonResponse
    {
        return $this->handleResponse($category, 'Category retrieved.');
    }


    public function update(Request $request, Category $category): JsonResponse
    {
        $input = $request->post();

        $validator = Validator::make($input, $this->rules);

        if ($validator->fails()) {
            return $this->handleError($validator->errors());
        }

        $category->update($input);

        return $this->handleResponse($category, 'Category successfully updated!');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return $this->handleResponse([], $category->name . ' deleted!');
    }
}
