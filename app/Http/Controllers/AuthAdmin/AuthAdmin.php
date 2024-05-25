<?php

namespace App\Http\Controllers\AuthAdmin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\GeneraleTrait;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AuthAdmin extends Controller
{
    use GeneraleTrait;
    public function __construct()
    {
        $this->middleware('auth.admin:api', ['except' => ['login']]);
    }


    public function login(Request $request)
    {
        $rules = [
            'email' => "required|string",
            'password' => "required|string",
        ];
        $credentials = $request->only(['email', "password"]);
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            $message = $validator->messages();
            return $this->errorResponse(["data" => ['messages' => $message]], 400);
        }

        $token = JWTAuth::attempt($credentials);
        if (!$token) {
            return $this->errorResponse(["data" => ["message" => "credentials not correct "]], 400);
        }
        $admin = JWTAuth::user();
        $refreshToken = JWTAuth::fromUser(JWTAuth::user());
        $expiresIn = JWTAuth::factory()->getTTL() * 60; // TTL is in minutes, multiply by 60 for seconds
        $expirationDate = Carbon::now()->addSeconds($expiresIn);

        $response = response()->json(
            [
                "data" => ["message" => "Logged in successfully", "data" => $admin]
            ]
        );

        // Set the access token cookie
        $response->cookie('access_token', $token, $expiresIn, '/', null, true, true);

        // Set the refresh token cookie
        $response->cookie('refresh_token', $refreshToken, null, '/', null, true, true);

        // Set the expiration date cookie (optional)
        $response->cookie('expirationDate', $expirationDate->toISOString(), $expiresIn, '/', null, true, true);

        return $response;
    }



    public function logout(Request $request)
    {
        $token = $request->cookie("access_token");

        if ($token) {
            try {
                JWTAuth::setToken($token)->invalidate();
                $response = response()->json(
                    [
                        "data" => ["message" => "logout successfully"]
                    ]
                );

                // Set the access token cookie
                $response->cookie('access_token', '', -1, '/', null, true, true);

                // Set the refresh token cookie
                $response->cookie('refresh_token', '', -1, '/', null, true, true);

                // Set the expiration date cookie (optional)
                $response->cookie('expirationDate', '', -1, '/', null, true, true);



                return $response;
            } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
                return $this->errorResponse(["data" => ["message" => "some thing worng"]], 400);
            }
        } else {
            return $this->errorResponse(["data" => ["message" => "some thing worng"]], 400);
        }
    }


    public function refresh()
    {

        try {
            // Get the current token
            $token = JWTAuth::getToken();

            // Refresh the token
            $newToken = JWTAuth::refresh($token);

            $expiresIn = JWTAuth::factory()->getTTL() * 60; // TTL is in minutes, multiply by 60 for seconds

            $expirationDate = Carbon::now()->addSeconds($expiresIn);



            // Return the new access token
            $response = response()->json(
                [
                    "data" => ["message" => "refresh token successfully"]
                ]
            );

            // Set the access token cookie
            $response->cookie('access_token', $newToken, $expiresIn, '/', null, true, true);

            // Set the expiration date cookie (optional)
            $response->cookie('expirationDate', $expirationDate->toISOString(), $expiresIn, '/', null, true, true);

            return $response;

        } catch (\Throwable $e) {
            return $this->errorResponse(["data" => ["message" => "some thing worng"]], 400);
        }
    }
}
