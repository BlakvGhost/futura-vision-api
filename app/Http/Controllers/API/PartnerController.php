<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Partner;
use App\Utils\Utils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class PartnerController extends BaseController
{
    private $rules = [
        'name' => 'required',
        'logo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ];

    public function __construct()
    {
        $this->authorizeResource(Partner::class);
    }
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $partner = Partner::orderBy('id', 'desc')->get();
        return $this->handleResponse($partner, 'Partners have been retrieved!');
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
            $image = Utils::store_image($request, $input['name'], 'partner', 'logo');
        }

        $partner = Partner::create([
            'name' => $request->name,
            'logo' => $image,
            'icon' => $request->icon,
        ]);
        return $this->handleResponse($partner, 'Partner created!');
    }


    /**
     * @param $id
     * @return JsonResponse
     */
    public function show(Partner $partner): JsonResponse
    {
        return $this->handleResponse($partner, 'Partner retrieved.');
    }


    public function update(Request $request, Partner $partner): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, $this->rules);

        if ($validator->fails()) {
            return $this->handleError($validator->errors());
        }

        $image = $partner->logo;
        if ($request->file('logo')) {
            $image = Utils::store_image($request, $input['logo'].
            '-' . date('Y-m-d H-i-s'), 'partner', 'logo');
        }

        $partner->update([
            'name' => $request->name,
            'logo' => $image,
            'icon' => $request->icon,
        ]);

        return $this->handleResponse($partner, 'Partner successfully updated!');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Partner $partner)
    {
        $partner->delete();
        return $this->handleResponse([], $partner->name . ' deleted!');
    }
}