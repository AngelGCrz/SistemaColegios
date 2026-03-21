<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Colegio;
use App\Models\Plan;
use App\Models\Suscripcion;
use App\Models\PagoSuscripcion;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_colegios' => Colegio::count(),
            'colegios_activos' => Colegio::where('activo', true)->count(),
            'colegios_trial' => Suscripcion::where('estado', 'trial')->count(),
            'colegios_vencidos' => Suscripcion::where('estado', 'vencida')->count(),
            'ingresos_mes' => PagoSuscripcion::where('estado', 'aprobado')
                ->whereMonth('pagado_en', now()->month)
                ->whereYear('pagado_en', now()->year)
                ->sum('monto'),
        ];

        $colegiosRecientes = Colegio::with('suscripcionActiva.plan')
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        return view('superadmin.dashboard', compact('stats', 'colegiosRecientes'));
    }
}
