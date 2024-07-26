<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use App\Http\ApiResponse;
use Closure;
use Exception;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use App\Models\User; // Make sure to import your User model

class AuthMiddleware extends BaseMiddleware
{
    public function handle(Request $request, Closure $next, $guard = null)
    {
        if ($guard !== null) {
            auth()->shouldUse($guard);
            try {
                $user = JWTAuth::parseToken()->authenticate();

            } catch (TokenExpiredException $e) {

                error_log($e->getMessage());
                return ApiResponse::error('Token expired', 401);

            } catch (TokenInvalidException $e) {
                error_log($e->getMessage());
                return ApiResponse::error('Invalid token', 400);
            } catch (Exception $e) {
                error_log($e->getMessage());
                return ApiResponse::error('Invalid token', 400);
            }
        }
        return $next($request);
    }
}
