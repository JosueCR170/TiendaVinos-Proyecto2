<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class CategoriaController extends Controller
{
    public function index(Request $request)
    {
        $query = Categoria::with('padre');

        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = '%' . $request->search . '%';
            $query->where('nombre', 'like', $searchTerm)
                  ->orWhere('descripcion', 'like', $searchTerm);
        }

        $query->orderByRaw('CASE WHEN nombre_padre IS NULL THEN id_categoria ELSE nombre_padre END ASC')
              ->orderBy('nivel', 'asc');

        $categorias = $query->get();
        return response()->json([
            'success' => true,
            'data' => $categorias
        ]);
    }


    public function store(Request $request)
    {
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

        try {
            $categoria = Categoria::create($validated);
            return response()->json([
                'success' => true,
                'message' => 'Categoría creada con éxito.',
                'data' => $categoria
            ], 201);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la categoría en la base de datos. Verifique que los datos sean correctos.'
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado al crear la categoría.'
            ], 500);
        }
    }


    public function update(Request $request, Categoria $categoria)
    {
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

        try {
            $categoria->update($validated);
            return response()->json([
                'success' => true,
                'message' => 'Categoría actualizada con éxito.',
                'data' => $categoria
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la categoría en la base de datos.'
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado al actualizar la categoría.'
            ], 500);
        }
    }

    public function destroy(Categoria $categoria)
    {
        try {
            // Verificar si tiene subcategorías asociadas
            $subcategorias = Categoria::where('nombre_padre', $categoria->id_categoria)->count();
            if ($subcategorias > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar esta categoría porque tiene ' . $subcategorias . ' subcategoría(s) asociada(s). Elimine primero las subcategorías.'
                ], 409);
            }

            // Verificar si tiene productos asociados
            $productos = $categoria->productos()->count();
            if ($productos > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar esta categoría porque tiene ' . $productos . ' producto(s) asociado(s). Reasigne los productos primero.'
                ], 409);
            }

            $categoria->delete();
            return response()->json([
                'success' => true,
                'message' => 'Categoría eliminada con éxito.'
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar esta categoría porque tiene registros dependientes.'
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado al eliminar la categoría.'
            ], 500);
        }
    }
}
