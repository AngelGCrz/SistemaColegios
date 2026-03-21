<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Suscripcion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreaDatosDePrueba;
use Tests\TestCase;

class SuscripcionTest extends TestCase
{
    use RefreshDatabase, CreaDatosDePrueba;

    protected function setUp(): void
    {
        parent::setUp();
        $this->crearDatosDePrueba();
    }

    // ── Verificación de suscripción ────────────────────

    public function test_modelo_suscripcion_vigente_activa(): void
    {
        $this->assertTrue($this->suscripcion->estaVigente());
    }

    public function test_modelo_suscripcion_vigente_trial(): void
    {
        $this->suscripcion->update([
            'estado' => 'trial',
            'trial_hasta' => now()->addDays(15),
        ]);
        $this->assertTrue($this->suscripcion->fresh()->estaVigente());
    }

    public function test_modelo_suscripcion_vencida(): void
    {
        $this->suscripcion->update([
            'estado' => 'activa',
            'fecha_fin' => now()->subDay(),
        ]);
        $this->assertFalse($this->suscripcion->fresh()->estaVigente());
    }

    public function test_modelo_suscripcion_trial_expirado(): void
    {
        $this->suscripcion->update([
            'estado' => 'trial',
            'trial_hasta' => now()->subDay(),
        ]);
        $this->assertFalse($this->suscripcion->fresh()->estaVigente());
    }

    public function test_modelo_dias_restantes(): void
    {
        $this->suscripcion->update([
            'fecha_fin' => now()->addDays(30),
        ]);
        $dias = $this->suscripcion->fresh()->diasRestantes();
        $this->assertGreaterThanOrEqual(29, $dias);
        $this->assertLessThanOrEqual(30, $dias);
    }

    // ── Checkout ───────────────────────────────────────

    public function test_admin_puede_ver_checkout(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('suscripcion.checkout'));
        $response->assertOk();
        $response->assertViewIs('suscripcion.checkout');
    }

    public function test_docente_no_puede_ver_checkout(): void
    {
        $response = $this->actingAs($this->docenteUser)->get(route('suscripcion.checkout'));
        $response->assertForbidden();
    }

    public function test_procesar_pago_demo_activa_suscripcion(): void
    {
        // Sin MERCADOPAGO_ACCESS_TOKEN, activa en modo demo
        $response = $this->actingAs($this->adminUser)->post(route('suscripcion.procesar'), [
            'plan_id' => $this->plan->id,
            'ciclo' => 'mensual',
        ]);

        $response->assertRedirect(route('suscripcion.checkout'));

        // La suscripción anterior debe estar cancelada
        $this->assertEquals('cancelada', $this->suscripcion->fresh()->estado);

        // Debe existir nueva suscripción activa
        $nueva = $this->colegio->suscripciones()->where('estado', 'activa')->latest('id')->first();
        $this->assertNotNull($nueva);
        $this->assertEquals($this->plan->id, $nueva->plan_id);
    }

    // ── Límite de plan ─────────────────────────────────

    public function test_plan_tiene_caracteristica(): void
    {
        $this->assertTrue($this->plan->tieneCaracteristica('Gestión académica'));
        $this->assertFalse($this->plan->tieneCaracteristica('Inexistente'));
    }

    public function test_colegio_suscripcion_activa_relacion(): void
    {
        $suscActiva = $this->colegio->suscripcionActiva;
        $this->assertNotNull($suscActiva);
        $this->assertEquals($this->plan->id, $suscActiva->plan_id);
    }

    // ── Middleware suscripcion.vigente ──────────────────

    public function test_superadmin_bypass_verificacion_suscripcion(): void
    {
        // Superadmin no tiene colegio_id, debe pasar sin problema
        $response = $this->actingAs($this->superadminUser)->get(route('superadmin.dashboard'));
        $response->assertOk();
    }
}
