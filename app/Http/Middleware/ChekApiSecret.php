<?php

namespace App\Http\Middleware;

use App\Traits\GeneraleTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ChekApiSecret
{
    use GeneraleTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiSecret = $request->header("API_SECRET");
        if ($apiSecret === env("API_SECRET")) {
            return $next($request);
        }
        return $this->errorResponse(["data" => ["message" => "You have something not right"]], 401);
    }
}
