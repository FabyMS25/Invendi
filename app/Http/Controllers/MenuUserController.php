<?php

namespace App\Http\Controllers;

use App\Models\MenuUsuario;
use Illuminate\Http\Request;

class MenuUserController extends Controller
{
    public function index()
    {
        return response()->json(MenuUsuario::all());
    }

    public function show($id)
    {
        $userMenu = MenuUsuario::find($id);
        if ($userMenu) {
            return response()->json($userMenu);
        }
        return response()->json(['message' => 'Record not found'], 404);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'usuario_id' => 'required|exists:usuario,id',
                'menu_id' => 'required|exists:menu,id',
                'empresa_id' => 'required|exists:empresa,id',
                'agencia_id' => 'requires|exists:agencia,id',
                'habilitado' => 'required|boolean',
            ]);
            $validated['habilitado'] = $validated['habilitado'] ?? true;

            $userMenu = MenuUsuario::create($validated);
            return response()->json($userMenu, 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating record',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        $userMenu = MenuUsuario::find($id);
        if ($userMenu) {
            $validated = $request->validate([
                'usuario_id' => 'sometimes|exists:usuario,id',
                'menu_id' => 'sometimes|exists:menu,id',
                'empresa_id' => 'sometimes|exists:empresa,id',
                'agencia_id' => 'sometimes|exists:agencia,id',
                'habilitado' => 'sometimes|boolean',
            ]);

            $userMenu->update($validated);
            return response()->json($userMenu);
        }
        return response()->json(['message' => 'Record not found'], 404);
    }

    public function destroy($id)
    {
        $userMenu = MenuUsuario::find($id);
        if ($userMenu) {
            $userMenu->delete();
            return response()->json(['message' => 'Record deleted successfully']);
        }
        return response()->json(['message' => 'Record not found'], 404);
    }
}
