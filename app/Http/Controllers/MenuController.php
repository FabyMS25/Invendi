<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;

class MenuController extends Controller
{
    public function index()
    {
        return response()->json(Menu::all());
    }

    public function show($id)
    {
        $menu = Menu::find($id);
        if ($menu) {
            return response()->json($menu);
        }
        return response()->json(['message' => 'Record not found'], 404);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'url' => 'required|url',
                'aplicacion' => 'required|string|max:255',
                'modulo' => 'required|string|max:255',
                'orden_modulo' => 'required|integer',
                'agrupador' => 'required|string|max:255',
                'habilitado' => 'nullable|boolean',
            ]);
            $validated['habilitado'] = $validated['habilitado'] ?? true;

            $menu = Menu::create($validated);
            return response()->json($menu, 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating record',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        $menu = Menu::find($id);
        if ($menu) {
            $validated = $request->validate([
                'nombre' => 'sometimes|string|max:255',
            ]);

            if (isset($validated['password'])) {
                $validated['password'] = bcrypt($validated['password']);
            }

            $menu->update($validated);
            return response()->json($menu);
        }
        return response()->json(['message' => 'Record not found'], 404);
    }

    public function destroy($id)
    {
        $menu = Menu::find($id);
        if ($menu) {
            $menu->delete();
            return response()->json(['message' => 'Record deleted successfully']);
        }
        return response()->json(['message' => 'Record not found'], 404);
    }
}
