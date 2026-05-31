<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use App\Traits\ApiQueryTrait;

class CategoriaController extends Controller
{
    use ApiQueryTrait;

    public function index(Request $request)
    {
        try {

            $query = Categoria::with('padre');

            $this->applySearch(
                $query,
                $request,
                ['nombre', 'descripcion']
            );

            $this->applyFilters(
                $query,
                $request,
                ['nivel', 'nombre_padre']
            );

            $this->applySorting(
                $query,
                $request,
                ['id_categoria', 'nombre', 'nivel'],
                'id_categoria'
            );

            $categorias = $query->paginate(
                $this->getPerPage($request)
            );

            return response()->json([
                'success' => true,
                'message' => $categorias->total() > 0
                    ? 'Categorías obtenidas correctamente.'
                    : 'No se encontraron categorías.',
                'data' => $categorias
            ], 200);

        } catch (QueryException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al consultar las categorías.',
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

            $categoria = Categoria::with('padre')
                ->find($id);

            if (!$categoria) {
                return response()->json([
                    'success' => false,
                    'message' => 'Categoría no encontrada.',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Categoría obtenida correctamente.',
                'data' => $categoria
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al obtener la categoría.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);

        }
    }

    public function store(Request $request)
    {
        try {

            $validated = $request->validate([
                'nombre' => 'required|max:100|unique:categorias,nombre',
                'nivel' => 'required|integer|in:1,2',
                'descripcion' => 'nullable|max:500',
                'nombre_padre' => 'nullable|exists:categorias,id_categoria',
            ], [
                'nombre.required' => 'El nombre de la categoría es obligatorio.',
                'nombre.max' => 'El nombre no puede exceder 100 caracteres.',
                'nombre.unique' => 'Ya existe una categoría con ese nombre.',
                'nivel.required' => 'El nivel jerárquico es obligatorio.',
                'nivel.in' => 'El nivel debe ser 1 (Principal) o 2 (Subcategoría).',
                'nombre_padre.exists' => 'La categoría padre seleccionada no existe.',
                'descripcion.max' => 'La descripción no puede exceder 500 caracteres.',
            ]);

            $categoria = Categoria::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Categoría creada con éxito.',
                'data' => $categoria
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
                'message' => 'Error al guardar la categoría en la base de datos.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null
            ], 500);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado al crear la categoría.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null
            ], 500);

        }
    }

    public function update(Request $request, Categoria $categoria)
    {
        try {

            $validated = $request->validate([
                'nombre' => 'required|max:100|unique:categorias,nombre,' . $categoria->id_categoria . ',id_categoria',
                'nivel' => 'required|integer|in:1,2',
                'descripcion' => 'nullable|max:500',
                'nombre_padre' => 'nullable|exists:categorias,id_categoria',
            ], [
                'nombre.required' => 'El nombre de la categoría es obligatorio.',
                'nombre.max' => 'El nombre no puede exceder 100 caracteres.',
                'nombre.unique' => 'Ya existe otra categoría con ese nombre.',
                'nivel.required' => 'El nivel jerárquico es obligatorio.',
                'nivel.in' => 'El nivel debe ser 1 (Principal) o 2 (Subcategoría).',
                'nombre_padre.exists' => 'La categoría padre seleccionada no existe.',
                'descripcion.max' => 'La descripción no puede exceder 500 caracteres.',
            ]);

            $categoria->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Categoría actualizada con éxito.',
                'data' => $categoria
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
                'message' => 'Error al actualizar la categoría en la base de datos.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null
            ], 500);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado al actualizar la categoría.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null
            ], 500);

        }
    }

    public function destroy(Categoria $categoria)
    {
        try {

            $subcategorias = Categoria::where(
                'nombre_padre',
                $categoria->id_categoria
            )->count();

            if ($subcategorias > 0) {

                return response()->json([
                    'success' => false,
                    'message' => "No se puede eliminar esta categoría porque tiene {$subcategorias} subcategoría(s) asociada(s)."
                ], 409);

            }

            $productos = $categoria->productos()->count();

            if ($productos > 0) {

                return response()->json([
                    'success' => false,
                    'message' => "No se puede eliminar esta categoría porque tiene {$productos} producto(s) asociado(s)."
                ], 409);

            }

            $categoria->delete();

            return response()->json([
                'success' => true,
                'message' => 'Categoría eliminada con éxito.'
            ], 200);

        } catch (QueryException $e) {

            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar la categoría porque existen registros relacionados.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null
            ], 500);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado al eliminar la categoría.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null
            ], 500);

        }
    }
}