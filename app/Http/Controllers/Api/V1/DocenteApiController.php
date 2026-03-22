<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CursoSeccion;
use App\Models\Tarea;
use App\Traits\FiltraPorColegio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocenteApiController extends Controller
{
    use FiltraPorColegio;

    public function misCursos(Request $request): JsonResponse
    {
        $docente = $request->user()->docente;
        if (!$docente) {
            return response()->json(['message' => 'No es docente.'], 403);
        }

        $cursos = CursoSeccion::where('docente_id', $docente->id)
            ->where('colegio_id', $this->colegioId())
            ->with(['curso', 'seccion.grado.nivel'])
            ->get()
            ->map(fn ($cs) => [
                'id' => $cs->id,
                'curso' => $cs->curso->nombre,
                'seccion' => $cs->seccion->nombre ?? '',
                'grado' => $cs->seccion->grado->nombre ?? '',
                'nivel' => $cs->seccion->grado->nivel->nombre ?? '',
            ]);

        return response()->json(['cursos' => $cursos]);
    }

    public function tareas(Request $request, CursoSeccion $cursoSeccion): JsonResponse
    {
        $docente = $request->user()->docente;

        if ($cursoSeccion->docente_id !== $docente->id) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $tareas = $cursoSeccion->tareas()
            ->withCount('entregas')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($t) => [
                'id' => $t->id,
                'titulo' => $t->titulo,
                'fecha_limite' => $t->fecha_limite?->toIso8601String(),
                'publicada' => $t->publicada,
                'puntaje_maximo' => $t->puntaje_maximo,
                'entregas_count' => $t->entregas_count,
            ]);

        return response()->json(['tareas' => $tareas]);
    }
}
