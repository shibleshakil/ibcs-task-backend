<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // Check for Admin
        if (User::where('role', 'User')->where('email', $request->email)->doesntExist()) {
            return response([
                'message' => 'These credentials do not match our records.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response([
                'message' => 'These credentials do not match our records.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = Auth::user();

        $token = $user->createToken('token')->plainTextToken;

        $cookie = cookie('jwt', $token, (60 * 24 * 7)); // 7 day

        return response([
            'message' => $token
        ])->withCookie($cookie);
    }

    public function logout(Request $request)
    {
        $cookie = Cookie::forget('jwt', '/', 'localhost');

        return response([
            'message' => 'Success'
        ])->withCookie($cookie);
    }
}
