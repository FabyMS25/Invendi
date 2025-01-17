<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


class PasswordResetController extends Controller
{
    public function forgot(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email|exists:usuario,email',
            ]);

            $user = Usuario::where('email', $validated['email'])->first();

            if (!$user) {
                return response()->json([
                    'message' => 'User not found'
                ], 404);
            }

            $token = Str::random(64);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $validated['email']],
                [
                    'token' => Hash::make($token),
                    'created_at' => now()
                ]
            );

            // Here you would typically send an email with the reset link
            // For now, we'll return the token in the response
            return response()->json([
                'message' => 'Password reset link sent successfully',
                'token' => $token
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error sending reset link',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function reset(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email|exists:usuario,email',
                'token' => 'required|string',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $resetRecord = DB::table('password_reset_tokens')
                ->where('email', $validated['email'])
                ->first();

            if (!$resetRecord || !Hash::check($validated['token'], $resetRecord->token)) {
                return response()->json([
                    'message' => 'Invalid or expired token'
                ], 400);
            }

            $user = Usuario::where('email', $validated['email'])->first();

            if (!$user) {
                return response()->json([
                    'message' => 'User not found'
                ], 404);
            }

            $user->password = Hash::make($validated['password']);
            $user->save();

            DB::table('password_reset_tokens')
                ->where('email', $validated['email'])
                ->delete();

            return response()->json([
                'message' => 'Password reset successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error resetting password',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
