<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\BloqueController;
use App\Http\Controllers\PiezaController;
use App\Http\Controllers\FabricacionController;
use App\Http\Controllers\ReporteController;

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

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/dashboard', function () {
        return redirect()->route('fabricacion.index');
    })->name('dashboard');

    Route::get('/peso-real', [FabricacionController::class, 'index'])->name('peso-real');
    Route::get('/fabricacion', [FabricacionController::class, 'index'])->name('fabricacion.index');
    Route::post('/fabricacion/registrar-peso', [FabricacionController::class, 'registrarPeso'])->name('fabricacion.registrar');

    Route::get('/proyectos', [ProyectoController::class, 'index'])->name('proyectos.index');
    Route::get('/crear-proyecto', [ProyectoController::class, 'index'])->name('crear-proyecto'); // Alias para compatibilidad con vistas
    Route::post('/proyectos', [ProyectoController::class, 'store'])->name('proyectos.store');
    Route::put('/proyectos/{id}', [ProyectoController::class, 'update'])->name('proyectos.update');
    Route::delete('/proyectos/{id}', [ProyectoController::class, 'destroy'])->name('proyectos.destroy');

    Route::get('/bloques', [BloqueController::class, 'index'])->name('bloques.index');
    Route::get('/crear-bloque', [BloqueController::class, 'index'])->name('crear-bloque'); // Alias para compatibilidad con vistas
    Route::get('/bloques/proyecto/{proyecto_id}', [BloqueController::class, 'getByProyecto'])->name('bloques.by-proyecto');
    Route::post('/bloques', [BloqueController::class, 'store'])->name('bloques.store');
    Route::put('/bloques/{id}', [BloqueController::class, 'update'])->name('bloques.update');
    Route::delete('/bloques/{id}', [BloqueController::class, 'destroy'])->name('bloques.destroy');

    Route::get('/piezas', [PiezaController::class, 'index'])->name('piezas.index');
    Route::get('/crear-pieza', [PiezaController::class, 'index'])->name('crear-pieza'); // Alias para compatibilidad con vistas
    Route::get('/piezas/bloque/{bloque_id}', [PiezaController::class, 'getByBloque'])->name('piezas.by-bloque');
    Route::get('/piezas/{pieza_id}/peso-teorico', [PiezaController::class, 'getPesoTeorico'])->name('piezas.peso-teorico');
    Route::post('/piezas', [PiezaController::class, 'store'])->name('piezas.store');
    Route::put('/piezas/{id}', [PiezaController::class, 'update'])->name('piezas.update');
    Route::delete('/piezas/{id}', [PiezaController::class, 'destroy'])->name('piezas.destroy');

    Route::get('/reportes/pendientes', [ReporteController::class, 'pendientes'])->name('reportes.pendientes');
    Route::get('/reporte/pendientes', [ReporteController::class, 'pendientes'])->name('reporte.pendientes'); // Alias para compatibilidad con vistas
    Route::get('/reportes/grafico', [ReporteController::class, 'grafico'])->name('reportes.grafico');
    Route::get('/reporte/grafico', [ReporteController::class, 'grafico'])->name('reporte.grafico'); // Alias para compatibilidad con vistas
});
