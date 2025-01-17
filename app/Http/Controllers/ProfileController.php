<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        try {
            $user = $request->user();

            // Load menu relationships if needed
            $user->load('menuUsuarios');

            return response()->json([
                'user' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $user = $request->user();

            $validated = $request->validate([
                'nombre' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:usuario,email,' . $user->id,
                'celular' => 'sometimes|string|max:15',
                'whatsapp' => 'nullable|string|max:15',
                'nickname' => 'nullable|string|max:255|unique:usuario,nickname,' . $user->id,
            ]);

            $user->update($validated);

            return response()->json([
                'message' => 'Profile updated successfully',
                'user' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            $validated = $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed',
            ]);

            $user = $request->user();

            if (!Hash::check($validated['current_password'], $user->password)) {
                return response()->json([
                    'message' => 'Current password is incorrect'
                ], 400);
            }

            $user->password = Hash::make($validated['new_password']);
            $user->save();

            return response()->json([
                'message' => 'Password updated successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating password',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
