<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->name('api.')->group(function () {
    
    Route::apiResource('categorias', \App\Http\Controllers\CategoriaController::class);
    Route::apiResource('marcas', \App\Http\Controllers\MarcaController::class);
    Route::apiResource('variedades', \App\Http\Controllers\VariedadController::class);
    Route::apiResource('productos', \App\Http\Controllers\ProductoController::class);
 
});
