<?php

namespace App\Http\Controllers\Auth\admins;

use App\Models\Admin;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;

class AdminAuthController extends Controller
{

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:8',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
    
        try {
            $admin = new Admin([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'location' => $request->input('location'),
                'role' => $request->input('role'),
                'password' => Hash::make($request->input('password')),
            ]);
    
            $admin->save();
    
            return response()->json(['message' => 'Admin registered successfully'], 201);
    
        } catch (\Exception $e) {
            // Log the error message
            \Log::error('Error during admin registration: ' . $e->getMessage());
    
            // Check if the error is related to a duplicate entry
            if ($e->getCode() == 23000) { // 23000 is the SQLSTATE code for integrity constraint violation
                return response()->json(['error' => 'Email already exists'], 409);
            }
    
            // Return a generic error response if it's another type of error
            return response()->json(['error' => 'An error occurred during registration'], 500);
        }
    }
    

    public function login(Request $request)
    {
          $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            $admin = Auth::guard('admin')->user();
            $token = JWTAuth::fromUser($admin);
            // $token = $admin->createToken('access_token')->accessToken;


            $user =  [
                "id"=> $admin->id,
                "email"=> $admin->email,
                "name"=> $admin->name,
                "role"=> $admin->role,
            ];


            return response()->json(['token' => $token,'user'=>$user]);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    public function checkTokenExpiration(Request $request)
{
    // $token = $request->token;
    $token = $request->bearerToken();

    try {
        // Check if the token is valid and get the authenticated admin
        $user = Auth::guard('admin')->setToken($token)->authenticate();

        // Check if the token's expiration time (exp) is greater than the current timestamp
        $isExpired = JWTAuth::setToken($token)->checkOrFail();

        return response()->json(['message' => 'Token is valid', 'admin' => $user], 200);
    } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
        // Token has expired
        return response()->json(['message' => 'Token has expired'], 401);
    } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
        // Token is invalid
        return response()->json(['message' => 'Invalid token'], 401);
    } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
        // Token not found or other JWT exception
        return response()->json(['message' => 'Error while processing token'], 500);
    }
}




public function logout(Request $request)
{
    try {
        $token = $request->bearerToken();
        if ($token) {
            JWTAuth::setToken($token)->invalidate();
            return response()->json(['message' => 'Logged out successfully']);
        } else {
            return response()->json(['message' => 'Invalid token'], 401);
        }
    } catch (JWTException $e) {
        return response()->json(['message' => 'Error while processing token'], 500);
    }
}

    public function checkToken(Request $request)
    {
        $admin = $request->user('admin');
        if ($admin) {
            return response()->json(['message' => 'Token is valid']);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }


}
