<?php

namespace App\Http\Controllers;

use App\Models\RolMenu;
use Illuminate\Http\Request;

class MenuRolController extends Controller
{
    public function index()
    {
        return response()->json(RolMenu::all());
    }

    public function show($id)
    {
        $menuRol = RolMenu::find($id);
        if ($menuRol) {
            return response()->json($menuRol);
        }
        return response()->json(['message' => 'Record not found'], 404);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'empresa_id' => 'required|exists:empresa,id',
                'menu_id' => 'required|exists:menu,id',
                'nombre' => 'required|string|max:255',
                'habilitado' => 'required|boolean',
            ]);
            $validated['habilitado'] = $validated['habilitado'] ?? true;

            $menuRol = RolMenu::create($validated);
            return response()->json($menuRol, 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating record',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        $menuRol = RolMenu::find($id);
        if ($menuRol) {
            $validated = $request->validate([
                'empresa_id' => 'sometimes|exists:empresa,id',
                'menu_id' => 'sometimes|exists:menu,id',
                'nombre' => 'sometimes|string|max:255',
                'habilitado' => 'sometimes|boolean',
            ]);

            $menuRol->update($validated);
            return response()->json($menuRol);
        }
        return response()->json(['message' => 'Record not found'], 404);
    }

    public function destroy($id)
    {
        $menuRol = RolMenu::find($id);
        if ($menuRol) {
            $menuRol->delete();
            return response()->json(['message' => 'Record deleted successfully']);
        }
        return response()->json(['message' => 'Record not found'], 404);
    }
}
