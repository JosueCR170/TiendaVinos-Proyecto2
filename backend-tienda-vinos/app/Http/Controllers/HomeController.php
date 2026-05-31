<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Devuelve los productos destacados y los que tienen descuento
     * para mostrar en la página principal del frontend.
     */
    public function index(Request $request)
    {
        // Productos destacados: los más recientes con stock disponible
        $destacados = Producto::with(['marca', 'categoria'])
            ->where('estado', 1)
            ->where('cantidad', '>', 0)
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get()
            ->map(fn($p) => $this->formatProducto($p));

        // Productos con descuento activo y en stock
        $descuentos = Producto::with(['marca', 'categoria'])
            ->where('estado', 1)
            ->where('cantidad', '>', 0)
            ->where('descuento', '>', 0)
            ->orderBy('descuento', 'desc')
            ->limit(6)
            ->get()
            ->map(fn($p) => $this->formatProducto($p));

        return response()->json([
            'data' => [
                'destacados' => $destacados,
                'descuentos' => $descuentos,
            ]
        ]);
    }

    private function formatProducto(Producto $p): array
    {
        return [
            'id_producto'   => $p->id_producto,
            'nombre'        => $p->nombre,
            'descripcion'   => $p->descripcion,
            'imagen_url'    => $p->imagen_url,
            'precio'        => $p->precio,
            'descuento'     => $p->descuento ?? 0,
            'cantidad'      => $p->cantidad,
            'pais'          => $p->pais,
            'region'        => $p->region,
            'anio_cosecha'  => $p->anio_cosecha,
            'marca'         => $p->marca ? ['id_marca' => $p->marca->id_marca, 'nombre' => $p->marca->nombre] : null,
            'categoria'     => $p->categoria ? ['id_categoria' => $p->categoria->id_categoria, 'nombre' => $p->categoria->nombre] : null,
        ];
    }
}
