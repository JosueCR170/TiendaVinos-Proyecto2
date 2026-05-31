<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Variedad;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CatalogoController extends Controller
{
    public function meta(Request $request)
    {
        $categorias = Categoria::query()
            ->orderBy('nombre')
            ->get(['id_categoria', 'nombre']);

        $marcas = Marca::query()
            ->orderBy('nombre')
            ->get(['id_marca', 'nombre']);

        // Si tu tabla Variedad tiene productos_count etc., igual aquí solo necesitamos id/nombre
        $variedades = Variedad::query()
            ->orderBy('nombre')
            ->get(['id_variedad', 'nombre']);

        $paises = Producto::query()
            ->whereNotNull('pais')
            ->where('pais', '!=', '')
            ->distinct()
            ->orderBy('pais')
            ->pluck('pais')
            ->values();

        return response()->json([
            'categorias' => $categorias,
            'marcas' => $marcas,
            'variedades' => $variedades,
            'paises' => $paises,
        ]);
    }

    public function index(Request $request)
    {
        $buscar = $request->query('buscar');
        $categoria = $request->query('categoria');
        $pais = $request->query('pais');
        $marca = $request->query('marca');
        $variedad = $request->query('variedad');
        $solo_descuentos = $request->query('solo_descuentos');
        $orden = $request->query('orden', 'newest');
        $page = (int) $request->query('page', 1);

        $perPage = 12; // coincide con la UI (md:grid-cols-2 / xl:grid-cols-3)

        $query = Producto::query()
            ->with(['marca', 'variedades', 'categoria'])
            ->select([
                'productos.*',
            ]);

        if (!empty($buscar)) {
            $searchTerm = '%' . $buscar . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('productos.nombre', 'like', $searchTerm)
                    ->orWhere('productos.descripcion', 'like', $searchTerm);
            });
        }

        if (!empty($categoria)) {
            $query->where('productos.id_categoria', $categoria);
        }

        if (!empty($pais)) {
            $query->where('productos.pais', $pais);
        }

        if (!empty($marca)) {
            $query->where('productos.id_marca', $marca);
        }

        if (!empty($variedad)) {
            // producto_variedad: id_producto + id_variedad
            $query->whereHas('variedades', function ($q) use ($variedad) {
                $q->where('variedades.id_variedad', $variedad);
            });
        }

        if (!empty($solo_descuentos) && (string) $solo_descuentos === '1') {
            $query->where('productos.descuento', '>', 0);
        }

        // Ordenamiento (mapea con tu frontend CatalogPage)
        switch ($orden) {
            case 'precio_asc':
                $query->orderBy('productos.precio', 'asc');
                break;
            case 'precio_desc':
                $query->orderBy('productos.precio', 'desc');
                break;
            case 'nombre':
                $query->orderBy('productos.nombre', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('productos.created_at', 'desc');
                break;
        }

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        // Transformar para que coincida con lo que espera CatalogPage.vue
        $productos = $paginator->getCollection()->map(function ($p) {
            return [
                'id_producto' => $p->id_producto,
                'nombre' => $p->nombre,
                'descripcion' => $p->descripcion,
                'imagen_url' => $p->imagen_url,
                'precio' => $p->precio,
                'descuento' => $p->descuento ?? 0,
                'cantidad' => $p->cantidad,
                'pais' => $p->pais,
                'region' => $p->region,
                'anio_cosecha' => $p->anio_cosecha,
                'marca' => $p->marca ? [
                    'id_marca' => $p->marca->id_marca,
                    'nombre' => $p->marca->nombre,
                ] : null,
                'categoria' => $p->categoria ? [
                    'id_categoria' => $p->categoria->id_categoria,
                    'nombre' => $p->categoria->nombre,
                ] : null,
                'variedades' => $p->variedades ? $p->variedades->map(fn ($v) => [
                    'id_variedad' => $v->id_variedad,
                    'nombre' => $v->nombre,
                ])->values() : [],
            ];
        })->values();

        return response()->json([
            'data' => $productos,
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'total' => $paginator->total(),
            'from' => $paginator->firstItem() ?? 0,
            'to' => $paginator->lastItem() ?? 0,
        ]);
    }
}

