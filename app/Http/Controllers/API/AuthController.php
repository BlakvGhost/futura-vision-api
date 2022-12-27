<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AuthController extends BaseController
{

    public function login(Request $request): JsonResponse
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $auth = Auth::user();
            $success['token'] = $auth->createToken('LaravelSanctumAuth')->plainTextToken;
            $success['role'] = $auth->role;

            return $this->handleResponse($success, 'User logged-in!');
        } else {
            return $this->handleError('Unauthorised.', ['error' => 'Unauthorised'], 202);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return $this->handleError($validator->errors(), [], 202);
        }

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('LaravelSanctumAuth')->plainTextToken;
        $success['role'] = $user->role;

        return $this->handleResponse($success, 'User successfully registered!');
    }

    public function user(Request $request): JsonResponse
    {
        $user = Auth::user();
        $user['token'] = $request->bearerToken();
        return $this->handleResponse($user, 'Welcome' . $user->first_name);
    }

}
