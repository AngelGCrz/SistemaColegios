<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Periodo;
use App\Traits\FiltraPorColegio;

class ReporteController extends Controller
{
    use FiltraPorColegio;

    public function index()
    {
        $colegioId = $this->colegioId();

        $periodos = Periodo::where('colegio_id', $colegioId)
            ->orderByDesc('anio')
            ->get();

        $periodoActivo = $periodos->firstWhere('activo', true);

        return view('admin.reportes.index', compact('periodos', 'periodoActivo'));
    }
}
