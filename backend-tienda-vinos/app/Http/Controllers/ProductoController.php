<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Variedad;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use App\Traits\ApiQueryTrait;

class ProductoController extends Controller
{
    use ApiQueryTrait;

    public function index(Request $request)
    {
        try {

            $this->normalizeIndexRequest($request);

            $query = Producto::with(['categoria.padre', 'marca', 'variedades']);

            $this->applySearch(
                $query,
                $request,
                ['nombre', 'descripcion', 'pais', 'region']
            );

            $this->applyFilters(
                $query,
                $request,
                ['id_categoria', 'id_marca', 'pais', 'estado']
            );

            $sortBy = $request->input('sort_by', 'id_producto');

            if ($sortBy === 'categoria') {

                $direction = $this->getSortDirection($request);

                $query->join(
                    'categorias',
                    'productos.id_categoria',
                    '=',
                    'categorias.id_categoria'
                )
                    ->orderBy('categorias.nombre', $direction)
                    ->select('productos.*');

            } elseif ($sortBy === 'marca') {

                $direction = $this->getSortDirection($request);

                $query->join(
                    'marcas',
                    'productos.id_marca',
                    '=',
                    'marcas.id_marca'
                )
                    ->orderBy('marcas.nombre', $direction)
                    ->select('productos.*');

            } else {

                $this->applySorting(
                    $query,
                    $request,
                    [
                        'id_producto',
                        'nombre',
                        'cantidad',
                        'precio',
                        'estado',
                        'pais',
                        'created_at'
                    ],
                    'id_producto'
                );

            }

            $productos = $query->paginate(
                $this->getPerPage($request)
            );

            return response()->json([
                'success' => true,
                'message' => $productos->total() > 0
                    ? 'Productos obtenidos correctamente.'
                    : 'No se encontraron productos.',
                'data' => $productos
            ], 200);

        } catch (QueryException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al consultar los productos.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null
            ], 500);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Ocurrio un error inesperado.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null
            ], 500);

        }
    }

    /**
     * Devuelve datos auxiliares para los formularios de productos
     * (categorias, marcas, variedades, paises).
     */
    public function formData()
    {
        try {

            $categorias = Categoria::orderBy('nombre')
                ->get(['id_categoria', 'nombre', 'nivel', 'nombre_padre']);

            $marcas = Marca::orderBy('nombre')
                ->get(['id_marca', 'nombre']);

            $variedades = Variedad::orderBy('nombre')
                ->get(['id_variedad', 'nombre']);

            $paises = Producto::whereNotNull('pais')
                ->where('pais', '!=', '')
                ->distinct()
                ->orderBy('pais')
                ->pluck('pais')
                ->values();

            return response()->json([
                'success' => true,
                'message' => 'Datos del formulario obtenidos correctamente.',
                'data' => [
                    'categorias' => $categorias,
                    'marcas'     => $marcas,
                    'variedades' => $variedades,
                    'paises'     => $paises,
                ]
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Ocurrio un error al obtener los datos del formulario.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null
            ], 500);

        }
    }

    /**
     * Devuelve un producto especifico con sus relaciones para edicion.
     */
    public function show($id)
    {
        try {

            $producto = Producto::with(['categoria.padre', 'marca', 'variedades'])
                ->find($id);

            if (!$producto) {

                return response()->json([
                    'success' => false,
                    'message' => 'Producto no encontrado.',
                    'data' => null
                ], 404);

            }

            return response()->json([
                'success' => true,
                'message' => 'Producto obtenido correctamente.',
                'data' => $producto
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Ocurrio un error al obtener el producto.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null
            ], 500);

        }
    }

    public function store(Request $request)
    {
        try {

            $validated = $this->validateProducto($request);
            $validated['estado'] = $request->has('estado') ? 1 : 0;

            $producto = Producto::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Producto creado con exito.',
                'data' => $producto
            ], 201);

        } catch (ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error de validacion.',
                'errors' => $e->errors()
            ], 422);

        } catch (QueryException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al guardar el producto en la base de datos.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null
            ], 500);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Ocurrio un error inesperado al crear el producto.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null
            ], 500);

        }
    }

    public function update(Request $request, Producto $producto)
    {
        try {

            $validated = $this->validateProducto($request);
            $validated['estado'] = $request->has('estado') ? 1 : 0;

            $producto->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Producto actualizado con exito.',
                'data' => $producto
            ], 200);

        } catch (ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error de validacion.',
                'errors' => $e->errors()
            ], 422);

        } catch (QueryException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el producto en la base de datos.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null
            ], 500);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Ocurrio un error inesperado al actualizar el producto.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null
            ], 500);

        }
    }

    public function destroy(Producto $producto)
    {
        try {

            $producto->delete();

            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado con exito.'
            ], 200);

        } catch (QueryException $e) {

            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el producto porque existen registros relacionados.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null
            ], 500);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Ocurrio un error inesperado al eliminar el producto.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null
            ], 500);

        }
    }

    private function normalizeIndexRequest(Request $request): void
    {
        if ($request->filled('buscar') && !$request->filled('search')) {
            $request->merge(['search' => $request->input('buscar')]);
        }

        if ($request->filled('categoria') && !$request->filled('id_categoria')) {
            $request->merge(['id_categoria' => $request->input('categoria')]);
        }

        if ($request->filled('marca') && !$request->filled('id_marca')) {
            $request->merge(['id_marca' => $request->input('marca')]);
        }

        if ($request->filled('sort') && !$request->filled('sort_by')) {
            $request->merge(['sort_by' => $request->input('sort')]);
        }
    }

    private function getSortDirection(Request $request): string
    {
        $direction = strtolower($request->input('direction', 'asc'));

        return in_array($direction, ['asc', 'desc'])
            ? $direction
            : 'asc';
    }

    private function validateProducto(Request $request): array
    {
        return $request->validate([
            'nombre' => 'required|max:200',
            'id_categoria' => 'required|exists:categorias,id_categoria',
            'id_marca' => 'required|exists:marcas,id_marca',
            'precio' => 'required|numeric|min:0',
            'cantidad' => 'nullable|integer|min:0',
            'descuento' => 'nullable|numeric|min:0|max:100',
            'alcohol_porcentaje' => 'nullable|numeric|min:0|max:100',
            'contenido_ml' => 'nullable|integer|min:0',
            'anio_cosecha' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'imagen_url' => 'nullable|max:500',
        ], [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 200 caracteres.',
            'id_categoria.required' => 'Debe seleccionar una categoria.',
            'id_categoria.exists' => 'La categoria seleccionada no existe.',
            'id_marca.required' => 'Debe seleccionar una marca.',
            'id_marca.exists' => 'La marca seleccionada no existe.',
            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric' => 'El precio debe ser un numero valido.',
            'precio.min' => 'El precio no puede ser negativo.',
            'cantidad.integer' => 'La cantidad debe ser un numero entero.',
            'cantidad.min' => 'La cantidad no puede ser negativa.',
            'descuento.min' => 'El descuento no puede ser negativo.',
            'descuento.max' => 'El descuento no puede superar el 100%.',
            'alcohol_porcentaje.min' => 'El porcentaje de alcohol no puede ser negativo.',
            'alcohol_porcentaje.max' => 'El porcentaje de alcohol no puede superar 100%.',
            'anio_cosecha.min' => 'El anio de cosecha no es valido.',
            'anio_cosecha.max' => 'El anio de cosecha no puede ser futuro.',
            'imagen_url.url' => 'La URL de imagen debe ser una direccion web valida.',
        ]);
    }
}
