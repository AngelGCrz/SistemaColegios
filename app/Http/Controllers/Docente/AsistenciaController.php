<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use App\Models\CursoSeccion;
use App\Models\Matricula;
use App\Traits\FiltraPorColegio;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AsistenciaController extends Controller
{
    use FiltraPorColegio;

    public function seleccionar()
    {
        $docente = auth()->user()->docente;

        $cursoSecciones = CursoSeccion::where('docente_id', $docente->id)
            ->with(['curso', 'seccion.grado.nivel'])
            ->get();

        // Obtener secciones únicas del docente
        $secciones = $cursoSecciones->pluck('seccion')->unique('id');

        return view('docente.asistencia.seleccionar', compact('secciones'));
    }

    public function registrar(Request $request)
    {
        if ($request->isMethod('get')) {
            return redirect()->route('docente.asistencia.seleccionar');
        }

        $request->validate([
            'seccion_id' => ['required', 'exists:secciones,id'],
            'fecha' => ['required', 'date'],
        ]);

        $seccionId = $request->seccion_id;
        $fecha = Carbon::parse($request->fecha);

        $matriculas = Matricula::where('seccion_id', $seccionId)
            ->where('colegio_id', $this->colegioId())
            ->where('estado', 'activa')
            ->with('alumno.user')
            ->get()
            ->sortBy('alumno.user.apellidos');

        $asistenciasExistentes = Asistencia::where('seccion_id', $seccionId)
            ->where('fecha', $fecha->toDateString())
            ->pluck('estado', 'matricula_id');

        return view('docente.asistencia.registrar', compact(
            'matriculas', 'seccionId', 'fecha', 'asistenciasExistentes'
        ));
    }

    public function guardar(Request $request)
    {
        $data = $request->validate([
            'seccion_id' => ['required', 'integer', 'exists:secciones,id'],
            'fecha' => ['required', 'date'],
            'asistencias' => ['required', 'array'],
            'asistencias.*.matricula_id' => ['required', 'integer', 'exists:matriculas,id'],
            'asistencias.*.estado' => ['required', 'in:presente,falta,tardanza,justificada'],
        ]);

        DB::transaction(function () use ($data) {
            foreach ($data['asistencias'] as $item) {
                Asistencia::updateOrCreate(
                    [
                        'matricula_id' => $item['matricula_id'],
                        'fecha' => $data['fecha'],
                    ],
                    [
                        'colegio_id' => $this->colegioId(),
                        'seccion_id' => $data['seccion_id'],
                        'estado' => $item['estado'],
                    ]
                );
            }
        });

        return back()->with('success', 'Asistencia guardada exitosamente.');
    }
}
