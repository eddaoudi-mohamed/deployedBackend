<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Traits\GeneraleTrait;
use Tymon\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class AuthAdmin
{
    use GeneraleTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $guard = null): Response
    {
        if ($guard != null) {
            auth()->shouldUse($guard);
            try {
                $token = $request->cookie('access_token');
                $request->headers->set('auth-token', (string) $token, true);
                $request->headers->set('Authorization', 'Bearer ' . $token, true);
                $admin = JWTAuth::parseToken()->authenticate();
            } catch (TokenExpiredException $e) {

                if ($request->routeIs('api.refresh')) {
                    // Allow the request to proceed to the refresh method
                    return $next($request);
                } else {
                    // Otherwise, return an error response
                    return $this->errorResponse(["data" => ["message" => "Token is expired "]], 401);
                }
            } catch (\Exception $th) {
                return $this->errorResponse(["data" => ["message" => "Not Authentication"]], 401);
            }
        }

        return $next($request);
    }
}
