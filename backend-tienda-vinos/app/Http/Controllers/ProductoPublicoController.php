<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoPublicoController extends Controller
{
    /**
     * Devuelve el detalle completo de un producto para la vista pública.
     */
    public function show(Producto $producto)
    {
        $producto->load(['marca', 'categoria.padre', 'variedades']);

        return response()->json([
            'data' => [
                'id_producto'         => $producto->id_producto,
                'nombre'              => $producto->nombre,
                'descripcion'         => $producto->descripcion,
                'imagen_url'          => $producto->imagen_url,
                'precio'              => $producto->precio,
                'descuento'           => $producto->descuento ?? 0,
                'cantidad'            => $producto->cantidad,
                'estado'              => $producto->estado,
                'pais'                => $producto->pais,
                'region'              => $producto->region,
                'anio_cosecha'        => $producto->anio_cosecha,
                'alcohol_porcentaje'  => $producto->alcohol_porcentaje,
                'contenido_ml'        => $producto->contenido_ml,
                'marca'               => $producto->marca ? [
                    'id_marca' => $producto->marca->id_marca,
                    'nombre'   => $producto->marca->nombre,
                ] : null,
                'categoria'           => $producto->categoria ? [
                    'id_categoria' => $producto->categoria->id_categoria,
                    'nombre'       => $producto->categoria->nombre,
                    'padre'        => $producto->categoria->padre ? [
                        'id_categoria' => $producto->categoria->padre->id_categoria,
                        'nombre'       => $producto->categoria->padre->nombre,
                    ] : null,
                ] : null,
                'variedades'          => $producto->variedades->map(fn($v) => [
                    'id_variedad' => $v->id_variedad,
                    'nombre'      => $v->nombre,
                ])->values(),
            ]
        ]);
    }
}
