<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Project;
use App\Utils\Utils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class ProjectController extends BaseController
{
    private $rules = [
        'Nom' => 'required',
        'URL' => 'required',
        'Category' => 'required',
        'Image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ];

    public function __construct()
    {
        $this->authorizeResource(Project::class);
    }
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $project = Project::orderBy('id', 'desc')->get();
        return $this->handleResponse($project, 'Projects have been retrieved!');
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

        $image = Utils::store_image($request, $input['Nom'], 'project', 'Image');

        $project = Project::create([
            'name' => $request->Nom,
            'link' => $request->URL,
            'cat' => $request->Category,
            'cover' => $image,
        ]);
        return $this->handleResponse($project, 'Project created!');
    }


    /**
     * @param $id
     * @return JsonResponse
     */
    public function show(Project $project): JsonResponse
    {
        return $this->handleResponse($project, 'Project retrieved.');
    }


    public function update(Request $request, Project $project): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'Nom' => 'required',
            'URL' => 'required',
            'Category' => 'required',
            'Image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->handleError($validator->errors());
        }

        $image = $project->cover;
        if ($request->file('Image')) {
            $image = Utils::store_image($request, $input['Nom'], 'project', 'Image');
        }

        $project->update([
            'name' => $request->Nom,
            'link' => $request->URL,
            'cat' => $request->Category,
            'cover' => $image,
        ]);

        return $this->handleResponse($project, 'Project successfully updated!');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Project $project)
    {
        $project->delete();
        return $this->handleResponse([], $project->name . ' deleted!');
    }
}
