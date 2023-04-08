<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthSigninRequest;
use App\Http\Requests\AuthSignupRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function signup(AuthSignupRequest $request)
    {
        $username = $request->get('username');
        $password = $request->get('password');

        $newUser = User::create([
            'username' => $username,
            'password' => Hash::make($password),
            'registered_timestamp' => Carbon::now()->toDateTimeString(),
            'last_login_timestamp' => Carbon::now()->toDateTimeString()
        ]);

        $token = $newUser->createToken('token');

        return response()->json([
            'status' => 'success',
            'token' => $token->plainTextToken
        ], 201);
    }

    public function signin(AuthSigninRequest $request)
    {
        $username = $request->get('username');
        $password = $request->get('password');

        $existingUser = User::query()->where('username', $username)->first();

        if (!$existingUser || !Hash::check($password, $existingUser->password)) {
            return response()->json([
                'status' => 'invalid',
                'message' => 'Wrong username or password'
            ], 401);
        }

        $token = $existingUser->createToken('token');
        $existingUser->last_login_timestamp = Carbon::now()->toDateTimeString();
        $existingUser->save();

        return response()->json([
            'status' => 'success',
            'token' => $token->plainTextToken
        ], 200);
    }

    public function signout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
        ], 200);
    }
}
