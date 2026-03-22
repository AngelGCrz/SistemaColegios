<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Periodo;
use App\Traits\FiltraPorColegio;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteApiWebController extends Controller
{
    use FiltraPorColegio;

    public function notasPorCurso(Request $request): JsonResponse
    {
        $colegioId = $this->colegioId();
        $periodoId = $request->input('periodo_id')
            ?? Periodo::where('colegio_id', $colegioId)->where('activo', true)->value('id');

        if (!$periodoId) {
            return response()->json(['labels' => [], 'data' => []]);
        }

        $resultados = DB::table('notas')
            ->join('curso_seccion', 'notas.curso_seccion_id', '=', 'curso_seccion.id')
            ->join('cursos', 'curso_seccion.curso_id', '=', 'cursos.id')
            ->join('matriculas', 'notas.matricula_id', '=', 'matriculas.id')
            ->where('notas.colegio_id', $colegioId)
            ->where('matriculas.periodo_id', $periodoId)
            ->groupBy('cursos.nombre')
            ->select('cursos.nombre', DB::raw('ROUND(AVG(notas.nota), 2) as promedio'))
            ->orderBy('cursos.nombre')
            ->get();

        return response()->json([
            'labels' => $resultados->pluck('nombre'),
            'data' => $resultados->pluck('promedio'),
        ]);
    }

    public function asistenciaMensual(Request $request): JsonResponse
    {
        $colegioId = $this->colegioId();
        $anio = $request->input('anio', Carbon::now()->year);

        $resultados = DB::table('asistencias')
            ->where('colegio_id', $colegioId)
            ->whereYear('fecha', $anio)
            ->groupBy('mes', 'estado')
            ->select(
                DB::raw('MONTH(fecha) as mes'),
                'estado',
                DB::raw('COUNT(*) as total')
            )
            ->orderBy('mes')
            ->get();

        $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $estados = ['presente', 'falta', 'tardanza', 'justificada'];
        $datasets = [];

        foreach ($estados as $estado) {
            $data = array_fill(0, 12, 0);
            foreach ($resultados->where('estado', $estado) as $r) {
                $data[$r->mes - 1] = $r->total;
            }
            $datasets[$estado] = $data;
        }

        return response()->json([
            'labels' => $meses,
            'datasets' => $datasets,
        ]);
    }

    public function pagosMensual(Request $request): JsonResponse
    {
        $colegioId = $this->colegioId();
        $anio = $request->input('anio', Carbon::now()->year);

        $pagados = DB::table('pagos')
            ->where('colegio_id', $colegioId)
            ->where('estado', 'pagado')
            ->whereNotNull('fecha_pago')
            ->whereYear('fecha_pago', $anio)
            ->groupBy(DB::raw('MONTH(fecha_pago)'))
            ->select(DB::raw('MONTH(fecha_pago) as mes'), DB::raw('SUM(monto) as total'))
            ->pluck('total', 'mes');

        $pendientes = DB::table('pagos')
            ->where('colegio_id', $colegioId)
            ->where('estado', 'pendiente')
            ->whereYear('created_at', $anio)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->select(DB::raw('MONTH(created_at) as mes'), DB::raw('SUM(monto) as total'))
            ->pluck('total', 'mes');

        $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $dataPagados = [];
        $dataPendientes = [];

        for ($i = 1; $i <= 12; $i++) {
            $dataPagados[] = round((float)($pagados[$i] ?? 0), 2);
            $dataPendientes[] = round((float)($pendientes[$i] ?? 0), 2);
        }

        return response()->json([
            'labels' => $meses,
            'pagados' => $dataPagados,
            'pendientes' => $dataPendientes,
        ]);
    }

    public function matriculasPorNivel(Request $request): JsonResponse
    {
        $colegioId = $this->colegioId();
        $periodoId = $request->input('periodo_id')
            ?? Periodo::where('colegio_id', $colegioId)->where('activo', true)->value('id');

        if (!$periodoId) {
            return response()->json(['labels' => [], 'data' => []]);
        }

        $resultados = DB::table('matriculas')
            ->join('secciones', 'matriculas.seccion_id', '=', 'secciones.id')
            ->join('grados', 'secciones.grado_id', '=', 'grados.id')
            ->join('niveles', 'grados.nivel_id', '=', 'niveles.id')
            ->where('matriculas.colegio_id', $colegioId)
            ->where('matriculas.periodo_id', $periodoId)
            ->where('matriculas.estado', 'activa')
            ->groupBy('niveles.nombre')
            ->select('niveles.nombre', DB::raw('COUNT(*) as total'))
            ->orderBy('niveles.nombre')
            ->get();

        return response()->json([
            'labels' => $resultados->pluck('nombre'),
            'data' => $resultados->pluck('total'),
        ]);
    }

    public function rendimientoGeneral(Request $request): JsonResponse
    {
        $colegioId = $this->colegioId();
        $periodoId = $request->input('periodo_id')
            ?? Periodo::where('colegio_id', $colegioId)->where('activo', true)->value('id');

        if (!$periodoId) {
            return response()->json(['labels' => [], 'data' => []]);
        }

        $resultados = DB::table('notas')
            ->join('matriculas', 'notas.matricula_id', '=', 'matriculas.id')
            ->where('notas.colegio_id', $colegioId)
            ->where('matriculas.periodo_id', $periodoId)
            ->groupBy('notas.nota_letra')
            ->select('notas.nota_letra', DB::raw('COUNT(*) as total'))
            ->get()
            ->pluck('total', 'nota_letra');

        $labels = ['AD (18-20)', 'A (14-17)', 'B (11-13)', 'C (0-10)'];
        $data = [
            (int)($resultados['AD'] ?? 0),
            (int)($resultados['A'] ?? 0),
            (int)($resultados['B'] ?? 0),
            (int)($resultados['C'] ?? 0),
        ];

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }
}
