<?php

namespace App\Services;

use App\Models\Pieza;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FabricacionService
{
    /**
     *
     * @param string $piezaId
     * @param float $pesoReal
     * @param string $usuario
     * @return array
     * @throws \Exception
     */
    public function registrarPesoReal(string $piezaId, float $pesoReal, string $usuario): array
    {
        return DB::transaction(function () use ($piezaId, $pesoReal, $usuario) {
            $pieza = Pieza::lockForUpdate()->findOrFail($piezaId);

            if ($pieza->estado !== 'Pendiente') {
                throw new \Exception('La pieza ya ha sido fabricada.');
            }

            $diferencia = $pesoReal - $pieza->peso_teorico;
            $porcentajeDiferencia = ($diferencia / $pieza->peso_teorico) * 100;

            $pieza->update([
                'peso_real' => $pesoReal,
                'estado' => 'Fabricado',
                'fecha_registro' => now(),
                'registrado_por' => $usuario,
            ]);

            Log::info("Peso registrado para pieza: {$piezaId}", [
                'peso_teorico' => $pieza->peso_teorico,
                'peso_real' => $pesoReal,
                'diferencia' => $diferencia,
                'porcentaje_diferencia' => round($porcentajeDiferencia, 2),
                'usuario' => $usuario
            ]);

            return [
                'success' => true,
                'pieza' => $pieza,
                'diferencia' => $diferencia,
                'porcentaje_diferencia' => $porcentajeDiferencia,
            ];
        });
    }

    /**
     *
     * @return array
     */
    public function obtenerEstadisticas(): array
    {
        $totalPiezas = Pieza::count();
        $piezasFabricadas = Pieza::where('estado', 'Fabricado')->count();
        $piezasPendientes = Pieza::where('estado', 'Pendiente')->count();
        $porcentajeCompletado = $totalPiezas > 0
            ? round(($piezasFabricadas / $totalPiezas) * 100, 2)
            : 0;

        return [
            'total' => $totalPiezas,
            'fabricadas' => $piezasFabricadas,
            'pendientes' => $piezasPendientes,
            'porcentaje_completado' => $porcentajeCompletado,
        ];
    }

    /**
     *
     * @param float $porcentajeUmbral
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function obtenerPiezasConGrandesDiferencias(float $porcentajeUmbral = 10)
    {
        return Pieza::where('estado', 'Fabricado')
            ->whereNotNull('peso_real')
            ->get()
            ->filter(function ($pieza) use ($porcentajeUmbral) {
                $diferencia = abs($pieza->peso_real - $pieza->peso_teorico);
                $porcentaje = ($diferencia / $pieza->peso_teorico) * 100;
                return $porcentaje > $porcentajeUmbral;
            });
    }
}
