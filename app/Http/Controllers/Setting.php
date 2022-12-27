<?php

namespace App\Http\Controllers;

use App\Utils\Utils;
use App\Models\About;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController as BaseController;

class Setting extends BaseController
{
    private $rules = [
        'logo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ];

    private $path = "setting/ui.json";
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $data = json_decode(Storage::get($this->path));

        return $this->handleResponse($data, 'Setting data have been retrieved!');
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        Gate::authorize('onlyAdmin');

        $files = $request->file();
        $data = $request->post();

        $validator = Validator::make($files, $this->rules);
        if ($validator->fails()) {
            return $this->handleError($validator->errors(), [], 302);
        }
        
        foreach ($files as $key => $file) {
            $data[$key] = Utils::store_image($request, "cover-" . $key, 'setting', $key);            
        }

        $data = json_encode($data);

        if (!Storage::put($this->path, $data)) {
            return $this->handleError("Erreur d'insertion", null, 302);
        }

        return $this->handleResponse($data, 'Setting data have been saved successufy !');
    }
}
