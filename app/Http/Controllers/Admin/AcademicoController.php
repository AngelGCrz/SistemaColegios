<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use App\Models\CursoSeccion;
use App\Models\Docente;
use App\Models\Grado;
use App\Models\Nivel;
use App\Models\Seccion;
use App\Traits\FiltraPorColegio;
use Illuminate\Http\Request;

class AcademicoController extends Controller
{
    use FiltraPorColegio;

    // --- Niveles ---

    public function niveles()
    {
        $niveles = Nivel::where('colegio_id', $this->colegioId())
            ->with('grados')
            ->orderBy('orden')
            ->get();

        return view('admin.academico.niveles', compact('niveles'));
    }

    public function storeNivel(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:50'],
            'orden' => ['required', 'integer', 'min:0'],
        ]);

        Nivel::create(['colegio_id' => $this->colegioId(), ...$data]);

        return back()->with('success', 'Nivel creado.');
    }

    // --- Grados ---

    public function storeGrado(Request $request)
    {
        $data = $request->validate([
            'nivel_id' => ['required', 'exists:niveles,id'],
            'nombre' => ['required', 'string', 'max:50'],
            'orden' => ['required', 'integer', 'min:0'],
        ]);

        // Verificar que el nivel pertenece al colegio
        $nivel = Nivel::where('id', $data['nivel_id'])
            ->where('colegio_id', $this->colegioId())
            ->firstOrFail();

        Grado::create(['colegio_id' => $this->colegioId(), ...$data]);

        return back()->with('success', 'Grado creado.');
    }

    // --- Secciones ---

    public function secciones(Request $request)
    {
        $periodoId = $request->periodo_id;

        $secciones = Seccion::where('colegio_id', $this->colegioId())
            ->when($periodoId, fn ($q) => $q->where('periodo_id', $periodoId))
            ->with(['grado.nivel', 'periodo'])
            ->get();

        $grados = Grado::where('colegio_id', $this->colegioId())
            ->with('nivel')
            ->get();

        return view('admin.academico.secciones', compact('secciones', 'grados'));
    }

    public function storeSeccion(Request $request)
    {
        $data = $request->validate([
            'grado_id' => ['required', 'exists:grados,id'],
            'periodo_id' => ['required', 'exists:periodos,id'],
            'nombre' => ['required', 'string', 'max:10'],
            'capacidad' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        Seccion::create(['colegio_id' => $this->colegioId(), ...$data]);

        return back()->with('success', 'Sección creada.');
    }

    // --- Cursos ---

    public function cursos()
    {
        $cursos = Curso::where('colegio_id', $this->colegioId())
            ->orderBy('nombre')
            ->get();

        return view('admin.academico.cursos', compact('cursos'));
    }

    public function storeCurso(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'codigo' => ['nullable', 'string', 'max:20'],
            'descripcion' => ['nullable', 'string', 'max:500'],
        ]);

        Curso::create(['colegio_id' => $this->colegioId(), ...$data]);

        return back()->with('success', 'Curso creado.');
    }

    // --- Asignación curso-sección-docente ---

    public function asignaciones()
    {
        $asignaciones = CursoSeccion::where('colegio_id', $this->colegioId())
            ->with(['curso', 'seccion.grado.nivel', 'docente.user'])
            ->get();

        $cursos = Curso::where('colegio_id', $this->colegioId())->where('activo', true)->get();
        $secciones = Seccion::where('colegio_id', $this->colegioId())->with('grado.nivel')->get();
        $docentes = Docente::where('colegio_id', $this->colegioId())->with('user')->get();

        return view('admin.academico.asignaciones', compact('asignaciones', 'cursos', 'secciones', 'docentes'));
    }

    public function storeAsignacion(Request $request)
    {
        $data = $request->validate([
            'curso_id' => ['required', 'exists:cursos,id'],
            'seccion_id' => ['required', 'exists:secciones,id'],
            'docente_id' => ['required', 'exists:docentes,id'],
        ]);

        // Verificar que no exista la misma asignación
        $exists = CursoSeccion::where('curso_id', $data['curso_id'])
            ->where('seccion_id', $data['seccion_id'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Esta asignación ya existe.');
        }

        CursoSeccion::create(['colegio_id' => $this->colegioId(), ...$data]);

        return back()->with('success', 'Asignación creada.');
    }
}
