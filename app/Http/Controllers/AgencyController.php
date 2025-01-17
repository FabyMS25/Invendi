<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agencia;

class AgencyController extends Controller
{
    public function index()
    {
        $agencias = Agencia::with('empresa')->get();
        return response()->json($agencias, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'empresa_id' => 'required|exists:empresa,id',
            'nombre' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefonos' => 'nullable|string|max:255',
            'geolocalizacion' => 'nullable|string|max:255',
            'correo_agencia' => 'nullable|email|max:255',
            'habilitado' => 'required|boolean',
        ]);

        $agencia = Agencia::create($validated);

        return response()->json([
            'message' => 'Agency created successfully',
            'data' => $agencia,
        ], 201);
    }

    public function show($id)
    {
        $agencia = Agencia::with('empresa')->find($id);

        if (!$agencia) {
            return response()->json(['message' => 'Agency not found'], 404);
        }

        return response()->json($agencia, 200);
    }

    public function update(Request $request, $id)
    {
        $agencia = Agencia::find($id);

        if (!$agencia) {
            return response()->json(['message' => 'Agency not found'], 404);
        }

        $validated = $request->validate([
            'empresa_id' => 'sometimes|exists:empresa,id',
            'nombre' => 'sometimes|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefonos' => 'nullable|string|max:255',
            'geolocalizacion' => 'nullable|string|max:255',
            'correo_agencia' => 'nullable|email|max:255',
            'habilitado' => 'sometimes|boolean',
        ]);

        $agencia->update($validated);

        return response()->json([
            'message' => 'Agency updated successfully',
            'data' => $agencia,
        ], 200);
    }

    public function destroy($id)
    {
        $agencia = Agencia::find($id);

        if (!$agencia) {
            return response()->json(['message' => 'Agency not found'], 404);
        }

        $agencia->delete();

        return response()->json(['message' => 'Agency deleted successfully'], 200);
    }
}
