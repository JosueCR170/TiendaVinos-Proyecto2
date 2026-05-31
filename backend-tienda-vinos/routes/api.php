<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->name('api.')->group(function () {

    Route::apiResource('categorias', \App\Http\Controllers\CategoriaController::class);
    Route::apiResource('marcas',     \App\Http\Controllers\MarcaController::class);
    Route::apiResource('variedades', \App\Http\Controllers\VariedadController::class);
    Route::apiResource('productos',  \App\Http\Controllers\ProductoController::class);

});
