<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ExcelExporter;
use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\Asistencia;
use App\Models\Matricula;
use App\Models\Nota;
use App\Models\Pago;
use App\Models\Periodo;
use App\Traits\FiltraPorColegio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExportController extends Controller
{
    use FiltraPorColegio;

    public function index()
    {
        $colegioId = $this->colegioId();
        $periodos = Periodo::where('colegio_id', $colegioId)->orderByDesc('anio')->get();
        $periodoActivo = $periodos->firstWhere('activo', true);

        return view('admin.exportar.index', compact('periodos', 'periodoActivo'));
    }

    public function alumnos(Request $request)
    {
        $colegioId = $this->colegioId();

        $alumnos = Alumno::where('colegio_id', $colegioId)
            ->with('user')
            ->get()
            ->map(fn ($a) => [
                $a->codigo_alumno,
                $a->user->apellidos ?? '',
                $a->user->nombre ?? '',
                $a->user->email ?? '',
                $a->user->dni ?? '',
                $a->user->telefono ?? '',
                $a->fecha_nacimiento?->format('Y-m-d') ?? '',
                $a->genero ?? '',
                $a->direccion ?? '',
            ]);

        return ExcelExporter::download(
            'alumnos_' . now()->format('Ymd') . '.xlsx',
            ['Código', 'Apellidos', 'Nombre', 'Email', 'DNI', 'Teléfono', 'Fecha Nac.', 'Género', 'Dirección'],
            $alumnos
        );
    }

    public function notas(Request $request)
    {
        $colegioId = $this->colegioId();
        $periodoId = $request->input('periodo_id')
            ?? Periodo::where('colegio_id', $colegioId)->where('activo', true)->value('id');

        $notas = DB::table('notas')
            ->join('matriculas', 'notas.matricula_id', '=', 'matriculas.id')
            ->join('alumnos', 'matriculas.alumno_id', '=', 'alumnos.id')
            ->join('users', 'alumnos.user_id', '=', 'users.id')
            ->join('curso_seccion', 'notas.curso_seccion_id', '=', 'curso_seccion.id')
            ->join('cursos', 'curso_seccion.curso_id', '=', 'cursos.id')
            ->join('bimestres', 'notas.bimestre_id', '=', 'bimestres.id')
            ->where('notas.colegio_id', $colegioId)
            ->when($periodoId, fn ($q) => $q->where('matriculas.periodo_id', $periodoId))
            ->select(
                'alumnos.codigo_alumno',
                'users.apellidos',
                'users.nombre',
                'cursos.nombre as curso',
                'bimestres.nombre as bimestre',
                'notas.nota',
                'notas.nota_letra'
            )
            ->orderBy('users.apellidos')
            ->orderBy('cursos.nombre')
            ->orderBy('bimestres.nombre')
            ->get()
            ->map(fn ($r) => [
                $r->codigo_alumno,
                $r->apellidos,
                $r->nombre,
                $r->curso,
                $r->bimestre,
                $r->nota,
                $r->nota_letra,
            ]);

        return ExcelExporter::download(
            'notas_' . now()->format('Ymd') . '.xlsx',
            ['Código', 'Apellidos', 'Nombre', 'Curso', 'Bimestre', 'Nota', 'Letra'],
            $notas
        );
    }

    public function asistencia(Request $request)
    {
        $colegioId = $this->colegioId();

        $registros = DB::table('asistencias')
            ->join('matriculas', 'asistencias.matricula_id', '=', 'matriculas.id')
            ->join('alumnos', 'matriculas.alumno_id', '=', 'alumnos.id')
            ->join('users', 'alumnos.user_id', '=', 'users.id')
            ->join('secciones', 'asistencias.seccion_id', '=', 'secciones.id')
            ->where('asistencias.colegio_id', $colegioId)
            ->when($request->fecha_desde, fn ($q, $v) => $q->where('asistencias.fecha', '>=', $v))
            ->when($request->fecha_hasta, fn ($q, $v) => $q->where('asistencias.fecha', '<=', $v))
            ->select(
                'asistencias.fecha',
                'alumnos.codigo_alumno',
                'users.apellidos',
                'users.nombre',
                'secciones.nombre as seccion',
                'asistencias.estado',
                'asistencias.observacion'
            )
            ->orderBy('asistencias.fecha', 'desc')
            ->orderBy('users.apellidos')
            ->get()
            ->map(fn ($r) => [
                $r->fecha,
                $r->codigo_alumno,
                $r->apellidos,
                $r->nombre,
                $r->seccion,
                $r->estado,
                $r->observacion ?? '',
            ]);

        return ExcelExporter::download(
            'asistencia_' . now()->format('Ymd') . '.xlsx',
            ['Fecha', 'Código', 'Apellidos', 'Nombre', 'Sección', 'Estado', 'Observación'],
            $registros
        );
    }

    public function pagos(Request $request)
    {
        $colegioId = $this->colegioId();

        $registros = DB::table('pagos')
            ->join('alumnos', 'pagos.alumno_id', '=', 'alumnos.id')
            ->join('users', 'alumnos.user_id', '=', 'users.id')
            ->leftJoin('conceptos_pago', 'pagos.concepto_pago_id', '=', 'conceptos_pago.id')
            ->where('pagos.colegio_id', $colegioId)
            ->when($request->estado, fn ($q, $v) => $q->where('pagos.estado', $v))
            ->select(
                'alumnos.codigo_alumno',
                'users.apellidos',
                'users.nombre',
                'conceptos_pago.nombre as concepto',
                'pagos.monto',
                'pagos.estado',
                'pagos.fecha_pago',
                'pagos.metodo_pago',
                'pagos.numero_recibo'
            )
            ->orderBy('pagos.created_at', 'desc')
            ->get()
            ->map(fn ($r) => [
                $r->codigo_alumno,
                $r->apellidos,
                $r->nombre,
                $r->concepto ?? '',
                $r->monto,
                $r->estado,
                $r->fecha_pago ?? '',
                $r->metodo_pago ?? '',
                $r->numero_recibo ?? '',
            ]);

        return ExcelExporter::download(
            'pagos_' . now()->format('Ymd') . '.xlsx',
            ['Código', 'Apellidos', 'Nombre', 'Concepto', 'Monto', 'Estado', 'Fecha Pago', 'Método', 'Recibo'],
            $registros
        );
    }
}
