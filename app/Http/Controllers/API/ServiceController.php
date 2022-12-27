<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Service;
use App\Utils\Utils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class ServiceController extends BaseController
{
    private $rules = [
        'name' => 'required',
        'logo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'content' => 'required',
    ];

    public function __construct()
    {
        $this->authorizeResource(Service::class);
    }
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $service = Service::orderBy('id', 'desc')->get();
        return $this->handleResponse($service, 'Services have been retrieved!');
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
        $image = null;
        if ($request->logo) {
            $image = Utils::store_image($request, $input['name'], 'service', 'logo');
        }

        $service = Service::create([
            'name' => $request->name,
            'logo' => $image,
            'icon' => $request->icon,
            'content' => $request->content,
        ]);
        return $this->handleResponse($service, 'Service created!');
    }


    /**
     * @param $id
     * @return JsonResponse
     */
    public function show(Service $service): JsonResponse
    {
        return $this->handleResponse($service, 'Service retrieved.');
    }


    public function update(Request $request, Service $service): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, $this->rules);

        if ($validator->fails()) {
            return $this->handleError($validator->errors());
        }

        $image = $service->logo;
        if ($request->file('logo')) {
            $image = Utils::store_image($request, $input['logo'].
            '-' . date('Y-m-d H-i-s'), 'service', 'logo');
        }

        $service->update([
            'name' => $request->name,
            'logo' => $image,
            'icon' => $request->icon,
            'content' => $request->content,
        ]);

        return $this->handleResponse($service, 'Service successfully updated!');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Service $service)
    {
        $service->delete();
        return $this->handleResponse([], $service->name . ' deleted!');
    }
}
