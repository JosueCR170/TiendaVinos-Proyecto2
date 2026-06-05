<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Variedad;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Retorna datos para la página de inicio.
     */
    public function home()
    {
        try {
            // Obtener vinos destacados (primeros 3 activos con stock)
            $destacados = Producto::with(['categoria', 'marca', 'variedades'])
                ->where('estado', 1)
                ->where('cantidad', '>', 0)
                ->latest()
                ->take(3)
                ->get();

            // Si no hay destacados con stock, tomar cualquiera activo
            if ($destacados->isEmpty()) {
                $destacados = Producto::with(['categoria', 'marca', 'variedades'])
                    ->where('estado', 1)
                    ->latest()
                    ->take(3)
                    ->get();
            }

            // Obtener ofertas imperdibles (activos con descuento > 0)
            $descuentos = Producto::with(['categoria', 'marca', 'variedades'])
                ->where('estado', 1)
                ->where('descuento', '>', 0)
                ->latest()
                ->take(3)
                ->get();

            return response()->json([
                'success' => true,
                'destacados' => $destacados,
                'descuentos' => $descuentos
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al cargar los datos de inicio.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Retorna las estadísticas del panel de administración.
     */
    public function stats()
    {
        try {
            $total_productos = Producto::count();
            $productos_activos = Producto::where('estado', 1)->count();
            $productos_sin_stock = Producto::where('cantidad', '<=', 0)->count();
            $con_descuento = Producto::where('descuento', '>', 0)->count();
            
            $total_categorias = Categoria::count();
            $total_marcas = Marca::count();
            $total_variedades = Variedad::count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_productos' => $total_productos,
                    'productos_activos' => $productos_activos,
                    'productos_sin_stock' => $productos_sin_stock,
                    'con_descuento' => $con_descuento,
                    'total_categorias' => $total_categorias,
                    'total_marcas' => $total_marcas,
                    'total_variedades' => $total_variedades
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al cargar las estadísticas.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
