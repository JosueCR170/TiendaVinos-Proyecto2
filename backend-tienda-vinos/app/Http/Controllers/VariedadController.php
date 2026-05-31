<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Variedad;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class VariedadController extends Controller
{
    public function index(Request $request)
    {
        $query = Variedad::withCount('productos');

        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = '%' . $request->search . '%';
            $query->where('nombre', 'like', $searchTerm)
                  ->orWhere('tipo', 'like', $searchTerm);
        }

        $sort = $request->get('sort', 'id_variedad');
        $direction = $request->get('direction', 'desc');
        $allowedSorts = ['nombre', 'tipo', 'productos_count', 'id_variedad'];
        
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('id_variedad', 'desc');
        }

        $variedades = $query->paginate(10)->withQueryString();
        return response()->json([
            'success' => true,
            'data' => $variedades->items(),
            'pagination' => [
                'current_page' => $variedades->currentPage(),
                'per_page' => $variedades->perPage(),
                'total' => $variedades->total(),
                'last_page' => $variedades->lastPage()
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|max:100|unique:variedades,nombre',
            'tipo' => 'required|in:Tinta,Blanca,Aromatica',
            'descripcion' => 'nullable|max:500',
        ], [
            'nombre.required' => 'El nombre de la variedad es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 100 caracteres.',
            'nombre.unique' => 'Ya existe una variedad con ese nombre.',
            'tipo.required' => 'El tipo de variedad es obligatorio.',
            'tipo.in' => 'El tipo debe ser Tinta, Blanca o Aromática.',
            'descripcion.max' => 'La descripción no puede exceder 500 caracteres.',
        ]);

        try {
            $variedad = Variedad::create($validated);
            return response()->json([
                'success' => true,
                'message' => 'Variedad creada con éxito.',
                'data' => $variedad
            ], 201);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la variedad en la base de datos.'
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado al crear la variedad.'
            ], 500);
        }
    }

    public function update(Request $request, Variedad $variedade)
    {
        $variedad = $variedade;
        $validated = $request->validate([
            'nombre' => 'required|max:100|unique:variedades,nombre,' . $variedad->id_variedad . ',id_variedad',
            'tipo' => 'required|in:Tinta,Blanca,Aromatica',
            'descripcion' => 'nullable|max:500',
        ], [
            'nombre.required' => 'El nombre de la variedad es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 100 caracteres.',
            'nombre.unique' => 'Ya existe otra variedad con ese nombre.',
            'tipo.required' => 'El tipo de variedad es obligatorio.',
            'tipo.in' => 'El tipo debe ser Tinta, Blanca o Aromática.',
            'descripcion.max' => 'La descripción no puede exceder 500 caracteres.',
        ]);

        try {
            $variedad->update($validated);
            return response()->json([
                'success' => true,
                'message' => 'Variedad actualizada con éxito.',
                'data' => $variedad
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la variedad.'
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado al actualizar la variedad.'
            ], 500);
        }
    }

    public function destroy(Variedad $variedade)
    {
        try {
            $productos = $variedade->productos()->count();
            if ($productos > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar "' . $variedade->nombre . '" porque tiene ' . $productos . ' producto(s) asociado(s).'
                ], 409);
            }

            $variedade->delete();
            return response()->json([
                'success' => true,
                'message' => 'Variedad eliminada con éxito.'
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar esta variedad porque tiene registros dependientes.'
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado al eliminar la variedad.'
            ], 500);
        }
    }
}
