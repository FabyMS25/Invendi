<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

use function App\Providers\apiResponse;
use App\Models\Usuario;

class UserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $validated = $request->validate([
                'habilitado' => 'nullable|boolean',
                'tipo_usuario_id' => 'nullable|integer',
                'search' => 'nullable|string',
                'per_page' => 'nullable|integer|min:1',
            ]);
            $query = Usuario::query();
            if ($request->has('habilitado')) {
                $query->where('habilitado', $validated['habilitado']);
            }

            if (isset($validated['tipo_usuario_id'])) {
                $query->where('tipo_usuario_id', (int)$validated['tipo_usuario_id']);
            }

            if (!empty($validated['search'])) {
                $search = $validated['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('nombre', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%")
                        ->orWhere('nickname', 'LIKE', "%{$search}%");
                });
            }

            if ($request->per_page === 'all') {
                $data = $query->paginate(min($query->count(), 1000));
            } else {
                $data = $query->paginate($request->per_page ?? 20);
            }
            return apiResponse([
                'data' => $data->items(),
                'pagination' => $data
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error fetching users: ' . $e->getMessage());
            return apiResponse([
                'message' => 'Error retrieving users',
                'error' => $e->getMessage(),
            ],500);
        }
    }
    public function show(Request $request, $id)
    {
        try {
            $query = Usuario::query();
            if ($request->has('with_menu')) {
                $query->with('menuUsuario');
            }
            $user = $query->find($id);

            if (!$user) {
                return response(['message' => 'User not found'],404);
            }

            return response(['message' => 'User retrieved successfully', 'data' => $user],200);
        } catch (\Exception $e) {
            Log::error('Error fetching user: ' . $e->getMessage());
            return response(['message' => 'Error fetching user', 'error' => $e->getMessage()],500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'email' => 'required|email|unique:usuario,email',
                'password' => 'required|string|min:8',
                'celular' => 'required|string|max:20',
                'whatsapp' => 'nullable|string|max:20',
                'nickname' => 'nullable|string|max:255|unique:usuario,nickname',
                'habilitado' => 'nullable|boolean',
                'ayuda' => 'nullable|boolean',
                'ultima_conexion' => 'nullable|date',
                'tipo_usuario_id' => 'nullable|integer'
            ]);
            $validated['password'] = bcrypt($validated['password']);
            $validated['habilitado'] = $validated['habilitado'] ?? true;
            $validated['ayuda'] = $validated['ayuda'] ?? false;

            $user = Usuario::create($validated);
            return response($user,201);
        } catch (ValidationException $e) {
        return response([
            'message' => 'Validation error',
            'error' => $e->errors()
        ],422);
        } catch (\Exception $e) {
            return response([
                'message' => 'Error creating user',
                'error' => $e->getMessage()
            ],500);
        }
    }

    public function update(Request $request, $id)
    {
        $user = Usuario::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        try {
            $validated = $request->validate([
                'nombre' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:usuario,email,' . $id,
                'password' => 'sometimes|string|min:8',
                'celular' => 'sometimes|string|max:15',
                'whatsapp' => 'nullable|string|max:15',
                'nickname' => 'nullable|string|max:255|unique:usuario,nickname,' . $id,
                'habilitado' => 'nullable|boolean',
                'ayuda' => 'nullable|boolean',
                'ultima_conexion' => 'nullable|date',
                'tipo_usuario_id' => 'nullable|integer'
            ]);

            if (!empty($validated['password'])) {
                $validated['password'] = bcrypt($validated['password']);
            }

            $user->update($validated);

            return response()->json($user, 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error updating user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = Usuario::find($id);
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $user->delete();
            return response()->json(['message' => 'User deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function verifyEmail($id)
    {
        $user = Usuario::find($id);
        if ($user) {
            $user->email_verified_at = now();
            $user->save();
            return response()->json(['message' => 'Email verified successfully'], 200);
        }
        return response()->json(['message' => 'Usuario not found'], 404);
    }

    /**
     * Update user status
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $user = Usuario::find($id);
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $validated = $request->validate([
                'habilitado' => 'required|boolean'
            ]);

            $user->update($validated);

            return response()->json([
                'message' => 'User status updated successfully',
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating user status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function rememberMe($id)
    {
        try {
            $user = Usuario::find($id);
            if ($user) {
                $user->remember_token = Str::random(60);
                $user->save();
                return response()->json(['message' => 'Remember token updated successfully'], 200);
            }
            return response()->json(['message' => 'Usuario not found'], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating remember token',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
