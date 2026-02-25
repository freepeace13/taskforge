<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()->where('email', $data['email'])->first();

        if (!$user) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 422);
        }

        if (is_null($user->password)) {
            return response()->json([
                'message' => 'Set your password first.',
            ], 422);
        }

        if (!Hash::check($data['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 422);
        }

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }
}
