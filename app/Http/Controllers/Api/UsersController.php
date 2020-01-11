<?php

namespace App\Http\Controllers\Api;

use JWTAuth;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;


class UsersController extends Controller
{

    private $loginAfterSignUp = true;

    public function login(Request $request)
    {
        $token = null;
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid Email or Password',
                ], 400);
            }
        } catch (JWTException $e) {
            return response()->json([
                'status' => false,
                'message' => 'could not create token'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {

        $request->validate([
            'token' => 'required'
        ]);

        try {
            JWTAuth::invalidate($request->token);
            return response()->json([
                'status' => true,
                'message' => 'User logged out successfully'
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Sorry, the user cannot be logged out',
                'jwt' => $e
            ], 500);
        }
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3|alpha_dash',
            'email' => 'required|unique:users|email',
            'password' => 'required|min:3'
        ]);

        $newUser = User::create([
            'name' => $request->name,
            'username' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);


        if ($this->loginAfterSignUp) {
            //     return $this->login($request);
            $token = JWTAuth::fromUser($newUser);
            $newUser['token'] = $token;
        }

        return response()->json([
            'status'   =>  true,
            'message'  =>  $newUser
        ], 200);
    }

    public function me()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json([
                    "status" => false,
                    "message" => 'user_not_found'
                ], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json([
                "status" => false,
                "message" => 'token_expired'
            ], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json([
                "status" => false,
                "message" => 'token_invalid'
            ], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json([
                "status" => false,
                "message" => 'token_absent'
            ], $e->getStatusCode());
        }

        return response()->json([
            "status" => true,
            "message" => $user,
        ], 200);
    }
}
