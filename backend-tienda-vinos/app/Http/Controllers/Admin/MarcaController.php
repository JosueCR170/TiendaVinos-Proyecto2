<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Monarobase\CountryList\CountryList;

class MarcaController extends Controller
{
    public function index(Request $request)
    {
        $query = Marca::query();

        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = '%' . $request->search . '%';
            $query->where('nombre', 'like', $searchTerm)
                  ->orWhere('descripcion', 'like', $searchTerm);
        }

        if ($request->has('pais') && !empty($request->pais)) {
            $query->where('pais', $request->pais);
        }

        $sort = $request->get('sort', 'id_marca');
        $direction = $request->get('direction', 'desc');
        $allowedSorts = ['nombre', 'pais', 'id_marca'];
        
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('id_marca', 'desc');
        }

        $marcas = $query->paginate(10)->withQueryString();

        return response()->json([
            'success' => true,
            'data' => $marcas->items(),
            'pagination' => [
                'current_page' => $marcas->currentPage(),
                'per_page' => $marcas->perPage(),
                'total' => $marcas->total(),
                'last_page' => $marcas->lastPage()
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|max:100|unique:marcas,nombre',
            'pais' => 'required|max:100',
            'descripcion' => 'nullable|max:500',
        ], [
            'nombre.required' => 'El nombre de la marca es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 100 caracteres.',
            'nombre.unique' => 'Ya existe una marca con ese nombre.',
            'pais.required' => 'El país de origen es obligatorio.',
            'descripcion.max' => 'La descripción no puede exceder 500 caracteres.',
        ]);

        try {
            $marca = Marca::create($validated);
            return response()->json([
                'success' => true,
                'message' => 'Marca creada con éxito.',
                'data' => $marca
            ], 201);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la marca en la base de datos.'
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado al crear la marca.'
            ], 500);
        }
    }

    public function update(Request $request, Marca $marca)
    {
        $validated = $request->validate([
            'nombre' => 'required|max:100|unique:marcas,nombre,' . $marca->id_marca . ',id_marca',
            'pais' => 'required|max:100',
            'descripcion' => 'nullable|max:500',
        ], [
            'nombre.required' => 'El nombre de la marca es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 100 caracteres.',
            'nombre.unique' => 'Ya existe otra marca con ese nombre.',
            'pais.required' => 'El país de origen es obligatorio.',
            'descripcion.max' => 'La descripción no puede exceder 500 caracteres.',
        ]);

        try {
            $marca->update($validated);
            return response()->json([
                'success' => true,
                'message' => 'Marca actualizada con éxito.',
                'data' => $marca
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la marca.'
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado al actualizar la marca.'
            ], 500);
        }
    }

    public function destroy(Marca $marca)
    {
        try {
            $productos = $marca->productos()->count();
            if ($productos > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar "' . $marca->nombre . '" porque tiene ' . $productos . ' producto(s) asociado(s).'
                ], 409);
            }

            $marca->delete();
            return response()->json([
                'success' => true,
                'message' => 'Marca eliminada con éxito.'
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar esta marca porque tiene registros dependientes.'
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado al eliminar la marca.'
            ], 500);
        }
    }
}
