<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Variedad;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Devuelve estadísticas generales para el dashboard admin.
     */
    public function index()
    {
        return response()->json([
            'data' => [
                'total_productos'  => Producto::count(),
                'productos_activos' => Producto::where('estado', 1)->count(),
                'productos_sin_stock' => Producto::where('cantidad', 0)->count(),
                'total_categorias' => Categoria::count(),
                'total_marcas'     => Marca::count(),
                'total_variedades' => Variedad::count(),
                'con_descuento'    => Producto::where('descuento', '>', 0)->count(),
            ]
        ]);
    }
}
