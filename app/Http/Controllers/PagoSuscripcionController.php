<?php

namespace App\Http\Controllers;

use App\Models\Colegio;
use App\Models\PagoSuscripcion;
use App\Models\Plan;
use App\Models\Suscripcion;
use App\Services\MercadoPagoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PagoSuscripcionController extends Controller
{
    /**
     * Muestra la página de checkout para renovar/activar suscripción.
     */
    public function checkout(Request $request)
    {
        $colegio = auth()->user()->colegio;
        $suscripcion = $colegio->suscripcionActiva;
        $planes = Plan::where('activo', true)->orderBy('orden')->get();

        return view('suscripcion.checkout', compact('colegio', 'suscripcion', 'planes'));
    }

    /**
     * Genera la preferencia de pago en MercadoPago y redirige.
     */
    public function procesar(Request $request)
    {
        $data = $request->validate([
            'plan_id' => ['required', 'exists:planes,id'],
            'ciclo' => ['required', 'in:mensual,anual'],
        ]);

        $colegio = auth()->user()->colegio;
        $plan = Plan::findOrFail($data['plan_id']);
        $monto = $data['ciclo'] === 'anual' ? $plan->precio_anual : $plan->precio_mensual;

        $mp = new MercadoPagoService();

        if (!$mp->estaConfigurado()) {
            // Modo demo: activar suscripción directamente
            return $this->activarSuscripcionDirecta($colegio, $plan, $data['ciclo'], $monto);
        }

        $referencia = "sub_{$colegio->id}_" . time();

        $preferencia = $mp->crearPreferencia([
            'titulo' => "Plan {$plan->nombre} ({$data['ciclo']}) - {$colegio->nombre}",
            'monto' => $monto,
            'moneda' => 'USD',
            'referencia' => $referencia,
            'url_exito' => route('suscripcion.exito', ['referencia' => $referencia]),
            'url_fallo' => route('suscripcion.checkout'),
            'url_pendiente' => route('suscripcion.checkout'),
            'url_webhook' => route('webhook.mercadopago'),
        ]);

        if (!$preferencia) {
            return back()->with('error', 'Error al procesar el pago. Intenta nuevamente.');
        }

        // Registrar pago pendiente
        PagoSuscripcion::create([
            'colegio_id' => $colegio->id,
            'suscripcion_id' => $colegio->suscripcionActiva?->id,
            'monto' => $monto,
            'moneda' => 'USD',
            'estado' => 'pendiente',
            'metodo_pago' => 'mercadopago',
            'referencia_externa' => $referencia,
            'metadata' => [
                'plan_id' => $plan->id,
                'ciclo' => $data['ciclo'],
                'preference_id' => $preferencia['id'],
            ],
        ]);

        return redirect($preferencia['init_point']);
    }

    /**
     * Callback de éxito después del pago.
     */
    public function exito(Request $request)
    {
        $referencia = $request->query('referencia');
        $pago = PagoSuscripcion::where('referencia_externa', $referencia)->first();

        if ($pago && $pago->estado === 'pendiente') {
            // Verificar con MercadoPago si está configurado
            $mp = new MercadoPagoService();
            $paymentId = $request->query('payment_id');

            if ($mp->estaConfigurado() && $paymentId) {
                $mpPago = $mp->obtenerPago($paymentId);
                if ($mpPago && $mpPago['status'] === 'approved') {
                    $this->confirmarPago($pago);
                }
            }
        }

        return redirect()->route('suscripcion.checkout')
            ->with('success', '¡Pago procesado! Tu suscripción ha sido activada.');
    }

    /**
     * Webhook de MercadoPago para notificaciones.
     */
    public function webhook(Request $request)
    {
        Log::info('MercadoPago webhook recibido', $request->all());

        $tipo = $request->input('type') ?? $request->input('topic');
        $dataId = $request->input('data.id') ?? $request->input('id');

        if ($tipo === 'payment' && $dataId) {
            $mp = new MercadoPagoService();
            $mpPago = $mp->obtenerPago($dataId);

            if ($mpPago && $mpPago['status'] === 'approved') {
                $referencia = $mpPago['external_reference'] ?? null;
                $pago = $referencia
                    ? PagoSuscripcion::where('referencia_externa', $referencia)->first()
                    : null;

                if ($pago && $pago->estado === 'pendiente') {
                    $this->confirmarPago($pago);
                }
            }
        }

        return response('OK', 200);
    }

    /**
     * Confirma un pago y activa la suscripción.
     */
    protected function confirmarPago(PagoSuscripcion $pago): void
    {
        $pago->update([
            'estado' => 'aprobado',
            'pagado_en' => now(),
        ]);

        $metadata = $pago->metadata ?? [];
        $planId = $metadata['plan_id'] ?? null;
        $ciclo = $metadata['ciclo'] ?? 'mensual';

        if (!$planId) {
            return;
        }

        $plan = Plan::find($planId);
        if (!$plan) {
            return;
        }

        $colegio = $pago->colegio;
        $fechaFin = $ciclo === 'anual' ? now()->addYear() : now()->addMonth();

        // Cancelar suscripciones previas
        $colegio->suscripciones()
            ->whereIn('estado', ['activa', 'trial'])
            ->update(['estado' => 'cancelada']);

        $suscripcion = Suscripcion::create([
            'colegio_id' => $colegio->id,
            'plan_id' => $plan->id,
            'estado' => 'activa',
            'ciclo' => $ciclo,
            'fecha_inicio' => now(),
            'fecha_fin' => $fechaFin,
            'monto' => $pago->monto,
            'referencia_pago' => $pago->referencia_externa,
        ]);

        $pago->update(['suscripcion_id' => $suscripcion->id]);

        $colegio->update([
            'plan' => $plan->slug,
            'fecha_vencimiento' => $fechaFin,
        ]);
    }

    /**
     * Modo demo: activa suscripción directamente sin pasarela real.
     */
    protected function activarSuscripcionDirecta(Colegio $colegio, Plan $plan, string $ciclo, float $monto)
    {
        $fechaFin = $ciclo === 'anual' ? now()->addYear() : now()->addMonth();

        $colegio->suscripciones()
            ->whereIn('estado', ['activa', 'trial'])
            ->update(['estado' => 'cancelada']);

        Suscripcion::create([
            'colegio_id' => $colegio->id,
            'plan_id' => $plan->id,
            'estado' => 'activa',
            'ciclo' => $ciclo,
            'fecha_inicio' => now(),
            'fecha_fin' => $fechaFin,
            'monto' => $monto,
        ]);

        PagoSuscripcion::create([
            'colegio_id' => $colegio->id,
            'monto' => $monto,
            'moneda' => 'USD',
            'estado' => 'aprobado',
            'metodo_pago' => 'demo',
            'pagado_en' => now(),
        ]);

        $colegio->update([
            'plan' => $plan->slug,
            'fecha_vencimiento' => $fechaFin,
        ]);

        return redirect()->route('suscripcion.checkout')
            ->with('success', "¡Plan {$plan->nombre} activado! (Modo demo)");
    }
}
