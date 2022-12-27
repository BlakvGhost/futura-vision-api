<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Team;
use App\Utils\Utils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class TeamController extends BaseController
{
    private $rules = [
        'Nom' => 'required',
        'Prenom' => 'required',
        'Role' => 'required',
        'Biographie' => 'required',
        'Photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ];

    public function __construct()
    {
        $this->authorizeResource(Team::class);
    }
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $team = Team::orderBy('id', 'desc')->get();
        return $this->handleResponse($team, 'Teams have been retrieved!');
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

        $image = Utils::store_image($request, $input['Nom'] . '-' . $input['Prenom'], 'team', 'Photo');

        $team = Team::create([
            'first_name' => $request->Prenom,
            'last_name' => $request->Nom,
            'role' => $request->Role,
            'bio' => $request->Biographie,
            'avatar' => $image,
        ]);
        return $this->handleResponse($team, 'Team created!');
    }


    /**
     * @param $id
     * @return JsonResponse
     */
    public function show(Team $team): JsonResponse
    {
        return $this->handleResponse($team, 'Team retrieved.');
    }


    public function update(Request $request, Team $team): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'Nom' => 'required',
            'Prenom' => 'required',
            'Role' => 'required',
            'Biographie' => 'required',
            'Photo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->handleError($validator->errors());
        }

        $image = $team->avatar;
        if ($request->file('Photo')) {
            $image = Utils::store_image($request, $input['Nom'] . '-' . $input['Prenom'], 'team', 'Photo');
        }

        $team->update([
            'first_name' => $request->Prenom,
            'last_name' => $request->Nom,
            'role' => $request->Role,
            'bio' => $request->Biographie,
            'avatar' => $image,
        ]);

        return $this->handleResponse($team, 'Team successfully updated!');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Team $team)
    {
        $team->delete();
        return $this->handleResponse([], $team->name . ' deleted!');
    }
}
