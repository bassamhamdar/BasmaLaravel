<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $user = User::create([
             'name' => $request->name,
             'email'    => $request->email,
             'password' => $request->password,
         ]);

        $token = auth()->login($user);

        return $this->respondWithToken($token);
    }

    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth('admin')->attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 200);
        }

        return response()->json([
            'success'=> true,
            'message'=>'you logged in successfully',
            'token'=> $token,
        ]);
    }

    public function logout()
    {
        auth('admin')->logout();

        return response()->json([
            'success'=> true,
            'message' => 'Successfully logged out']);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth()->factory()->getTTL() * 60
        ]);
    }
}