<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Utils\Utils;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController as BaseController;


class UserController extends BaseController
{
    private $rules = [
        'first_name' => 'required|string',
        'last_name' => 'required|string',
        'email' => 'required|email',
        'password' => 'required|min:8',
        'confirm_password' => 'required|same:password',
        'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ];

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $user = User::orderBy('id', 'desc')->get();
        return $this->handleResponse($user, 'Users have been retrieved!');
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {

        return $this->handleResponse(true, 'User created!');
    }


    /**
     * @param $id
     * @return JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        return $this->handleResponse($user, 'User retrieved.');
    }


    public function update(Request $request, User $user): JsonResponse
    {
        if ($request->user()->id !== $user->id) {
            return $this->handleError("Vous ne pouvez Pas editer ce compte", null, 202);
        }
        $input = $request->all();

        $validator = Validator::make($input, $this->rules);

        if ($validator->fails()) {
            return $this->handleError($validator->errors(), null, 202);
        }

        $image = $user->avatar;
        if ($request->file('cover')) {
            $image = Utils::store_image(
                $request,
                $input['first_name'] . '-' . $input['last_name'],
                'user',
                'cover'
            );
        }

        $input['password'] = Hash::make($input['password']);
        $user->update(array_merge($input, ['avatar' => $image]));

        $user['token'] = $request->bearerToken();

        return $this->handleResponse($user, 'User successfully updated!');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(User $user)
    {
        $user->delete();
        return $this->handleResponse([], $user->first_name . $user->last_name . ' deleted!');
    }
}
