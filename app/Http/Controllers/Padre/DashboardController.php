<?php

namespace App\Http\Controllers\Padre;

use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use App\Models\Aviso;
use App\Models\Nota;
use App\Models\Pago;
use App\Traits\FiltraPorColegio;

class DashboardController extends Controller
{
    use FiltraPorColegio;

    public function index()
    {
        $padre = auth()->user()->padre;
        $hijos = $padre->alumnos()->with('user', 'matriculas.periodo', 'matriculas.seccion.grado.nivel')->get();

        $avisos = Aviso::where('colegio_id', $this->colegioId())
            ->where('publicado', true)
            ->whereIn('destinatario', ['todos', 'padres'])
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return view('padre.dashboard', compact('hijos', 'avisos'));
    }

    public function notasHijo(int $alumnoId)
    {
        $padre = auth()->user()->padre;

        // Verificar que el alumno es hijo de este padre
        $alumno = $padre->alumnos()->where('alumnos.id', $alumnoId)->firstOrFail();
        $matricula = $alumno->matriculaActiva();
        $matricula?->load('seccion.grado.nivel', 'periodo');

        $notas = collect();
        $cursos = collect();
        $bimestres = collect();
        if ($matricula) {
            $notas = \App\Models\Nota::where('matricula_id', $matricula->id)
                ->with(['cursoSeccion.curso', 'bimestre'])
                ->get();

            $cursos = \App\Models\CursoSeccion::where('seccion_id', $matricula->seccion_id)
                ->with('curso')
                ->get();

            $bimestres = \App\Models\Bimestre::where('periodo_id', $matricula->periodo_id)
                ->orderBy('numero')
                ->get();
        }

        return view('padre.notas', compact('alumno', 'notas', 'matricula', 'cursos', 'bimestres'));
    }

    public function asistenciaHijo(int $alumnoId)
    {
        $padre = auth()->user()->padre;
        $alumno = $padre->alumnos()->where('alumnos.id', $alumnoId)->firstOrFail();
        $matricula = $alumno->matriculaActiva();
        $matricula?->load('seccion.grado.nivel');

        $asistencias = collect();
        if ($matricula) {
            $asistencias = Asistencia::where('matricula_id', $matricula->id)
                ->orderByDesc('fecha')
                ->take(30)
                ->get();
        }

        $resumen = [
            'presentes' => $asistencias->where('estado', 'presente')->count(),
            'faltas' => $asistencias->where('estado', 'falta')->count(),
            'tardanzas' => $asistencias->where('estado', 'tardanza')->count(),
        ];

        return view('padre.asistencia', compact('alumno', 'asistencias', 'resumen'));
    }

    public function pagosHijo(int $alumnoId)
    {
        $padre = auth()->user()->padre;
        $alumno = $padre->alumnos()->where('alumnos.id', $alumnoId)->firstOrFail();

        $pagos = Pago::where('alumno_id', $alumno->id)
            ->with('conceptoPago')
            ->orderByDesc('created_at')
            ->get();

        $totalPendiente = $pagos->where('estado', 'pendiente')->sum('monto');

        return view('padre.pagos', compact('alumno', 'pagos', 'totalPendiente'));
    }
}
