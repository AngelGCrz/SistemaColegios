<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Periodo;
use App\Traits\FiltraPorColegio;
use Illuminate\Http\Request;

class PeriodoController extends Controller
{
    use FiltraPorColegio;

    public function index()
    {
        $periodos = Periodo::where('colegio_id', $this->colegioId())
            ->orderByDesc('anio')
            ->get();

        return view('admin.periodos.index', compact('periodos'));
    }

    public function create()
    {
        return view('admin.periodos.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:50'],
            'anio' => ['required', 'integer', 'min:2020', 'max:2099'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date', 'after:fecha_inicio'],
        ]);

        // Si se marca como activo, desactivar los demás
        if ($request->boolean('activo')) {
            Periodo::where('colegio_id', $this->colegioId())
                ->update(['activo' => false]);
        }

        Periodo::create([
            'colegio_id' => $this->colegioId(),
            'activo' => $request->boolean('activo'),
            ...$data,
        ]);

        return redirect()->route('admin.periodos.index')
            ->with('success', 'Periodo creado exitosamente.');
    }

    public function edit(Periodo $periodo)
    {
        abort_if($periodo->colegio_id !== $this->colegioId(), 403);

        return view('admin.periodos.edit', compact('periodo'));
    }

    public function update(Request $request, Periodo $periodo)
    {
        abort_if($periodo->colegio_id !== $this->colegioId(), 403);

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:50'],
            'anio' => ['required', 'integer', 'min:2020', 'max:2099'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date', 'after:fecha_inicio'],
        ]);

        if ($request->boolean('activo')) {
            Periodo::where('colegio_id', $this->colegioId())
                ->where('id', '!=', $periodo->id)
                ->update(['activo' => false]);
        }

        $periodo->update([
            'activo' => $request->boolean('activo'),
            ...$data,
        ]);

        return redirect()->route('admin.periodos.index')
            ->with('success', 'Periodo actualizado exitosamente.');
    }
}
