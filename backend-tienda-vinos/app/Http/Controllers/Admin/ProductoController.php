<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Variedad;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::with(['categoria.padre', 'marca']);

        // Búsqueda por nombre o descripción
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('nombre', 'like', $searchTerm)
                  ->orWhere('descripcion', 'like', $searchTerm);
            });
        }

        // Filtro por categoría
        if ($request->has('categoria') && !empty($request->categoria)) {
            $query->where('id_categoria', $request->categoria);
        }

        // Filtro por marca
        if ($request->has('marca') && !empty($request->marca)) {
            $query->where('id_marca', $request->marca);
        }

        // Filtro por país
        if ($request->has('pais') && !empty($request->pais)) {
            $query->where('pais', $request->pais);
        }

        // Ordenamiento
        $sort = $request->get('sort', 'id_producto');
        $direction = $request->get('direction', 'desc');

        if ($sort === 'categoria') {
            $query->join('categorias', 'productos.id_categoria', '=', 'categorias.id_categoria')
                  ->orderBy('categorias.nombre', $direction)
                  ->select('productos.*');
        } elseif ($sort === 'marca') {
            $query->join('marcas', 'productos.id_marca', '=', 'marcas.id_marca')
                  ->orderBy('marcas.nombre', $direction)
                  ->select('productos.*');
        } else {
            // Validar que la columna existe para evitar errores SQL
            $allowedSorts = ['nombre', 'cantidad', 'precio', 'estado', 'id_producto', 'created_at'];
            if (in_array($sort, $allowedSorts)) {
                $query->orderBy($sort, $direction);
            } else {
                $query->orderBy('id_producto', 'desc');
            }
        }

        $productos = $query->paginate(10)->withQueryString();

        return response()->json([
            'success'      => true,
            'data'         => $productos->items(),
            'current_page' => $productos->currentPage(),
            'last_page'    => $productos->lastPage(),
            'total'        => $productos->total(),
            'from'         => $productos->firstItem() ?? 0,
            'to'           => $productos->lastItem()  ?? 0,
        ]);
    }

    /**
     * Devuelve datos auxiliares para los formularios de productos
     * (categorías, marcas, variedades, países).
     */
    public function formData()
    {
        $categorias = Categoria::orderBy('nombre')->get(['id_categoria', 'nombre', 'nivel', 'nombre_padre']);
        $marcas     = Marca::orderBy('nombre')->get(['id_marca', 'nombre']);
        $variedades = Variedad::orderBy('nombre')->get(['id_variedad', 'nombre']);
        $paises     = Producto::whereNotNull('pais')
            ->where('pais', '!=', '')
            ->distinct()
            ->orderBy('pais')
            ->pluck('pais')
            ->values();

        return response()->json([
            'categorias' => $categorias,
            'marcas'     => $marcas,
            'variedades' => $variedades,
            'paises'     => $paises,
        ]);
    }

    /**
     * Devuelve un producto específico con sus relaciones para edición.
     */
    public function show(Producto $producto)
    {
        $producto->load(['categoria', 'marca', 'variedades']);
        return response()->json(['success' => true, 'data' => $producto]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|max:200',
            'id_categoria' => 'required|exists:categorias,id_categoria',
            'id_marca' => 'required|exists:marcas,id_marca',
            'precio' => 'required|numeric|min:0',
            'cantidad' => 'nullable|integer|min:0',
            'descuento' => 'nullable|numeric|min:0|max:100',
            'alcohol_porcentaje' => 'nullable|numeric|min:0|max:100',
            'contenido_ml' => 'nullable|integer|min:0',
            'anio_cosecha' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'imagen_url' => 'nullable|url|max:500',
        ], [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 200 caracteres.',
            'id_categoria.required' => 'Debe seleccionar una categoría.',
            'id_categoria.exists' => 'La categoría seleccionada no existe.',
            'id_marca.required' => 'Debe seleccionar una marca.',
            'id_marca.exists' => 'La marca seleccionada no existe.',
            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric' => 'El precio debe ser un número válido.',
            'precio.min' => 'El precio no puede ser negativo.',
            'cantidad.integer' => 'La cantidad debe ser un número entero.',
            'cantidad.min' => 'La cantidad no puede ser negativa.',
            'descuento.min' => 'El descuento no puede ser negativo.',
            'descuento.max' => 'El descuento no puede superar el 100%.',
            'alcohol_porcentaje.min' => 'El porcentaje de alcohol no puede ser negativo.',
            'alcohol_porcentaje.max' => 'El porcentaje de alcohol no puede superar 100%.',
            'anio_cosecha.min' => 'El año de cosecha no es válido.',
            'anio_cosecha.max' => 'El año de cosecha no puede ser futuro.',
            'imagen_url.url' => 'La URL de imagen debe ser una dirección web válida.',
        ]);

        try {
            $validated['estado'] = $request->has('estado') ? 1 : 0;
            $producto = Producto::create($validated);
            return response()->json([
                'success' => true,
                'message' => 'Producto creado con éxito.',
                'data' => $producto
            ], 201);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar el producto en la base de datos. Verifique que los datos sean correctos.'
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado al crear el producto. Intente nuevamente.'
            ], 500);
        }
    }

    public function update(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'nombre' => 'required|max:200',
            'id_categoria' => 'required|exists:categorias,id_categoria',
            'id_marca' => 'required|exists:marcas,id_marca',
            'precio' => 'required|numeric|min:0',
            'cantidad' => 'nullable|integer|min:0',
            'descuento' => 'nullable|numeric|min:0|max:100',
            'alcohol_porcentaje' => 'nullable|numeric|min:0|max:100',
            'contenido_ml' => 'nullable|integer|min:0',
            'anio_cosecha' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'imagen_url' => 'nullable|url|max:500',
        ], [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 200 caracteres.',
            'id_categoria.required' => 'Debe seleccionar una categoría.',
            'id_categoria.exists' => 'La categoría seleccionada no existe.',
            'id_marca.required' => 'Debe seleccionar una marca.',
            'id_marca.exists' => 'La marca seleccionada no existe.',
            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric' => 'El precio debe ser un número válido.',
            'precio.min' => 'El precio no puede ser negativo.',
            'cantidad.integer' => 'La cantidad debe ser un número entero.',
            'cantidad.min' => 'La cantidad no puede ser negativa.',
            'descuento.min' => 'El descuento no puede ser negativo.',
            'descuento.max' => 'El descuento no puede superar el 100%.',
            'alcohol_porcentaje.min' => 'El porcentaje de alcohol no puede ser negativo.',
            'alcohol_porcentaje.max' => 'El porcentaje de alcohol no puede superar 100%.',
            'anio_cosecha.min' => 'El año de cosecha no es válido.',
            'anio_cosecha.max' => 'El año de cosecha no puede ser futuro.',
            'imagen_url.url' => 'La URL de imagen debe ser una dirección web válida.',
        ]);

        try {
            $validated['estado'] = $request->has('estado') ? 1 : 0;
            $producto->update($validated);
            return response()->json([
                'success' => true,
                'message' => 'Producto actualizado con éxito.',
                'data' => $producto
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el producto en la base de datos.'
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado al actualizar el producto.'
            ], 500);
        }
    }

    public function destroy(Producto $producto)
    {
        try {
            $producto->delete();
            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado con éxito.'
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el producto porque tiene registros asociados (ej. carritos de compra).'
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado al eliminar el producto.'
            ], 500);
        }
    }
}
