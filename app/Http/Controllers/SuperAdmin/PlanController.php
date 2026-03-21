<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $planes = Plan::withCount('suscripciones')
            ->orderBy('orden')
            ->get();

        return view('superadmin.planes.index', compact('planes'));
    }

    public function create()
    {
        return view('superadmin.planes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:50'],
            'slug' => ['required', 'string', 'max:30', 'unique:planes,slug', 'alpha_dash'],
            'precio_mensual' => ['required', 'numeric', 'min:0'],
            'precio_anual' => ['required', 'numeric', 'min:0'],
            'max_alumnos' => ['required', 'integer', 'min:1'],
            'caracteristicas' => ['nullable', 'string'],
            'orden' => ['required', 'integer', 'min:0'],
        ]);

        $data['caracteristicas'] = array_filter(
            array_map('trim', explode("\n", $data['caracteristicas'] ?? ''))
        );

        Plan::create($data);

        return redirect()->route('superadmin.planes.index')
            ->with('success', 'Plan creado.');
    }

    public function edit(Plan $plan)
    {
        return view('superadmin.planes.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:50'],
            'precio_mensual' => ['required', 'numeric', 'min:0'],
            'precio_anual' => ['required', 'numeric', 'min:0'],
            'max_alumnos' => ['required', 'integer', 'min:1'],
            'caracteristicas' => ['nullable', 'string'],
            'orden' => ['required', 'integer', 'min:0'],
            'activo' => ['boolean'],
        ]);

        $data['caracteristicas'] = array_filter(
            array_map('trim', explode("\n", $data['caracteristicas'] ?? ''))
        );
        $data['activo'] = $request->boolean('activo');

        $plan->update($data);

        return redirect()->route('superadmin.planes.index')
            ->with('success', 'Plan actualizado.');
    }
}
