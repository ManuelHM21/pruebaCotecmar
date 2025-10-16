<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BloqueController;
use App\Http\Controllers\PiezaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * API Endpoints protegidos con Sanctum
 */
Route::middleware('auth:sanctum')->group(function() {
    Route::get('/proyectos/{proyecto}/bloques', [BloqueController::class, 'getByProyecto'])
        ->name('api.proyectos.bloques');

    Route::get('/bloques/{bloque}/piezas', [PiezaController::class, 'getByBloque'])
        ->name('api.bloques.piezas');

    Route::get('/piezas/{pieza}/peso-teorico', [PiezaController::class, 'getPesoTeorico'])
        ->name('api.piezas.peso-teorico');
});
