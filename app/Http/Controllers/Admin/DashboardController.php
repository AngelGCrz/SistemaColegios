<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\Asistencia;
use App\Models\Matricula;
use App\Models\Pago;
use App\Traits\FiltraPorColegio;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    use FiltraPorColegio;

    public function index()
    {
        $colegioId = $this->colegioId();

        $stats = Cache::remember("dashboard_stats_{$colegioId}", 300, function () use ($colegioId) {
            return [
                'totalAlumnos' => Alumno::where('colegio_id', $colegioId)->count(),
                'matriculasActivas' => Matricula::where('colegio_id', $colegioId)
                    ->where('estado', 'activa')
                    ->count(),
                'pagosPendientes' => Pago::where('colegio_id', $colegioId)
                    ->where('estado', 'pendiente')
                    ->sum('monto'),
            ];
        });

        $totalAlumnos = $stats['totalAlumnos'];
        $matriculasActivas = $stats['matriculasActivas'];
        $pagosPendientes = $stats['pagosPendientes'];

        $asistenciaHoy = Asistencia::where('colegio_id', $colegioId)
            ->where('fecha', Carbon::today())
            ->count();

        $pagosDelMes = Pago::where('colegio_id', $colegioId)
            ->where('estado', 'pagado')
            ->whereMonth('fecha_pago', Carbon::now()->month)
            ->whereYear('fecha_pago', Carbon::now()->year)
            ->sum('monto');

        return view('admin.dashboard', compact(
            'totalAlumnos',
            'matriculasActivas',
            'asistenciaHoy',
            'pagosPendientes',
            'pagosDelMes',
        ));
    }
}
