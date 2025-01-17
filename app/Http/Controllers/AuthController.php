<?php

namespace App\Http\Controllers;
use App\Models\Usuario;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            $user = Usuario::where('email', $validated['email'])->first();

            if (!$user || !Hash::check($validated['password'], $user->password)) {
                return response()->json([
                    'message' => 'Invalid credentials'
                ], 401);
            }

            if (!$user->habilitado) {
                return response()->json([
                    'message' => 'Account is disabled'
                ], 403);
            }

            $token = $user->createToken('auth_token')->plainTextToken;
            $user->ultima_conexion = now();
            $user->save();

            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error during login',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Successfully logged out'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error during logout',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function refresh(Request $request)
    {
        try {
            $user = $request->user();
            $user->tokens()->delete();

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Token refreshed successfully',
                'token' => $token,
                'token_type' => 'Bearer'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error refreshing token',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}
