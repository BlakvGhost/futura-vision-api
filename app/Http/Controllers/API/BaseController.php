<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    public function handleResponse($result, $msg): \Illuminate\Http\JsonResponse
    {
        $res = [
            'success' => true,
            'data' => $result,
            'message' => $msg,
        ];
        return response()->json($res, 200);
    }

    public function handleError($error, $errorMsg = [], $code = 404): \Illuminate\Http\JsonResponse
    {
        $res = [
            'success' => false,
            'message' => $error,
        ];
        if (!empty($errorMsg)) {
            $res['data'] = $errorMsg;
        }
        return response()->json($res, $code);
    }
}
