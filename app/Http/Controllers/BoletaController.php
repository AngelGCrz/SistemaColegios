<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Bimestre;
use App\Models\CursoSeccion;
use App\Models\Matricula;
use App\Models\Nota;
use App\Traits\FiltraPorColegio;
use Barryvdh\DomPDF\Facade\Pdf;

class BoletaController extends Controller
{
    use FiltraPorColegio;

    public function descargar(Matricula $matricula)
    {
        abort_if($matricula->colegio_id !== $this->colegioId(), 403);

        // Verificar acceso: admin, o el propio alumno, o un padre del alumno
        $user = auth()->user();
        if (!$user->esAdmin()) {
            if ($user->esAlumno() && $user->alumno->id !== $matricula->alumno_id) {
                abort(403);
            }
            if ($user->esPadre()) {
                $esHijo = $user->padre->alumnos()
                    ->where('alumnos.id', $matricula->alumno_id)
                    ->exists();
                abort_if(!$esHijo, 403);
            }
        }

        $matricula->load(['alumno.user', 'seccion.grado.nivel', 'periodo']);

        $alumno = $matricula->alumno;
        $colegio = $user->colegio;

        // Bimestres del periodo
        $bimestres = Bimestre::where('periodo_id', $matricula->periodo_id)
            ->orderBy('numero')
            ->get();

        // Cursos asignados a la sección del alumno
        $cursos = CursoSeccion::where('seccion_id', $matricula->seccion_id)
            ->with('curso')
            ->get();

        // Todas las notas (flat collection para buscar por curso_seccion_id + bimestre_id)
        $notas = Nota::where('matricula_id', $matricula->id)
            ->with(['cursoSeccion.curso', 'bimestre'])
            ->get();

        // Resumen de asistencia
        $asistenciaQuery = Asistencia::where('matricula_id', $matricula->id);
        $asistencia = [
            'presente' => (clone $asistenciaQuery)->where('estado', 'presente')->count(),
            'falta' => (clone $asistenciaQuery)->where('estado', 'falta')->count(),
            'tardanza' => (clone $asistenciaQuery)->where('estado', 'tardanza')->count(),
            'justificada' => (clone $asistenciaQuery)->where('estado', 'justificada')->count(),
        ];

        $pdf = Pdf::loadView('pdf.boleta', compact(
            'matricula', 'alumno', 'notas', 'colegio', 'bimestres', 'cursos', 'asistencia'
        ));

        $nombre = "boleta_{$alumno->user->apellidos}_{$matricula->periodo->nombre}.pdf";

        return $pdf->download($nombre);
    }
}
