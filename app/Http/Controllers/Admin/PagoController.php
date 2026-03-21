<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\ConceptoPago;
use App\Models\Pago;
use App\Models\Periodo;
use App\Notifications\PagoRegistrado;
use App\Traits\FiltraPorColegio;
use Illuminate\Http\Request;

class PagoController extends Controller
{
    use FiltraPorColegio;

    public function index(Request $request)
    {
        $query = Pago::where('colegio_id', $this->colegioId())
            ->with(['alumno.user', 'conceptoPago', 'periodo']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('alumno_id')) {
            $query->where('alumno_id', $request->alumno_id);
        }

        $pagos = $query->orderByDesc('created_at')->paginate(20);

        $conceptos = ConceptoPago::where('colegio_id', $this->colegioId())->get();

        return view('admin.pagos.index', compact('pagos', 'conceptos'));
    }

    public function create()
    {
        $alumnos = Alumno::where('colegio_id', $this->colegioId())
            ->with('user')
            ->get();

        $conceptos = ConceptoPago::where('colegio_id', $this->colegioId())
            ->where('activo', true)
            ->get();

        $periodos = Periodo::where('colegio_id', $this->colegioId())
            ->orderByDesc('anio')
            ->get();

        return view('admin.pagos.create', compact('alumnos', 'conceptos', 'periodos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'alumno_id' => ['required', 'exists:alumnos,id'],
            'concepto_pago_id' => ['required', 'exists:conceptos_pago,id'],
            'periodo_id' => ['required', 'exists:periodos,id'],
            'monto' => ['required', 'numeric', 'min:0.01'],
            'estado' => ['required', 'in:pendiente,pagado'],
            'fecha_pago' => ['nullable', 'date'],
            'metodo_pago' => ['nullable', 'string', 'max:50'],
            'numero_recibo' => ['nullable', 'string', 'max:50'],
            'observacion' => ['nullable', 'string', 'max:500'],
        ]);

        $pago = Pago::create(['colegio_id' => $this->colegioId(), ...$data]);

        // Notificar a los padres del alumno
        $pago->load(['alumno.user', 'conceptoPago', 'alumno.padres.user']);
        foreach ($pago->alumno->padres as $padre) {
            $padre->user->notify(new PagoRegistrado($pago));
        }

        return redirect()->route('admin.pagos.index')
            ->with('success', 'Pago registrado exitosamente.');
    }

    public function marcarPagado(Request $request, Pago $pago)
    {
        abort_if($pago->colegio_id !== $this->colegioId(), 403);

        $data = $request->validate([
            'fecha_pago' => ['required', 'date'],
            'metodo_pago' => ['nullable', 'string', 'max:50'],
            'numero_recibo' => ['nullable', 'string', 'max:50'],
        ]);

        $pago->update([
            'estado' => 'pagado',
            ...$data,
        ]);

        return back()->with('success', 'Pago marcado como pagado.');
    }

    // --- Conceptos de pago ---

    public function conceptos()
    {
        $conceptos = ConceptoPago::where('colegio_id', $this->colegioId())->get();

        return view('admin.pagos.conceptos', compact('conceptos'));
    }

    public function storeConcepto(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'monto' => ['required', 'numeric', 'min:0'],
        ]);

        ConceptoPago::create(['colegio_id' => $this->colegioId(), ...$data]);

        return back()->with('success', 'Concepto de pago creado.');
    }

    // Estado de cuenta por alumno
    public function estadoCuenta(Alumno $alumno)
    {
        abort_if($alumno->colegio_id !== $this->colegioId(), 403);

        $pagos = Pago::where('colegio_id', $this->colegioId())
            ->where('alumno_id', $alumno->id)
            ->with('conceptoPago')
            ->orderByDesc('created_at')
            ->get();

        $totalPendiente = $pagos->where('estado', 'pendiente')->sum('monto');
        $totalPagado = $pagos->where('estado', 'pagado')->sum('monto');

        return view('admin.pagos.estado-cuenta', compact('alumno', 'pagos', 'totalPendiente', 'totalPagado'));
    }
}
