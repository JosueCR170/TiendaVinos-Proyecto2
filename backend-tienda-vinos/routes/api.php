<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Home: destacados y descuentos
Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index']);

// Catálogo público con filtros y paginación
Route::get('/catalogo/meta',  [\App\Http\Controllers\CatalogoController::class, 'meta']);
Route::get('/catalogo',       [\App\Http\Controllers\CatalogoController::class, 'index']);

// Detalle de producto público
Route::get('/producto/{producto}', [\App\Http\Controllers\ProductoPublicoController::class, 'show']);

// ── Endpoints de administración (prefijo /api/v1/admin) ────────────────────

Route::prefix('v1/admin')->name('api.admin.')->group(function () {

    Route::apiResource('categorias', \App\Http\Controllers\Admin\CategoriaController::class);
    Route::apiResource('marcas',     \App\Http\Controllers\Admin\MarcaController::class);
    Route::apiResource('variedades', \App\Http\Controllers\Admin\VariedadController::class);
    Route::apiResource('productos',  \App\Http\Controllers\Admin\ProductoController::class);

    // Datos auxiliares para formularios de productos
    Route::get('productos-form-data', [\App\Http\Controllers\Admin\ProductoController::class, 'formData']);

    // Dashboard stats
    Route::get('dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index']);


});
