<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\CursoSeccion;
use App\Models\EntregaTarea;
use App\Models\Tarea;
use App\Traits\FiltraPorColegio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TareaController extends Controller
{
    use FiltraPorColegio;

    public function index(CursoSeccion $cursoSeccion)
    {
        $this->autorizarDocente($cursoSeccion);

        $tareas = Tarea::where('curso_seccion_id', $cursoSeccion->id)
            ->withCount('entregas')
            ->orderByDesc('created_at')
            ->get();

        return view('docente.tareas.index', compact('cursoSeccion', 'tareas'));
    }

    public function create(CursoSeccion $cursoSeccion)
    {
        $this->autorizarDocente($cursoSeccion);

        return view('docente.tareas.create', compact('cursoSeccion'));
    }

    public function store(Request $request, CursoSeccion $cursoSeccion)
    {
        $this->autorizarDocente($cursoSeccion);

        $data = $request->validate([
            'titulo' => ['required', 'string', 'max:200'],
            'descripcion' => ['nullable', 'string', 'max:5000'],
            'fecha_limite' => ['nullable', 'date'],
            'puntaje_maximo' => ['required', 'numeric', 'min:1', 'max:100'],
            'archivo_adjunto' => ['nullable', 'file', 'max:10240'],
        ]);

        $archivo = null;
        if ($request->hasFile('archivo_adjunto')) {
            $archivo = $request->file('archivo_adjunto')
                ->store('tareas/' . $this->colegioId(), 'local');
        }

        Tarea::create([
            'colegio_id' => $this->colegioId(),
            'curso_seccion_id' => $cursoSeccion->id,
            'titulo' => $data['titulo'],
            'descripcion' => $data['descripcion'] ?? null,
            'fecha_limite' => $data['fecha_limite'] ?? null,
            'puntaje_maximo' => $data['puntaje_maximo'],
            'archivo_adjunto' => $archivo,
            'publicada' => $request->boolean('publicada'),
        ]);

        return redirect()->route('docente.tareas.index', $cursoSeccion)
            ->with('success', 'Tarea creada exitosamente.');
    }

    public function edit(CursoSeccion $cursoSeccion, Tarea $tarea)
    {
        $this->autorizarDocente($cursoSeccion);
        abort_if($tarea->curso_seccion_id !== $cursoSeccion->id, 404);

        return view('docente.tareas.edit', compact('cursoSeccion', 'tarea'));
    }

    public function update(Request $request, CursoSeccion $cursoSeccion, Tarea $tarea)
    {
        $this->autorizarDocente($cursoSeccion);
        abort_if($tarea->curso_seccion_id !== $cursoSeccion->id, 404);

        $data = $request->validate([
            'titulo' => ['required', 'string', 'max:200'],
            'descripcion' => ['nullable', 'string', 'max:5000'],
            'fecha_limite' => ['nullable', 'date'],
            'puntaje_maximo' => ['required', 'numeric', 'min:1', 'max:100'],
            'archivo_adjunto' => ['nullable', 'file', 'max:10240'],
        ]);

        if ($request->hasFile('archivo_adjunto')) {
            if ($tarea->archivo_adjunto) {
                Storage::disk('local')->delete($tarea->archivo_adjunto);
            }
            $data['archivo_adjunto'] = $request->file('archivo_adjunto')
                ->store('tareas/' . $this->colegioId(), 'local');
        } else {
            unset($data['archivo_adjunto']);
        }

        $data['publicada'] = $request->boolean('publicada');
        $tarea->update($data);

        return redirect()->route('docente.tareas.index', $cursoSeccion)
            ->with('success', 'Tarea actualizada.');
    }

    public function destroy(CursoSeccion $cursoSeccion, Tarea $tarea)
    {
        $this->autorizarDocente($cursoSeccion);
        abort_if($tarea->curso_seccion_id !== $cursoSeccion->id, 404);

        if ($tarea->archivo_adjunto) {
            Storage::disk('local')->delete($tarea->archivo_adjunto);
        }

        $tarea->delete();

        return redirect()->route('docente.tareas.index', $cursoSeccion)
            ->with('success', 'Tarea eliminada.');
    }

    public function togglePublicada(CursoSeccion $cursoSeccion, Tarea $tarea)
    {
        $this->autorizarDocente($cursoSeccion);
        abort_if($tarea->curso_seccion_id !== $cursoSeccion->id, 404);

        $tarea->update(['publicada' => !$tarea->publicada]);

        return back()->with('success', $tarea->publicada ? 'Tarea publicada.' : 'Tarea despublicada.');
    }

    public function entregas(Tarea $tarea)
    {
        $this->autorizarDocente($tarea->cursoSeccion);

        $entregas = EntregaTarea::where('tarea_id', $tarea->id)
            ->with('alumno.user')
            ->orderBy('fecha_entrega')
            ->get();

        return view('docente.tareas.entregas', compact('tarea', 'entregas'));
    }

    public function calificarTodas(Request $request, Tarea $tarea)
    {
        $this->autorizarDocente($tarea->cursoSeccion);

        $request->validate([
            'entregas' => ['required', 'array'],
            'entregas.*.calificacion' => ['nullable', 'numeric', 'min:0', 'max:' . $tarea->puntaje_maximo],
            'entregas.*.comentario' => ['nullable', 'string', 'max:1000'],
        ]);

        foreach ($request->input('entregas', []) as $id => $entregaData) {
            $entrega = EntregaTarea::where('id', $id)
                ->where('tarea_id', $tarea->id)
                ->first();

            if ($entrega && isset($entregaData['calificacion']) && $entregaData['calificacion'] !== '') {
                $entrega->update([
                    'calificacion' => $entregaData['calificacion'],
                    'comentario_docente' => $entregaData['comentario'] ?? $entrega->comentario_docente,
                ]);
            }
        }

        return back()->with('success', 'Calificaciones guardadas.');
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
}
