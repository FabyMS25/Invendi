<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use function App\Providers\apiResponse;
use App\Models\Empresa;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        try {
            $validated = $request->validate([
                'habilitado' => 'nullable|boolean',
                'search' => 'nullable|string',
                'per_page' => 'nullable|integer|min:1',
            ]);
            $query = Empresa::query();
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
            Log::error('Error fetching companies: ' . $e->getMessage());
            return apiResponse([
                'message' => 'Error retrieving companies',
                'error' => $e->getMessage(),
            ],500);
        }
    }

    public function show($id)
    {
        try {
            $data = Empresa::with('agencias')->find($id);
            if (!$data) {
                return response()->json([
                    'message' => 'Company not found'
                ], 404);
            }
            return response()->json([
                'message' => 'Company retrieved successfully',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching Company: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error fetching Company',
                'error' => $e->getMessage()
            ], 500);
        }
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
        } catch (ValidationException $e) {
        return response([
            'message' => 'Validation error',
            'error' => $e->errors()
        ],422);
        } catch (\Exception $e) {
            return response([
                'message' => 'Error creating Company',
                'error' => $e->getMessage()
            ],500);
        }
    }

    public function update(Request $request, string $id)
    {
        $data = Empresa::find($id);
        if (!$data) {
            return response()->json([
                'message' => 'Record not found'
            ], 404);
        }
        try {
            $validated = $request->validate([
                'nombre' => 'sometimes|string|max:255',
                'titular' => 'sometimes|string|max:255',
                'actividad' => 'sometimes|string|max:255',
                'habilitado' => 'nullable|boolean',
            ]);

            $data->update($validated);
            return response()->json($data,200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating record: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error updating record',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $company = Empresa::find($id);
            if (!$company) {
                return response()->json(['message' => 'Record not found'], 404);
            }
            $company->delete();
            return response()->json(['message' => 'Record deleted successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting Record',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
