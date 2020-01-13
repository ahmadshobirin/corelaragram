<?php

namespace App\Http\Controllers\Api;

use Auth;
use JWTAuth;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
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
                    'status'      => false,
                    'description' => 'Invalid Email or Password',
                ], 400);
            }
        } catch (JWTException $e) {
            return response()->json([
                'status'      => false,
                'description' => 'could not create token'
            ], 500);
        }

        $loginUser = Auth::user();
        $loginUser['token'] = $token;

        return (new UserResource($loginUser))->additional([
            'status' => [
                'code'        => 202,
                'description' => 'OK'
            ]
        ])->response()->setStatusCode(202);
    }

    public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate($request->bearerToken());
            return response()->json([
                'status'      => true,
                'description' => 'User logged out successfully'
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'status'        => false,
                'description'   => 'Sorry, the user cannot be logged out',
                'error message' => $e,
            ], 500);
        }
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|min   : 3|alpha_dash',
            'email'    => 'required|unique: users|email',
            'password' => 'required|min   : 3'
        ]);

        $newUser = User::create([
            'name'     => $request->name,
            'username' => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
        ]);


        if ($this->loginAfterSignUp) {
            $token = JWTAuth::fromUser($newUser);
            $newUser['token'] = $token;
        }

        $loginUser = Auth::user();
        $loginUser['token'] = $token;

        return (new UserResource($newUser))->additional([
            'status' => [
                'code'        => 201,
                'description' => 'User Created'
            ]
        ])->response()->setStatusCode(201);
    }

    public function me(Request $request)
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json([
                    "status"      => false,
                    "description" => 'user_not_found'
                ], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json([
                "status"      => false,
                "description" => 'token_expired'
            ], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json([
                "status"      => false,
                "description" => 'token_invalid'
            ], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json([
                "status"      => false,
                "description" => 'token_absent'
            ], $e->getStatusCode());
        }

        $user['token'] = $request->bearerToken();

        return (new UserResource($user))->additional([
            'status' => [
                'code'        => 200,
                'description' => 'User Created'
            ]
        ])->response()->setStatusCode(200);
    }
}
