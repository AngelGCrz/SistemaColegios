<?php

namespace Tests\Feature;

use App\Models\Asistencia;
use App\Models\Nota;
use App\Models\Pago;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreaDatosDePrueba;
use Tests\TestCase;

class PadreAccesoTest extends TestCase
{
    use RefreshDatabase, CreaDatosDePrueba;

    protected function setUp(): void
    {
        parent::setUp();
        $this->crearDatosDePrueba();
    }

    public function test_padre_ve_dashboard_con_hijos(): void
    {
        $this->actingAs($this->padreUser)
            ->get('/padre/dashboard')
            ->assertStatus(200)
            ->assertSee('Alumno Test');
    }

    public function test_padre_ve_notas_del_hijo(): void
    {
        Nota::create([
            'colegio_id' => $this->colegio->id,
            'matricula_id' => $this->matricula->id,
            'curso_seccion_id' => $this->cursoSeccion->id,
            'bimestre_id' => $this->bimestre->id,
            'nota' => 17,
            'nota_letra' => 'A',
        ]);

        $this->actingAs($this->padreUser)
            ->get("/padre/notas/{$this->alumno->id}")
            ->assertStatus(200)
            ->assertSee('Matemáticas');
    }

    public function test_padre_ve_asistencia_del_hijo(): void
    {
        Asistencia::create([
            'colegio_id' => $this->colegio->id,
            'matricula_id' => $this->matricula->id,
            'seccion_id' => $this->seccion->id,
            'fecha' => now()->format('Y-m-d'),
            'estado' => 'presente',
        ]);

        $this->actingAs($this->padreUser)
            ->get("/padre/asistencia/{$this->alumno->id}")
            ->assertStatus(200);
    }

    public function test_padre_ve_pagos_del_hijo(): void
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

        $this->actingAs($this->padreUser)
            ->get("/padre/pagos/{$this->alumno->id}")
            ->assertStatus(200)
            ->assertSee('350');
    }

    public function test_padre_no_puede_ver_datos_de_alumno_ajeno(): void
    {
        // Crear otro alumno (sin relación con este padre)
        $otroAlumnoUser = \App\Models\User::create([
            'colegio_id' => $this->colegio->id,
            'nombre' => 'Otro', 'apellidos' => 'Alumno',
            'email' => 'otro.alumno@test.com',
            'password' => bcrypt('password'), 'rol' => 'alumno', 'activo' => true,
        ]);
        $otroAlumno = \App\Models\Alumno::create([
            'colegio_id' => $this->colegio->id,
            'user_id' => $otroAlumnoUser->id,
            'codigo_alumno' => 'ALU999',
        ]);

        $this->actingAs($this->padreUser)
            ->get("/padre/notas/{$otroAlumno->id}")
            ->assertStatus(404);
    }
}
