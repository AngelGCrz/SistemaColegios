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
use App\Traits\FiltraPorColegio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PadreApiController extends Controller
{
    use FiltraPorColegio;

    public function hijos(Request $request): JsonResponse
    {
        $padre = $request->user()->padre;
        if (!$padre) {
            return response()->json(['message' => 'No es padre.'], 403);
        }

        $hijos = $padre->alumnos()
            ->with(['user', 'matriculas' => fn ($q) => $q->where('estado', 'activa')->with('seccion.grado.nivel', 'periodo')])
            ->get()
            ->map(fn ($a) => [
                'id' => $a->id,
                'nombre' => $a->user->nombreCompleto(),
                'codigo' => $a->codigo_alumno,
                'matricula_activa' => $a->matriculas->first() ? [
                    'seccion' => $a->matriculas->first()->seccion->nombre ?? '',
                    'grado' => $a->matriculas->first()->seccion->grado->nombre ?? '',
                    'periodo' => $a->matriculas->first()->periodo->nombre ?? '',
                ] : null,
            ]);

        return response()->json(['hijos' => $hijos]);
    }

    public function notasHijo(Request $request, int $alumnoId): JsonResponse
    {
        $padre = $request->user()->padre;
        $alumno = $padre->alumnos()->where('alumnos.id', $alumnoId)->first();

        if (!$alumno) {
            return response()->json(['message' => 'Alumno no encontrado.'], 404);
        }

        $matricula = $alumno->matriculaActiva();
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
                'nota' => $n->nota,
                'nota_letra' => $n->nota_letra,
            ]);

        $cursos = CursoSeccion::where('seccion_id', $matricula->seccion_id)
            ->with('curso')
            ->get()
            ->map(fn ($cs) => ['id' => $cs->id, 'curso' => $cs->curso->nombre]);

        $bimestres = Bimestre::where('periodo_id', $matricula->periodo_id)
            ->orderBy('numero')->get()
            ->map(fn ($b) => ['id' => $b->id, 'nombre' => $b->nombre]);

        return response()->json(compact('notas', 'cursos', 'bimestres'));
    }

    public function asistenciaHijo(Request $request, int $alumnoId): JsonResponse
    {
        $padre = $request->user()->padre;
        $alumno = $padre->alumnos()->where('alumnos.id', $alumnoId)->first();

        if (!$alumno) {
            return response()->json(['message' => 'Alumno no encontrado.'], 404);
        }

        $matricula = $alumno->matriculaActiva();
        if (!$matricula) {
            return response()->json(['asistencias' => []]);
        }

        $asistencias = Asistencia::where('matricula_id', $matricula->id)
            ->orderByDesc('fecha')
            ->take(50)
            ->get()
            ->map(fn ($a) => [
                'fecha' => $a->fecha->toDateString(),
                'estado' => $a->estado,
            ]);

        return response()->json(['asistencias' => $asistencias]);
    }

    public function pagosHijo(Request $request, int $alumnoId): JsonResponse
    {
        $padre = $request->user()->padre;
        $alumno = $padre->alumnos()->where('alumnos.id', $alumnoId)->first();

        if (!$alumno) {
            return response()->json(['message' => 'Alumno no encontrado.'], 404);
        }

        $pagos = Pago::where('alumno_id', $alumno->id)
            ->where('colegio_id', $this->colegioId())
            ->with(['conceptoPago', 'periodo'])
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($p) => [
                'concepto' => $p->conceptoPago->nombre ?? 'N/A',
                'monto' => $p->monto,
                'estado' => $p->estado,
                'fecha_pago' => $p->fecha_pago?->toDateString(),
            ]);

        return response()->json(['pagos' => $pagos]);
    }
}
