<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Http\Requests\StoreProyectoRequest;
use App\Http\Requests\UpdateProyectoRequest;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProyectoController extends Controller
{
    public function index()
    {
        $proyectos = Proyecto::withCount(['bloques', 'piezas'])
            ->with(['bloques' => function($query) {
                $query->withCount('piezas');
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('CrearProyecto', [
            'proyectos' => $proyectos
        ]);
    }

    public function store(StoreProyectoRequest $request)
    {
        try {
            DB::beginTransaction();

            Proyecto::create($request->validated());

            DB::commit();
            return redirect()->back()->with('success', 'Proyecto creado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear proyecto: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al crear el proyecto. Inténtelo nuevamente.');
        }
    }

    public function update(UpdateProyectoRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $proyecto = Proyecto::findOrFail($id);
            $proyecto->update($request->validated());

            DB::commit();

            $usuario = Auth::check() ? Auth::user()->email : 'desconocido';
            Log::info("Proyecto actualizado: {$id}", ['usuario' => $usuario]);

            return redirect()->route('proyectos.index')->with('success', 'Proyecto actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar proyecto: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al actualizar el proyecto.');
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $proyecto = Proyecto::findOrFail($id);

            if ($proyecto->bloques()->count() > 0) {
                return redirect()->route('proyectos.index')
                    ->with('error', 'No se puede eliminar el proyecto porque tiene bloques asociados.');
            }

            $proyecto->delete();

            DB::commit();

            $usuario = Auth::check() ? Auth::user()->email : 'desconocido';
            Log::warning("Proyecto eliminado: {$id}", ['usuario' => $usuario]);

            return redirect()->route('proyectos.index')->with('success', 'Proyecto eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar proyecto: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al eliminar el proyecto.');
        }
    }
}
