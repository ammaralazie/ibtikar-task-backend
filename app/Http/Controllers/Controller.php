<?php

namespace App\Http\Controllers;

use App\Http\ApiResponse;
use App\Http\Requests\User\LoginRequest;
use App\Services\AuthService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        $this->middleware('authApi:api')->only(['homePage']);
    }

    public function login(LoginRequest $request): \Illuminate\Http\JsonResponse
    {
        $credentials = ['username' => $request->username, 'password' => $request->password];

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return ApiResponse::error('Wrong credentials', 422);
        }

        $user = Auth::guard('api')->user();

        if ($user->freeIPA) {
            // freeIPA is true, deny access
            Auth::guard('api')->logout();
            return ApiResponse::error('Access denied for users with freeIPA enabled', 403);
        }

        return ApiResponse::success(['userData' => $user, 'accessToken' => $token], 'success', 200);
    }// /login

    public function loginByFreeIPA(LoginRequest $request): \Illuminate\Http\JsonResponse
    {

        $credentials = ['username' => $request->username, 'password' => $request->password];

        $data = (new \App\Services\AuthService)->authenticateUser($credentials);

        if($data)
            return ApiResponse::success($data,'success',200);

        return ApiResponse::error('Wrong credentials',422);
    }// /loginByFreeIPA

    public function homePage(): \Illuminate\Http\JsonResponse
    {
        return ApiResponse::success('The user has the authority on the site.','success',200);
    }// /homePage

}
