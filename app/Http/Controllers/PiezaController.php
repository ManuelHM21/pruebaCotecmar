<?php

namespace App\Http\Controllers;

use App\Models\Bloque;
use App\Models\Pieza;
use App\Http\Requests\StorePiezaRequest;
use App\Http\Requests\UpdatePiezaRequest;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PiezaController extends Controller
{
    public function index()
    {
        return Inertia::render('CrearPieza', [
            'bloques' => Bloque::with('proyecto')->get(),
            'piezas' => Pieza::with('bloque.proyecto')
                ->orderBy('created_at', 'desc')
                ->get()
        ]);
    }

    public function getByBloque($bloque_id)
    {
        try {
            $piezas = Pieza::where('bloque_id', $bloque_id)
                ->where('estado', 'Pendiente')
                ->select('id', 'nombre', 'peso_teorico', 'bloque_id', 'estado')
                ->orderBy('nombre')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $piezas,
                'count' => $piezas->count()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las piezas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getPesoTeorico($pieza_id)
    {
        try {
            $pieza = Pieza::find($pieza_id);

            if (!$pieza) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pieza no encontrada'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $pieza->id,
                    'nombre' => $pieza->nombre,
                    'peso_teorico' => $pieza->peso_teorico
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el peso teórico',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(StorePiezaRequest $request)
    {
        Pieza::create(array_merge(
            $request->validated(),
            [
                'estado' => 'Pendiente',
                'fecha_registro' => now(),
                'registrado_por' => Auth::check() ? Auth::user()->name : 'Sistema',
            ]
        ));

        return redirect()->back()->with('success', 'Pieza creada correctamente');
    }

    public function update(UpdatePiezaRequest $request, $id)
    {
        $pieza = Pieza::findOrFail($id);
        $pieza->update($request->validated());

        return redirect()->route('piezas.index')->with('success', 'Pieza actualizada exitosamente.');
    }

    public function destroy($id)
    {
        $pieza = Pieza::findOrFail($id);

        if ($pieza->estado === 'Fabricado') {
            return redirect()->route('piezas.index')
                ->with('error', 'No se puede eliminar una pieza que ya fue fabricada.');
        }

        $pieza->delete();

        return redirect()->route('piezas.index')->with('success', 'Pieza eliminada exitosamente.');
    }
}
