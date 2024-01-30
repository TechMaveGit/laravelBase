<?php

namespace App\Http\Controllers\Api\V2\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register','refresh']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return  response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'response' => $validator->errors()
            ], 422);
        }elseif(!User::where(['email'=>$request->email])->exists()){
            return  response()->json([
                'status' => false,
                'message' => 'The selected email is invalid.'
            ], 422);
        }elseif (!$token = JWTAuth::attempt($validator->validated())) {
            return  response()->json([
                'status' => false,
                'message' => 'Invalid Credentials.'
            ], 422);
        }

        return $this->respondWithToken($token);
    }
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users,email',
            'password' => 'required|min:6',
        ]);
        if($validator->fails()){
            return  response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'response' => $validator->errors()
            ], 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'cleaner',
            'status' => 0
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'status' => true,
            'message' => 'User successfully registered',
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
        return response()->json([
            'status' => true,
            'message' => 'Successfully logged out'
        ]);
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|exists:users,email',
        ]);
        if($validator->fails()){
            return  response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'response' => $validator->errors()
            ], 400);
        }else if($request->email){
            $user = User::where('email', $request->email)->where('status','active')->first();
            if (!$newToken = auth()->login($user)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        }else{
            try {
                $token = JWTAuth::getToken();
                $newToken = JWTAuth::refresh($token);
            } catch (\Tymon\JWTAuth\Exceptions\TokenBlacklistedException $e) {
                // Token has been blacklisted (e.g., user logged out or token invalidated)
                return response()->json(['error' => 'Token has been blacklisted. Please log in again.'], 401);
            } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
                // Token and refresh token have both expired
                return response()->json(['error' => 'Token expired. Please log in again.'], 401);
            } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
                // Token is invalid
                return response()->json(['error' => 'Token is invalid.'], 401);
            }
        }
        return $this->createNewToken($newToken);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token)
    {
        return response()->json([
            'status' => true,
            'image_root' => env('APP_URL'),
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * (60*24*7),
            'user' => auth()->user()
        ]);
    }


}
