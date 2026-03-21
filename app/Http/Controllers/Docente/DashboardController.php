<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\CursoSeccion;
use App\Traits\FiltraPorColegio;

class DashboardController extends Controller
{
    use FiltraPorColegio;

    public function index()
    {
        $docente = auth()->user()->docente;

        $cursoSecciones = CursoSeccion::where('docente_id', $docente->id)
            ->with(['curso', 'seccion.grado.nivel', 'seccion.matriculas'])
            ->get();

        return view('docente.dashboard', compact('cursoSecciones'));
    }
}
