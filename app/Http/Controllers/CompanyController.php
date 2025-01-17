<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empresa;

class CompanyController extends Controller
{
    public function index()
    {
        return response()->json(Empresa::all());
    }

    public function show($id)
    {
        $company = Empresa::find($id);
        if ($company) {
            return response()->json($company);
        }
        return response()->json(['message' => 'Record not found'], 404);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'titular' => 'required|string|max:255',
                'actividad' => 'required|string|max:255',
                'habilitado' => 'nullable|boolean',
            ]);
            $validated['habilitado'] = $validated['habilitado'] ?? true;

            $company = Empresa::create($validated);
            return response()->json($company, 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating record',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        $company = Empresa::find($id);
        if ($company) {
            $validated = $request->validate([
                'nombre' => 'sometimes|string|max:255',
                'titular' => 'sometimes|string|max:255',
                'actividad' => 'sometimes|string|max:255',
                'habilitado' => 'nullable|boolean',
            ]);

            $company->update($validated);
            return response()->json($company);
        }
        return response()->json(['message' => 'Record not found'], 404);
    }

    public function destroy($id)
    {
        $company = Empresa::find($id);
        if ($company) {
            $company->delete();
            return response()->json(['message' => 'Record deleted successfully']);
        }
        return response()->json(['message' => 'Record not found'], 404);
    }
}
