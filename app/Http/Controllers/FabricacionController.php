<?php

namespace App\Http\Controllers;

use App\Models\Pieza;
use App\Http\Requests\RegistrarPesoRequest;
use App\Services\FabricacionService;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FabricacionController extends Controller
{
    protected $fabricacionService;

    public function __construct(FabricacionService $fabricacionService)
    {
        $this->fabricacionService = $fabricacionService;
    }

    public function index()
    {
        $piezas = Pieza::with(['bloque.proyecto'])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('RegistrarFabricacion', [
            'piezas' => $piezas,
            'success' => session('success')
        ]);
    }

    public function registrarPeso(RegistrarPesoRequest $request)
    {
        try {
            $resultado = $this->fabricacionService->registrarPesoReal(
                $request->pieza_id,
                $request->peso_real,
                Auth::check() ? Auth::user()->name : 'Sistema'
            );

            $mensaje = 'Peso registrado correctamente. Pieza marcada como fabricada.';

            // Informar sobre la diferencia si es significativa
            $porcentajeDiferencia = abs($resultado['porcentaje_diferencia']);
            if ($porcentajeDiferencia > 20) {
                $diferencia = $resultado['diferencia'];
                $signo = $diferencia > 0 ? '+' : '';
                $mensaje .= " Diferencia: {$signo}" . number_format($diferencia, 2) . " kg (" .
                           number_format($porcentajeDiferencia, 1) . "%).";
            }

            return redirect()->back()->with('success', $mensaje);

        } catch (\Exception $e) {
            Log::error('Error al registrar peso: ' . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
