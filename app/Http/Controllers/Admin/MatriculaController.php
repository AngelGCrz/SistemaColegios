<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\Matricula;
use App\Models\Periodo;
use App\Models\Seccion;
use App\Traits\FiltraPorColegio;
use Illuminate\Http\Request;

class MatriculaController extends Controller
{
    use FiltraPorColegio;

    public function index(Request $request)
    {
        $periodoId = $request->periodo_id;

        $matriculas = Matricula::where('colegio_id', $this->colegioId())
            ->when($periodoId, fn ($q) => $q->where('periodo_id', $periodoId))
            ->with(['alumno.user', 'seccion.grado.nivel', 'periodo'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $periodos = Periodo::where('colegio_id', $this->colegioId())
            ->orderByDesc('anio')
            ->get();

        return view('admin.matriculas.index', compact('matriculas', 'periodos'));
    }

    public function create()
    {
        $alumnos = Alumno::where('colegio_id', $this->colegioId())
            ->with('user')
            ->get();

        $secciones = Seccion::where('colegio_id', $this->colegioId())
            ->with('grado.nivel')
            ->get();

        $periodos = Periodo::where('colegio_id', $this->colegioId())
            ->where('activo', true)
            ->get();

        return view('admin.matriculas.create', compact('alumnos', 'secciones', 'periodos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'alumno_id' => ['required', 'exists:alumnos,id'],
            'seccion_id' => ['required', 'exists:secciones,id'],
            'periodo_id' => ['required', 'exists:periodos,id'],
            'fecha_matricula' => ['required', 'date'],
        ]);

        // Verificar que no exista matrícula activa del alumno en el mismo periodo
        $exists = Matricula::where('alumno_id', $data['alumno_id'])
            ->where('periodo_id', $data['periodo_id'])
            ->where('estado', 'activa')
            ->exists();

        if ($exists) {
            return back()->with('error', 'El alumno ya tiene una matrícula activa en este periodo.');
        }

        Matricula::create([
            'colegio_id' => $this->colegioId(),
            'estado' => 'activa',
            ...$data,
        ]);

        return redirect()->route('admin.matriculas.index')
            ->with('success', 'Matrícula registrada exitosamente.');
    }

    public function updateEstado(Request $request, Matricula $matricula)
    {
        abort_if($matricula->colegio_id !== $this->colegioId(), 403);

        $data = $request->validate([
            'estado' => ['required', 'in:activa,retirada,trasladada'],
        ]);

        $matricula->update($data);

        return back()->with('success', 'Estado de matrícula actualizado.');
    }
}
