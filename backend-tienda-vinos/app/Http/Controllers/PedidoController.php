<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    public function store(Request $request)
    {
        $carrito = $request->input('carrito', []);

        if (empty($carrito)) {
            return response()->json([
                'success' => false,
                'message' => 'El carrito está vacío.'
            ], 400);
        }

        try {
            DB::beginTransaction();

            foreach ($carrito as $item) {
                $producto = Producto::find($item['idProducto']);

                if (!$producto) {
                    throw new \Exception("El producto '{$item['nombre']}' ya no existe.");
                }

                if ($producto->cantidad < $item['cantidad']) {
                    throw new \Exception("Stock insuficiente para el producto '{$producto->nombre}'. Quedan {$producto->cantidad} unidades.");
                }

                $producto->cantidad -= $item['cantidad'];
                $producto->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pago procesado y stock actualizado correctamente.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
