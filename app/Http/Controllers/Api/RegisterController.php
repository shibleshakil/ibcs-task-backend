<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'min:4'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:5'],
            'image' => ['nullable', 'image'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors()
            ], 422);
        }

        $image = NULL;

        if ($request->hasFile('image')) {
            $image = $request->file('image')->store('users');
        }

        try {
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'image' => $image,
                'password' => Hash::make($request->password),
                'role' => "User"
            ]);

            return response()->json(['success' => 'Registration Successfull'], 200);
        } catch (\Throwable $th) {
            Log::error($th->getFile());
            Log::error($th->getLine());
            Log::error($th->getMessage());

            return response()->json(['error' => 'Internal Server Error'], 500);

        }
    }
}
