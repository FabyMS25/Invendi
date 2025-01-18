<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\Usuario;

use function App\Providers\apiResponse;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // return response()->json(Usuario::all(), 200);
        try {
            $query = Usuario::query();

            // Add filters
            if ($request->has('habilitado')) {
                $query->where('habilitado', $request->habilitado);
            }
            if ($request->has('tipo_usuario_id')) {
                $query->where('tipo_usuario_id', $request->tipo_usuario_id);
            }

            // Add search
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nombre', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%")
                      ->orWhere('nickname', 'LIKE', "%{$search}%");
                });
            }

            // Add pagination
            $usuarios = $query->paginate($request->per_page ?? 10);
            return apiResponse(200, 'Users fetched successfully', $usuarios->items(), [
            'total' => $usuarios->total(),
            'per_page' => $usuarios->perPage(),
            'current_page' => $usuarios->currentPage(),
            'last_page' => $usuarios->lastPage(),
            'from' => $usuarios->firstItem(),
            'to' => $usuarios->lastItem(),
        ]);
        } catch (\Exception $e) {
            return apiResponse(500, 'An unexpected error occurred. Please try again later.', [], [], $e->getMessage());
        }
    }
    public function show(Request $request, $id)
    {
        try {
            $query = Usuario::query();

            // Load relationships if requested
            if ($request->has('with_menu')) {
                $query->with('menuUsuarios');
            }

            $user = $query->find($id);

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            // return response()->json($user, 200);
            return response()->json([
                'message' => 'User retrieved successfully',
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching user',
                'error' => $e->getMessage()
            ], 500);
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
            // default values for nullable fields if not provided
            $validated['habilitado'] = $validated['habilitado'] ?? true;
            $validated['ayuda'] = $validated['ayuda'] ?? false;

            $user = Usuario::create($validated);
            // return response()->json($user, 201);
            return response()->json([
                'message' => 'User created successfully',
                'user' => $user
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $user = Usuario::find($id);

        if ($user) {
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

            if (isset($validated['password'])) {
                $validated['password'] = bcrypt($validated['password']);
            }

            $user->update($validated);
            // return response()->json($user, 200);
            return response()->json([
                'message' => 'User updated successfully',
                'user' => $user
            ], 200);

          } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating user',
                'error' => $e->getMessage()
            ], 500);
        }
        }

        return response()->json(['message' => 'User not found'], 404);
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
                'user' => $user
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
