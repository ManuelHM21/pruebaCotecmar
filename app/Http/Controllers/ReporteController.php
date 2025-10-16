<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proyecto;
use Inertia\Inertia;

class ReporteController extends Controller
{
    public function pendientes()
    {
        $proyectos = Proyecto::with(['bloques.piezas' => function($q) {
            $q->where('estado', 'Pendiente');
        }])->get();

        return Inertia::render('ReportePendientes', [
            'proyectos' => $proyectos
        ]);
    }

    public function grafico()
    {
        $proyectos = Proyecto::with(['bloques.piezas'])->get();

        return Inertia::render('ReporteGrafico', [
            'proyectos' => $proyectos
        ]);
    }
}
