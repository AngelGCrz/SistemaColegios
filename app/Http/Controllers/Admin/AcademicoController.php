<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use App\Models\CursoSeccion;
use App\Models\Docente;
use App\Models\Grado;
use App\Models\Nivel;
use App\Models\Periodo;
use App\Models\Seccion;
use App\Traits\FiltraPorColegio;
use Illuminate\Http\Request;

class AcademicoController extends Controller
{
    use FiltraPorColegio;

    private function allVarsForIndex(string $activeTab = 'niveles'): array
    {
        $colegioId = $this->colegioId();

        $niveles = Nivel::where('colegio_id', $colegioId)
            ->with('grados')
            ->orderBy('orden')
            ->get();

        $secciones = Seccion::where('colegio_id', $colegioId)
            ->with(['grado.nivel', 'periodo'])
            ->withCount('matriculas')
            ->get();

        $cursos = Curso::where('colegio_id', $colegioId)
            ->orderBy('nombre')
            ->get();

        $asignaciones = CursoSeccion::where('colegio_id', $colegioId)
            ->with(['curso', 'seccion.grado.nivel', 'docente.user'])
            ->get();

        $docentes = Docente::where('colegio_id', $colegioId)->with('user')->get();

        $periodos = Periodo::where('colegio_id', $colegioId)
            ->orderByDesc('anio')
            ->get();

        return compact('niveles', 'secciones', 'cursos', 'asignaciones', 'docentes', 'periodos', 'activeTab');
    }

    // --- Niveles ---

    public function niveles()
    {
        return view('admin.academico.index', $this->allVarsForIndex('niveles'));
    }

    public function storeNivel(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:50'],
            'orden' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['orden'] = $data['orden'] ?? Nivel::where('colegio_id', $this->colegioId())->max('orden') + 1;

        Nivel::create(['colegio_id' => $this->colegioId(), ...$data]);

        return back()->with('success', 'Nivel creado.');
    }

    // --- Grados ---

    public function storeGrado(Request $request)
    {
        $data = $request->validate([
            'nivel_id' => ['required', 'exists:niveles,id'],
            'nombre' => ['required', 'string', 'max:50'],
            'orden' => ['nullable', 'integer', 'min:0'],
        ]);

        // Verificar que el nivel pertenece al colegio
        $nivel = Nivel::where('id', $data['nivel_id'])
            ->where('colegio_id', $this->colegioId())
            ->firstOrFail();

        $data['orden'] = $data['orden'] ?? Grado::where('nivel_id', $data['nivel_id'])->max('orden') + 1;

        Grado::create(['colegio_id' => $this->colegioId(), ...$data]);

        return back()->with('success', 'Grado creado.');
    }

    // --- Secciones ---

    public function secciones(Request $request)
    {
        return view('admin.academico.index', $this->allVarsForIndex('secciones'));
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
        return view('admin.academico.index', $this->allVarsForIndex('cursos'));
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
        return view('admin.academico.index', $this->allVarsForIndex('asignaciones'));
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

    public function destroyAsignacion(CursoSeccion $cursoSeccion)
    {
        abort_if($cursoSeccion->colegio_id !== $this->colegioId(), 403);

        $cursoSeccion->delete();

        return back()->with('success', 'Asignación eliminada.');
    }
}
