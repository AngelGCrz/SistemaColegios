<?php

namespace App\Http\Controllers;

use App\Models\Colegio;
use App\Models\Plan;
use App\Models\Suscripcion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OnboardingController extends Controller
{
    public function mostrarRegistro()
    {
        $planes = Plan::where('activo', true)->orderBy('orden')->get();

        return view('onboarding.registro', compact('planes'));
    }

    public function registrar(Request $request)
    {
        $data = $request->validate([
            'colegio_nombre' => ['required', 'string', 'max:200'],
            'colegio_email' => ['nullable', 'email', 'max:255'],
            'colegio_telefono' => ['nullable', 'string', 'max:20'],
            'colegio_direccion' => ['nullable', 'string', 'max:500'],
            'contacto_nombre' => ['required', 'string', 'max:200'],
            'contacto_email' => ['required', 'email', 'max:255'],
            'contacto_telefono' => ['nullable', 'string', 'max:20'],
            'plan_id' => ['required', 'exists:planes,id'],
            'admin_nombre' => ['required', 'string', 'max:100'],
            'admin_apellidos' => ['required', 'string', 'max:100'],
            'admin_email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'admin_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $plan = Plan::findOrFail($data['plan_id']);

        $colegio = DB::transaction(function () use ($data, $plan) {
            $colegio = Colegio::create([
                'nombre' => $data['colegio_nombre'],
                'email' => $data['colegio_email'],
                'telefono' => $data['colegio_telefono'],
                'direccion' => $data['colegio_direccion'],
                'contacto_nombre' => $data['contacto_nombre'],
                'contacto_email' => $data['contacto_email'],
                'contacto_telefono' => $data['contacto_telefono'],
                'plan' => $plan->slug,
                'activo' => true,
                'fecha_vencimiento' => now()->addDays(30),
            ]);

            Suscripcion::create([
                'colegio_id' => $colegio->id,
                'plan_id' => $plan->id,
                'estado' => 'trial',
                'ciclo' => 'mensual',
                'fecha_inicio' => now(),
                'fecha_fin' => now()->addDays(30),
                'trial_hasta' => now()->addDays(30),
                'monto' => $plan->precio_mensual,
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

        return redirect()->route('login')
            ->with('success', "¡Registro exitoso! Tu colegio '{$colegio->nombre}' tiene 30 días de prueba gratuita. Inicia sesión con tu email y contraseña.");
    }
}
