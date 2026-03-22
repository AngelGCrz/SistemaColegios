<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Colegio;
use App\Models\PagoSuscripcion;
use App\Models\Plan;
use App\Models\Suscripcion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ColegioController extends Controller
{
    public function index(Request $request)
    {
        $query = Colegio::with('suscripcionActiva.plan')
            ->withCount('alumnos');

        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('email', 'like', "%{$buscar}%")
                  ->orWhere('contacto_email', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('activo', $request->input('estado') === 'activo');
        }

        if ($request->filled('plan')) {
            $planSlug = $request->input('plan');
            $query->whereHas('suscripcionActiva', fn ($q) =>
                $q->whereHas('plan', fn ($p) => $p->where('slug', $planSlug))
            );
        }

        $colegios = $query->orderByDesc('created_at')->paginate(20);
        $planes = Plan::where('activo', true)->orderBy('orden')->get();

        return view('superadmin.colegios.index', compact('colegios', 'planes'));
    }

    public function create()
    {
        $planes = Plan::where('activo', true)->orderBy('orden')->get();

        return view('superadmin.colegios.create', compact('planes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:200'],
            'subdominio' => ['required', 'string', 'max:50', 'regex:/^[a-z0-9]([a-z0-9-]*[a-z0-9])?$/', 'unique:colegios,subdominio', 'not_in:www,api,mail,ftp,admin,app,panel'],
            'email' => ['nullable', 'email', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:500'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'contacto_nombre' => ['required', 'string', 'max:200'],
            'contacto_email' => ['required', 'email', 'max:255'],
            'contacto_telefono' => ['nullable', 'string', 'max:20'],
            'plan_id' => ['required', 'exists:planes,id'],
            'ciclo' => ['required', 'in:mensual,anual'],
            'admin_email' => ['required', 'email', 'max:255'],
            'admin_password' => ['required', 'string', 'min:8'],
            'admin_nombre' => ['required', 'string', 'max:100'],
            'admin_apellidos' => ['required', 'string', 'max:100'],
        ]);

        $plan = Plan::findOrFail($data['plan_id']);

        $colegio = DB::transaction(function () use ($data, $plan) {
            $monto = $data['ciclo'] === 'anual' ? $plan->precio_anual : $plan->precio_mensual;
            $fechaFin = $data['ciclo'] === 'anual' ? now()->addYear() : now()->addMonth();

            $colegio = Colegio::create([
                'nombre' => $data['nombre'],
                'subdominio' => $data['subdominio'],
                'email' => $data['email'],
                'direccion' => $data['direccion'],
                'telefono' => $data['telefono'],
                'contacto_nombre' => $data['contacto_nombre'],
                'contacto_email' => $data['contacto_email'],
                'contacto_telefono' => $data['contacto_telefono'],
                'plan' => $plan->slug,
                'activo' => true,
                'fecha_vencimiento' => $fechaFin,
            ]);

            Suscripcion::create([
                'colegio_id' => $colegio->id,
                'plan_id' => $plan->id,
                'estado' => 'trial',
                'ciclo' => $data['ciclo'],
                'fecha_inicio' => now(),
                'fecha_fin' => $fechaFin,
                'trial_hasta' => now()->addDays(30),
                'monto' => $monto,
            ]);

            User::create([
                'colegio_id' => $colegio->id,
                'nombre' => $data['admin_nombre'],
                'apellidos' => $data['admin_apellidos'],
                'email' => $data['admin_email'],
                'password' => Hash::make($data['admin_password']),
                'rol' => 'admin',
                'activo' => true,
            ]);

            return $colegio;
        });

        return redirect()->route('superadmin.colegios.index')
            ->with('success', "Colegio '{$colegio->nombre}' creado con trial de 30 días.");
    }

    public function show(Colegio $colegio)
    {
        $colegio->load(['suscripciones.plan', 'suscripcionActiva.plan']);
        $stats = [
            'usuarios' => $colegio->users()->count(),
            'alumnos' => $colegio->alumnos()->count(),
            'docentes' => $colegio->docentes()->count(),
            'admin' => $colegio->users()->where('rol', 'admin')->first(),
        ];
        $pagos = PagoSuscripcion::where('colegio_id', $colegio->id)
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        return view('superadmin.colegios.show', compact('colegio', 'stats', 'pagos'));
    }

    public function edit(Colegio $colegio)
    {
        $planes = Plan::where('activo', true)->orderBy('orden')->get();

        return view('superadmin.colegios.edit', compact('colegio', 'planes'));
    }

    public function update(Request $request, Colegio $colegio)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:200'],
            'subdominio' => ['required', 'string', 'max:50', 'regex:/^[a-z0-9]([a-z0-9-]*[a-z0-9])?$/', Rule::unique('colegios', 'subdominio')->ignore($colegio->id), 'not_in:www,api,mail,ftp,admin,app,panel'],
            'email' => ['nullable', 'email', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:500'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'contacto_nombre' => ['nullable', 'string', 'max:200'],
            'contacto_email' => ['nullable', 'email', 'max:255'],
            'contacto_telefono' => ['nullable', 'string', 'max:20'],
            'activo' => ['boolean'],
        ]);

        $data['activo'] = $request->boolean('activo');
        $colegio->update($data);

        return redirect()->route('superadmin.colegios.show', $colegio)
            ->with('success', 'Colegio actualizado.');
    }

    public function toggleActivo(Colegio $colegio)
    {
        $colegio->update(['activo' => !$colegio->activo]);
        $estado = $colegio->activo ? 'activado' : 'desactivado';

        return back()->with('success', "Colegio {$estado}.");
    }

    public function cambiarPlan(Request $request, Colegio $colegio)
    {
        $data = $request->validate([
            'plan_id' => ['required', 'exists:planes,id'],
            'ciclo' => ['required', 'in:mensual,anual'],
        ]);

        $plan = Plan::findOrFail($data['plan_id']);
        $monto = $data['ciclo'] === 'anual' ? $plan->precio_anual : $plan->precio_mensual;
        $fechaFin = $data['ciclo'] === 'anual' ? now()->addYear() : now()->addMonth();

        // Marcar suscripción actual como cancelada
        $colegio->suscripciones()
            ->whereIn('estado', ['activa', 'trial'])
            ->update(['estado' => 'cancelada']);

        Suscripcion::create([
            'colegio_id' => $colegio->id,
            'plan_id' => $plan->id,
            'estado' => 'activa',
            'ciclo' => $data['ciclo'],
            'fecha_inicio' => now(),
            'fecha_fin' => $fechaFin,
            'monto' => $monto,
        ]);

        $colegio->update([
            'plan' => $plan->slug,
            'fecha_vencimiento' => $fechaFin,
        ]);

        return back()->with('success', "Plan cambiado a {$plan->nombre}.");
    }
}
