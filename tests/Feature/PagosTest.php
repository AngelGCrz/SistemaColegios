<?php

namespace Tests\Feature;

use App\Models\Pago;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreaDatosDePrueba;
use Tests\TestCase;

class PagosTest extends TestCase
{
    use RefreshDatabase, CreaDatosDePrueba;

    protected function setUp(): void
    {
        parent::setUp();
        $this->crearDatosDePrueba();
    }

    public function test_admin_ve_lista_de_pagos(): void
    {
        $this->actingAs($this->adminUser)
            ->get('/admin/pagos')
            ->assertStatus(200);
    }

    public function test_admin_ve_formulario_crear_pago(): void
    {
        $this->actingAs($this->adminUser)
            ->get('/admin/pagos/create')
            ->assertStatus(200)
            ->assertSee('Test, Alumno');
    }

    public function test_admin_puede_registrar_pago_pendiente(): void
    {
        $concepto = $this->crearConceptoPago();

        $response = $this->actingAs($this->adminUser)->post('/admin/pagos', [
            'alumno_id' => $this->alumno->id,
            'concepto_pago_id' => $concepto->id,
            'periodo_id' => $this->periodo->id,
            'monto' => 350.00,
            'estado' => 'pendiente',
        ]);

        $response->assertRedirect(route('admin.pagos.index'));

        $this->assertDatabaseHas('pagos', [
            'alumno_id' => $this->alumno->id,
            'monto' => 350.00,
            'estado' => 'pendiente',
        ]);
    }

    public function test_admin_puede_marcar_pago_como_pagado(): void
    {
        $concepto = $this->crearConceptoPago();

        $pago = Pago::create([
            'colegio_id' => $this->colegio->id,
            'alumno_id' => $this->alumno->id,
            'concepto_pago_id' => $concepto->id,
            'periodo_id' => $this->periodo->id,
            'monto' => 350.00,
            'estado' => 'pendiente',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->patch("/admin/pagos/{$pago->id}/pagado", [
                'fecha_pago' => now()->format('Y-m-d'),
                'metodo_pago' => 'efectivo',
            ]);

        $response->assertRedirect();

        $pago->refresh();
        $this->assertEquals('pagado', $pago->estado);
        $this->assertNotNull($pago->fecha_pago);
    }

    public function test_admin_puede_crear_concepto_de_pago(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post('/admin/pagos/conceptos', [
                'nombre' => 'Cuota Mensual',
                'monto' => 250.00,
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('conceptos_pago', [
            'nombre' => 'Cuota Mensual',
            'colegio_id' => $this->colegio->id,
        ]);
    }

    public function test_admin_ve_estado_de_cuenta_del_alumno(): void
    {
        $concepto = $this->crearConceptoPago();

        Pago::create([
            'colegio_id' => $this->colegio->id,
            'alumno_id' => $this->alumno->id,
            'concepto_pago_id' => $concepto->id,
            'periodo_id' => $this->periodo->id,
            'monto' => 350.00,
            'estado' => 'pendiente',
        ]);

        $this->actingAs($this->adminUser)
            ->get("/admin/pagos/alumno/{$this->alumno->id}")
            ->assertStatus(200)
            ->assertSee('350');
    }

    public function test_registrar_pago_sin_alumno_falla(): void
    {
        $concepto = $this->crearConceptoPago();

        $response = $this->actingAs($this->adminUser)->post('/admin/pagos', [
            'concepto_pago_id' => $concepto->id,
            'periodo_id' => $this->periodo->id,
            'monto' => 350.00,
            'estado' => 'pendiente',
        ]);

        $response->assertSessionHasErrors('alumno_id');
    }
}
