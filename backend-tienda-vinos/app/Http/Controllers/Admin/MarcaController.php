<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use App\Traits\ApiQueryTrait;

class MarcaController extends Controller
{
    use ApiQueryTrait;

    public function index(Request $request)
    {
        try {

            $query = Marca::query();

            $this->applySearch(
                $query,
                $request,
                ['nombre', 'descripcion']
            );

            $this->applyFilters(
                $query,
                $request,
                ['pais']
            );

            $this->applySorting(
                $query,
                $request,
                ['id_marca', 'nombre', 'pais'],
                'id_marca'
            );

            $marcas = $query->paginate(
                $this->getPerPage($request)
            );

            return response()->json([
                'success' => true,
                'message' => $marcas->total() > 0
                    ? 'Marcas obtenidas correctamente.'
                    : 'No se encontraron marcas.',
                'data' => $marcas
            ], 200);

        } catch (QueryException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al consultar las marcas.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null
            ], 500);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null
            ], 500);

        }
    }

    public function show($id)
    {
        try {

            $marca = Marca::find($id);

            if (!$marca) {

                return response()->json([
                    'success' => false,
                    'message' => 'Marca no encontrada.',
                    'data' => null
                ], 404);

            }

            return response()->json([
                'success' => true,
                'message' => 'Marca obtenida correctamente.',
                'data' => $marca
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al obtener la marca.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null
            ], 500);

        }
    }

    public function store(Request $request)
    {
        try {

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

            $marca = Marca::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Marca creada con éxito.',
                'data' => $marca
            ], 201);

        } catch (ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors' => $e->errors()
            ], 422);

        } catch (QueryException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la marca en la base de datos.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null
            ], 500);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado al crear la marca.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null
            ], 500);

        }
    }

    public function update(Request $request, Marca $marca)
    {
        try {

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

            $marca->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Marca actualizada con éxito.',
                'data' => $marca
            ], 200);

        } catch (ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors' => $e->errors()
            ], 422);

        } catch (QueryException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la marca.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null
            ], 500);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado al actualizar la marca.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null
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
                    'message' => "No se puede eliminar la marca porque tiene {$productos} producto(s) asociado(s)."
                ], 409);

            }

            $marca->delete();

            return response()->json([
                'success' => true,
                'message' => 'Marca eliminada con éxito.'
            ], 200);

        } catch (QueryException $e) {

            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar la marca porque existen registros relacionados.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null
            ], 500);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado al eliminar la marca.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null
            ], 500);

        }
    }
}