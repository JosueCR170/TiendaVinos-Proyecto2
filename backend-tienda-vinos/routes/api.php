<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1/')->name('api.')->group(function () {

    Route::apiResource('categorias', \App\Http\Controllers\CategoriaController::class);
    Route::apiResource('marcas',     \App\Http\Controllers\MarcaController::class);
    Route::apiResource('variedades', \App\Http\Controllers\VariedadController::class);
    Route::apiResource('productos',  \App\Http\Controllers\ProductoController::class);

    Route::post('images/upload', [\App\Http\Controllers\ImageUploadController::class, 'upload'])->name('images.upload');
    Route::delete('images/delete', [\App\Http\Controllers\ImageUploadController::class, 'delete'])->name('images.delete');

});
