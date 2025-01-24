<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agencia;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

use function App\Providers\apiResponse;

class AgencyController extends Controller
{
    public function index(Request $request)
    {
        try {
            $validated = $request->validate([
                'habilitado' => 'nullable|boolean',
                'search' => 'nullable|string',
                'per_page' => 'nullable|integer|min:1',
            ]);
            $query = Agencia::query()->with('empresa');
            if ($request->has('habilitado')) {
                $query->where('habilitado', $validated['habilitado']);
            }
            if (!empty($validated['search'])) {
                $search = $validated['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('nombre', 'LIKE', "%{$search}%")
                        ->orWhere('titular', 'LIKE', "%{$search}%")
                        ->orWhere('actividad', 'LIKE', "%{$search}%");
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
            Log::error('Error fetching agencies: ' . $e->getMessage());
            return apiResponse([
                'message' => 'Error retrieving agencies',
                'error' => $e->getMessage(),
            ],500);
        }
    }

    public function store(Request $request)
    {
        try{
            $validated = $request->validate([
                'empresa_id' => 'required|exists:empresa,id',
                'nombre' => 'required|string|max:255',
                'direccion' => 'nullable|string|max:255',
                'telefonos' => 'nullable|string|max:255',
                'geolocalizacion' => 'nullable|string|max:255',
                'correo_agencia' => 'nullable|email|max:255',
                'habilitado' => 'required|boolean',
            ]);

            $data = Agencia::create($validated);
            return response($data,201);
        } catch (ValidationException $e) {
        return response([
            'message' => 'Validation error',
            'error' => $e->errors()
        ],422);
        } catch (\Exception $e) {
            return response([
                'message' => 'Error creating Agency',
                'error' => $e->getMessage()
            ],500);
        }
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
