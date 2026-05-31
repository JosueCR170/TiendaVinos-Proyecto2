<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Variedad;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use App\Traits\ApiQueryTrait;

class VariedadController extends Controller
{
    use ApiQueryTrait;

    public function index(Request $request)
    {
        try {

            $query = Variedad::withCount('productos');

            $this->applySearch(
                $query,
                $request,
                ['nombre', 'tipo', 'descripcion']
            );

            $this->applySorting(
                $query,
                $request,
                [
                    'id_variedad',
                    'nombre',
                    'tipo',
                    'productos_count'
                ],
                'id_variedad'
            );

            $variedades = $query->paginate(
                $this->getPerPage($request)
            );

            return response()->json([
                'success' => true,
                'message' => $variedades->total() > 0
                    ? 'Variedades obtenidas correctamente.'
                    : 'No se encontraron variedades.',
                'data' => $variedades
            ], 200);

        } catch (QueryException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al consultar las variedades.',
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

            $variedad = Variedad::withCount('productos')
                ->find($id);

            if (!$variedad) {

                return response()->json([
                    'success' => false,
                    'message' => 'Variedad no encontrada.',
                    'data' => null
                ], 404);

            }

            return response()->json([
                'success' => true,
                'message' => 'Variedad obtenida correctamente.',
                'data' => $variedad
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al obtener la variedad.',
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
                'nombre' => 'required|max:100|unique:variedades,nombre',
                'tipo' => 'required|in:Tinta,Blanca,Aromatica',
                'descripcion' => 'nullable|max:500',
            ], [
                'nombre.required' => 'El nombre de la variedad es obligatorio.',
                'nombre.max' => 'El nombre no puede exceder 100 caracteres.',
                'nombre.unique' => 'Ya existe una variedad con ese nombre.',
                'tipo.required' => 'El tipo de variedad es obligatorio.',
                'tipo.in' => 'El tipo debe ser Tinta, Blanca o Aromatica.',
                'descripcion.max' => 'La descripción no puede exceder 500 caracteres.',
            ]);

            $variedad = Variedad::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Variedad creada con éxito.',
                'data' => $variedad
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
                'message' => 'Error al guardar la variedad en la base de datos.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null
            ], 500);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado al crear la variedad.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null
            ], 500);

        }
    }

    public function update(Request $request, Variedad $variedad)
    {
        try {

            $validated = $request->validate([
                'nombre' => 'required|max:100|unique:variedades,nombre,' .
                    $variedad->id_variedad . ',id_variedad',
                'tipo' => 'required|in:Tinta,Blanca,Aromatica',
                'descripcion' => 'nullable|max:500',
            ], [
                'nombre.required' => 'El nombre de la variedad es obligatorio.',
                'nombre.max' => 'El nombre no puede exceder 100 caracteres.',
                'nombre.unique' => 'Ya existe otra variedad con ese nombre.',
                'tipo.required' => 'El tipo de variedad es obligatorio.',
                'tipo.in' => 'El tipo debe ser Tinta, Blanca o Aromatica.',
                'descripcion.max' => 'La descripción no puede exceder 500 caracteres.',
            ]);

            $variedad->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Variedad actualizada con éxito.',
                'data' => $variedad
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
                'message' => 'Error al actualizar la variedad en la base de datos.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null
            ], 500);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado al actualizar la variedad.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null
            ], 500);

        }
    }

    public function destroy(Variedad $variedad)
    {
        try {

            $productos = $variedad->productos()->count();

            if ($productos > 0) {

                return response()->json([
                    'success' => false,
                    'message' => "No se puede eliminar esta variedad porque tiene {$productos} producto(s) asociado(s)."
                ], 409);

            }

            $variedad->delete();

            return response()->json([
                'success' => true,
                'message' => 'Variedad eliminada con éxito.'
            ], 200);

        } catch (QueryException $e) {

            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar la variedad porque existen registros relacionados.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null
            ], 500);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado al eliminar la variedad.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null
            ], 500);

        }
    }
}
