<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'phone_number' => $request->phone_number,
            ]);
        } catch (\Exception $e) {
            throw new \App\Exceptions\QueryDBException(__('Something went wrong.'));
        }

        return response()->json([
            'token' => $this->initToken($user),
            'user' => $user,
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw new \App\Exceptions\BadRequestException(__('The provided credentials are incorrect.'));
        }

        if (!$user->email_verified_at) {
            throw new \App\Exceptions\BadRequestException(__('Please verify your email.'));
        }

        $user->tokens()->delete();

        return response()->json([
            'token' => $this->initToken($user),
            'user' => $user,
        ], 200);
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();
        } catch (\Exception $e) {
            return response()->json(['message' => __('Something went wrong.')], 500);
        }
        return response()->json(['message' => __('Logged out successfully.')]);
    }

    protected function initToken($user)
    {
        return $user->createToken(
            date("Y-m-d H:i:s:ms"),
            ['*'],
            config('sanctum.expiration') ? now()->addMinutes(config('sanctum.expiration')) : null
        )->plainTextToken;
    }
}
