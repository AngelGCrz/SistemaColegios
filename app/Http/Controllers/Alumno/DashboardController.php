<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use App\Models\Aviso;
use App\Models\EntregaTarea;
use App\Models\Nota;
use App\Models\Tarea;
use App\Models\CursoSeccion;
use App\Models\Bimestre;
use App\Traits\FiltraPorColegio;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    use FiltraPorColegio;

    public function index()
    {
        $alumno = auth()->user()->alumno;
        $matricula = $alumno->matriculaActiva();
        $matricula?->load('seccion.grado.nivel', 'periodo');

        $avisos = Aviso::where('colegio_id', $this->colegioId())
            ->where('publicado', true)
            ->whereIn('destinatario', ['todos', 'alumnos'])
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $tareasRecientes = collect();
        if ($matricula) {
            $seccionId = $matricula->seccion_id;
            $tareasRecientes = Tarea::where('colegio_id', $this->colegioId())
                ->where('publicada', true)
                ->whereHas('cursoSeccion', fn ($q) => $q->where('seccion_id', $seccionId))
                ->with('cursoSeccion.curso')
                ->orderByDesc('created_at')
                ->take(5)
                ->get();
        }

        return view('alumno.dashboard', compact('alumno', 'matricula', 'avisos', 'tareasRecientes'));
    }

    public function notas()
    {
        $alumno = auth()->user()->alumno;
        $matricula = $alumno->matriculaActiva();
        $matricula?->load('seccion.grado.nivel', 'periodo');

        if (!$matricula) {
            return view('alumno.notas', [
                'notas' => collect(),
                'matricula' => null,
                'cursos' => collect(),
                'bimestres' => collect(),
            ]);
        }

        $notas = Nota::where('matricula_id', $matricula->id)
            ->with(['cursoSeccion.curso', 'bimestre'])
            ->get();

        $cursos = CursoSeccion::where('seccion_id', $matricula->seccion_id)
            ->with(['curso', 'docente.user'])
            ->get();

        $bimestres = Bimestre::where('periodo_id', $matricula->periodo_id)
            ->orderBy('numero')
            ->get();

        return view('alumno.notas', compact('notas', 'matricula', 'cursos', 'bimestres'));
    }

    public function tareas()
    {
        $alumno = auth()->user()->alumno;
        $matricula = $alumno->matriculaActiva();

        $tareas = collect();
        if ($matricula) {
            $tareas = Tarea::where('colegio_id', $this->colegioId())
                ->where('publicada', true)
                ->whereHas('cursoSeccion', fn ($q) => $q->where('seccion_id', $matricula->seccion_id))
                ->with(['cursoSeccion.curso', 'entregas' => fn ($q) => $q->where('alumno_id', $alumno->id)])
                ->orderByDesc('fecha_limite')
                ->get();

            // Alias for easy access in view
            $tareas->each(function ($tarea) {
                $tarea->mi_entrega = $tarea->entregas->first();
            });
        }

        return view('alumno.tareas', compact('tareas'));
    }

    public function entregarTarea(Request $request, Tarea $tarea)
    {
        $alumno = auth()->user()->alumno;

        // Verificar que la tarea pertenece a su sección
        $matricula = $alumno->matriculaActiva();
        abort_if(!$matricula || $tarea->cursoSeccion->seccion_id !== $matricula->seccion_id, 403);

        $data = $request->validate([
            'contenido' => ['nullable', 'string', 'max:5000'],
            'archivo' => ['nullable', 'file', 'max:10240'],
        ]);

        $archivo = null;
        if ($request->hasFile('archivo')) {
            $archivo = $request->file('archivo')
                ->store('entregas/' . $this->colegioId(), 'local');
        }

        EntregaTarea::updateOrCreate(
            ['tarea_id' => $tarea->id, 'alumno_id' => $alumno->id],
            [
                'colegio_id' => $this->colegioId(),
                'contenido' => $data['contenido'],
                'archivo' => $archivo,
                'fecha_entrega' => now(),
            ]
        );

        return back()->with('success', 'Tarea entregada exitosamente.');
    }

    public function calendario(Request $request)
    {
        $alumno = auth()->user()->alumno;
        $matricula = $alumno->matriculaActiva();

        $mes = $request->integer('mes', now()->month);
        $anio = $request->integer('anio', now()->year);
        $fecha = Carbon::createFromDate($anio, $mes, 1);

        $tareas = collect();
        if ($matricula) {
            $tareas = Tarea::where('colegio_id', $this->colegioId())
                ->where('publicada', true)
                ->whereNotNull('fecha_limite')
                ->whereMonth('fecha_limite', $mes)
                ->whereYear('fecha_limite', $anio)
                ->whereHas('cursoSeccion', fn ($q) => $q->where('seccion_id', $matricula->seccion_id))
                ->with(['cursoSeccion.curso', 'entregas' => fn ($q) => $q->where('alumno_id', $alumno->id)])
                ->orderBy('fecha_limite')
                ->get();
        }

        // Group by day
        $tareasPorDia = $tareas->groupBy(fn ($t) => $t->fecha_limite->day);

        return view('alumno.calendario', compact('fecha', 'tareasPorDia', 'mes', 'anio'));
    }

    public function historial()
    {
        $alumno = auth()->user()->alumno;

        $entregas = EntregaTarea::where('alumno_id', $alumno->id)
            ->where('colegio_id', $this->colegioId())
            ->with(['tarea.cursoSeccion.curso'])
            ->orderByDesc('fecha_entrega')
            ->paginate(20);

        return view('alumno.historial', compact('entregas'));
    }
}
