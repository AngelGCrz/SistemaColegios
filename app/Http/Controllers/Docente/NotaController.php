<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Bimestre;
use App\Models\CursoSeccion;
use App\Models\Matricula;
use App\Models\Nota;
use App\Traits\FiltraPorColegio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotaController extends Controller
{
    use FiltraPorColegio;

    /**
     * Muestra la planilla de notas para un curso-sección y bimestre.
     */
    public function planilla(CursoSeccion $cursoSeccion, Bimestre $bimestre)
    {
        $this->autorizarDocente($cursoSeccion);

        $matriculas = Matricula::where('seccion_id', $cursoSeccion->seccion_id)
            ->where('estado', 'activa')
            ->with('alumno.user')
            ->get()
            ->sortBy('alumno.user.apellidos');

        $notasExistentes = Nota::where('curso_seccion_id', $cursoSeccion->id)
            ->where('bimestre_id', $bimestre->id)
            ->pluck('nota', 'matricula_id');

        return view('docente.notas.planilla', compact(
            'cursoSeccion', 'bimestre', 'matriculas', 'notasExistentes'
        ));
    }

    /**
     * Guarda/actualiza notas masivamente (toda la planilla).
     */
    public function guardar(Request $request, CursoSeccion $cursoSeccion, Bimestre $bimestre)
    {
        $this->autorizarDocente($cursoSeccion);

        $data = $request->validate([
            'notas' => ['required', 'array'],
            'notas.*.matricula_id' => ['required', 'integer', 'exists:matriculas,id'],
            'notas.*.nota' => ['nullable', 'numeric', 'min:0', 'max:20'],
        ]);

        DB::transaction(function () use ($data, $cursoSeccion, $bimestre) {
            foreach ($data['notas'] as $item) {
                Nota::updateOrCreate(
                    [
                        'matricula_id' => $item['matricula_id'],
                        'curso_seccion_id' => $cursoSeccion->id,
                        'bimestre_id' => $bimestre->id,
                    ],
                    [
                        'colegio_id' => $this->colegioId(),
                        'nota' => $item['nota'],
                        'nota_letra' => $this->calcularNotaLetra($item['nota']),
                    ]
                );
            }
        });

        return back()->with('success', 'Notas guardadas exitosamente.');
    }

    /**
     * Seleccionar curso y bimestre antes de registrar notas.
     */
    public function seleccionar()
    {
        $docente = auth()->user()->docente;

        $cursoSecciones = CursoSeccion::where('docente_id', $docente->id)
            ->with(['curso', 'seccion.grado.nivel'])
            ->get();

        $bimestres = Bimestre::where('colegio_id', $this->colegioId())
            ->orderBy('numero')
            ->get();

        return view('docente.notas.seleccionar', compact('cursoSecciones', 'bimestres'));
    }

    private function autorizarDocente(CursoSeccion $cursoSeccion): void
    {
        $docente = auth()->user()->docente;
        abort_if(
            $cursoSeccion->docente_id !== $docente->id ||
            $cursoSeccion->colegio_id !== $this->colegioId(),
            403
        );
    }

    private function calcularNotaLetra(?float $nota): ?string
    {
        if ($nota === null) return null;
        if ($nota >= 18) return 'AD';
        if ($nota >= 14) return 'A';
        if ($nota >= 11) return 'B';
        return 'C';
    }
}
