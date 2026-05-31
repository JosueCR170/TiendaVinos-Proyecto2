<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Todas las rutas son manejadas por el frontend (SPA)
// Esta ruta actúa como un catch-all para la app del frontend
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*')->name('spa');

