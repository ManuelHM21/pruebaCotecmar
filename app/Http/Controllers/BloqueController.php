<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\Bloque;
use App\Http\Requests\StoreBloqueRequest;
use App\Http\Requests\UpdateBloqueRequest;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BloqueController extends Controller
{
    public function index()
    {
        return Inertia::render('CrearBloque', [
            'proyectos' => Proyecto::all(),
            'bloques' => Bloque::with('proyecto')
                ->withCount('piezas')
                ->orderBy('created_at', 'desc')
                ->get()
        ]);
    }

    public function getByProyecto($proyecto_id)
    {
        try {
            $bloques = Bloque::where('proyecto_id', $proyecto_id)
                ->select('id', 'nombre', 'proyecto_id')
                ->orderBy('nombre')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $bloques,
                'count' => $bloques->count()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los bloques',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(StoreBloqueRequest $request)
    {
        try {
            DB::beginTransaction();

            Bloque::create($request->validated());

            DB::commit();
            return redirect()->back()->with('success', 'Bloque creado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear bloque: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al crear el bloque.');
        }
    }

    public function update(UpdateBloqueRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $bloque = Bloque::findOrFail($id);
            $bloque->update($request->validated());

            DB::commit();
            
            $usuario = Auth::check() ? Auth::user()->email : 'desconocido';
            Log::info("Bloque actualizado: {$id}", ['usuario' => $usuario]);
            
            return redirect()->route('bloques.index')->with('success', 'Bloque actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar bloque: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al actualizar el bloque.');
        }
    }
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $bloque = Bloque::findOrFail($id);

            if ($bloque->piezas()->count() > 0) {
                return redirect()->route('bloques.index')
                    ->with('error', 'No se puede eliminar el bloque porque tiene piezas asociadas.');
            }

            $bloque->delete();

            DB::commit();
            
            $usuario = Auth::check() ? Auth::user()->email : 'desconocido';
            Log::warning("Bloque eliminado: {$id}", ['usuario' => $usuario]);
            
            return redirect()->route('bloques.index')->with('success', 'Bloque eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar bloque: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al eliminar el bloque.');
        }
    }
}
