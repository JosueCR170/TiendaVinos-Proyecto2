<?php

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Route;

// /*
// |--------------------------------------------------------------------------
// | API Routes
// |--------------------------------------------------------------------------
// |
// | Here is where you can register API routes for your application. These
// | routes are loaded by the RouteServiceProvider within a group which
// | is assigned the "api" middleware group. Enjoy building your API!
// |
// */

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

use Illuminate\Support\Facades\Route;



// ── Rutas públicas del frontend ───────────────────────────────────────────────

Route::prefix('v1')->name('api.')->group(function () {

    // Catálogo / productos públicos
    Route::get('/productos',           [\App\Http\Controllers\Api\ProductoPublicoController::class, 'index'])->name('productos.index');
    Route::get('/productos/{id}',      [\App\Http\Controllers\Api\ProductoPublicoController::class, 'show'])->name('productos.show');

    // Carrito (basado en sesión o token de usuario)
    Route::prefix('carrito')->name('carrito.')->group(function () {
        Route::get('/',                [\App\Http\Controllers\Api\CarritoController::class, 'index'])->name('index');
        Route::post('/agregar/{id}',   [\App\Http\Controllers\Api\CarritoController::class, 'agregar'])->name('agregar');
        Route::patch('/actualizar/{id}', [\App\Http\Controllers\Api\CarritoController::class, 'actualizar'])->name('actualizar');
        Route::delete('/eliminar/{id}',[\App\Http\Controllers\Api\CarritoController::class, 'eliminar'])->name('eliminar');
    });

    // Checkout
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/',                [\App\Http\Controllers\Api\CheckoutController::class, 'index'])->name('index');
        Route::post('/pagar',          [\App\Http\Controllers\Api\CheckoutController::class, 'procesarPago'])->name('pagar');
    });

    // ── Rutas de administración ───────────────────────────────────────────────
    // Agregar ->middleware(['auth:sanctum', 'role:admin']) cuando esté lista la auth
    Route::prefix('admin')->name('admin.')->group(function () {

        Route::get('/dashboard',       [\App\Http\Controllers\Admin\AdminController::class, 'index'])->name('dashboard');
        Route::apiResource('categorias', \App\Http\Controllers\Admin\CategoriaController::class);
        Route::apiResource('marcas',     \App\Http\Controllers\Admin\MarcaController::class);
        Route::apiResource('variedades', \App\Http\Controllers\Admin\VariedadController::class);
        Route::apiResource('productos',  \App\Http\Controllers\Admin\ProductoController::class);
    });
});
