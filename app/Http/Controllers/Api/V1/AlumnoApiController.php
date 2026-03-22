<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\Asistencia;
use App\Models\Bimestre;
use App\Models\CursoSeccion;
use App\Models\Matricula;
use App\Models\Nota;
use App\Models\Pago;
use App\Models\Periodo;
use App\Models\Tarea;
use App\Traits\FiltraPorColegio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AlumnoApiController extends Controller
{
    use FiltraPorColegio;

    public function dashboard(Request $request): JsonResponse
    {
        $alumno = $request->user()->alumno;
        if (!$alumno) {
            return response()->json(['message' => 'No es alumno.'], 403);
        }

        $matricula = $alumno->matriculaActiva();

        $tareasRecientes = collect();
        if ($matricula) {
            $tareasRecientes = Tarea::where('colegio_id', $this->colegioId())
                ->where('publicada', true)
                ->whereHas('cursoSeccion', fn ($q) => $q->where('seccion_id', $matricula->seccion_id))
                ->with('cursoSeccion.curso')
                ->orderByDesc('created_at')
                ->take(5)
                ->get()
                ->map(fn ($t) => [
                    'id' => $t->id,
                    'titulo' => $t->titulo,
                    'curso' => $t->cursoSeccion->curso->nombre,
                    'fecha_limite' => $t->fecha_limite?->toIso8601String(),
                ]);
        }

        return response()->json([
            'alumno' => [
                'id' => $alumno->id,
                'codigo' => $alumno->codigo_alumno,
                'nombre' => $alumno->user->nombreCompleto(),
            ],
            'matricula' => $matricula ? [
                'id' => $matricula->id,
                'estado' => $matricula->estado,
                'periodo' => $matricula->periodo->nombre ?? null,
            ] : null,
            'tareas_recientes' => $tareasRecientes,
        ]);
    }

    public function notas(Request $request): JsonResponse
    {
        $alumno = $request->user()->alumno;
        $matricula = $alumno?->matriculaActiva();

        if (!$matricula) {
            return response()->json(['notas' => [], 'cursos' => [], 'bimestres' => []]);
        }

        $matricula->load('seccion.grado.nivel', 'periodo');

        $notas = Nota::where('matricula_id', $matricula->id)
            ->with(['cursoSeccion.curso', 'bimestre'])
            ->get()
            ->map(fn ($n) => [
                'curso_seccion_id' => $n->curso_seccion_id,
                'curso' => $n->cursoSeccion->curso->nombre,
                'bimestre_id' => $n->bimestre_id,
                'bimestre' => $n->bimestre->nombre,
                'nota' => $n->nota,
                'nota_letra' => $n->nota_letra,
            ]);

        $cursos = CursoSeccion::where('seccion_id', $matricula->seccion_id)
            ->with(['curso', 'docente.user'])
            ->get()
            ->map(fn ($cs) => [
                'id' => $cs->id,
                'curso' => $cs->curso->nombre,
                'docente' => $cs->docente?->user?->nombreCompleto(),
            ]);

        $bimestres = Bimestre::where('periodo_id', $matricula->periodo_id)
            ->orderBy('numero')
            ->get()
            ->map(fn ($b) => ['id' => $b->id, 'nombre' => $b->nombre, 'numero' => $b->numero]);

        return response()->json([
            'periodo' => $matricula->periodo->nombre,
            'seccion' => $matricula->seccion->nombre ?? '',
            'notas' => $notas,
            'cursos' => $cursos,
            'bimestres' => $bimestres,
        ]);
    }

    public function asistencia(Request $request): JsonResponse
    {
        $alumno = $request->user()->alumno;
        $matricula = $alumno?->matriculaActiva();

        if (!$matricula) {
            return response()->json(['asistencias' => [], 'resumen' => []]);
        }

        $asistencias = Asistencia::where('matricula_id', $matricula->id)
            ->orderByDesc('fecha')
            ->take(50)
            ->get()
            ->map(fn ($a) => [
                'fecha' => $a->fecha->toDateString(),
                'estado' => $a->estado,
                'observacion' => $a->observacion,
            ]);

        $resumen = [
            'presente' => Asistencia::where('matricula_id', $matricula->id)->where('estado', 'presente')->count(),
            'falta' => Asistencia::where('matricula_id', $matricula->id)->where('estado', 'falta')->count(),
            'tardanza' => Asistencia::where('matricula_id', $matricula->id)->where('estado', 'tardanza')->count(),
            'justificada' => Asistencia::where('matricula_id', $matricula->id)->where('estado', 'justificada')->count(),
        ];

        return response()->json([
            'asistencias' => $asistencias,
            'resumen' => $resumen,
        ]);
    }

    public function tareas(Request $request): JsonResponse
    {
        $alumno = $request->user()->alumno;
        $matricula = $alumno?->matriculaActiva();

        if (!$matricula) {
            return response()->json(['tareas' => []]);
        }

        $tareas = Tarea::where('colegio_id', $this->colegioId())
            ->where('publicada', true)
            ->whereHas('cursoSeccion', fn ($q) => $q->where('seccion_id', $matricula->seccion_id))
            ->with(['cursoSeccion.curso', 'entregas' => fn ($q) => $q->where('alumno_id', $alumno->id)])
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($t) => [
                'id' => $t->id,
                'titulo' => $t->titulo,
                'descripcion' => $t->descripcion,
                'curso' => $t->cursoSeccion->curso->nombre,
                'fecha_limite' => $t->fecha_limite?->toIso8601String(),
                'puntaje_maximo' => $t->puntaje_maximo,
                'entregada' => $t->entregas->isNotEmpty(),
                'calificacion' => $t->entregas->first()?->calificacion,
            ]);

        return response()->json(['tareas' => $tareas]);
    }

    public function pagos(Request $request): JsonResponse
    {
        $alumno = $request->user()->alumno;

        $pagos = Pago::where('alumno_id', $alumno->id)
            ->where('colegio_id', $this->colegioId())
            ->with(['conceptoPago', 'periodo'])
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'concepto' => $p->conceptoPago->nombre ?? 'N/A',
                'periodo' => $p->periodo->nombre ?? '',
                'monto' => $p->monto,
                'estado' => $p->estado,
                'fecha_pago' => $p->fecha_pago?->toDateString(),
            ]);

        $pendiente = $pagos->where('estado', 'pendiente')->sum('monto');
        $pagado = $pagos->where('estado', 'pagado')->sum('monto');

        return response()->json([
            'pagos' => $pagos->values(),
            'totales' => ['pendiente' => $pendiente, 'pagado' => $pagado],
        ]);
    }
}
