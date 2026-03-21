<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\Asistencia;
use App\Models\Matricula;
use App\Models\Pago;
use App\Traits\FiltraPorColegio;
use Carbon\Carbon;

class DashboardController extends Controller
{
    use FiltraPorColegio;

    public function index()
    {
        $colegioId = $this->colegioId();

        $totalAlumnos = Alumno::where('colegio_id', $colegioId)->count();

        $matriculasActivas = Matricula::where('colegio_id', $colegioId)
            ->where('estado', 'activa')
            ->count();

        $asistenciaHoy = Asistencia::where('colegio_id', $colegioId)
            ->where('fecha', Carbon::today())
            ->count();

        $pagosPendientes = Pago::where('colegio_id', $colegioId)
            ->where('estado', 'pendiente')
            ->sum('monto');

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
