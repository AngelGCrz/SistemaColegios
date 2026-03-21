<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreaDatosDePrueba;
use Tests\TestCase;

class DocenteAsistenciaTest extends TestCase
{
    use RefreshDatabase, CreaDatosDePrueba;

    protected function setUp(): void
    {
        parent::setUp();
        $this->crearDatosDePrueba();
    }

    public function test_docente_ve_selector_de_asistencia(): void
    {
        $this->actingAs($this->docenteUser)
            ->get('/docente/asistencia')
            ->assertStatus(200);
    }

    public function test_docente_puede_registrar_asistencia(): void
    {
        // Primero seleccionar sección y fecha
        $response = $this->actingAs($this->docenteUser)
            ->post('/docente/asistencia/registrar', [
                'seccion_id' => $this->seccion->id,
                'fecha' => now()->format('Y-m-d'),
            ]);

        $response->assertStatus(200);
        $response->assertSee('Test, Alumno');
    }

    public function test_docente_puede_guardar_asistencia(): void
    {
        $fecha = now()->format('Y-m-d');

        $response = $this->actingAs($this->docenteUser)
            ->post('/docente/asistencia/guardar', [
                'seccion_id' => $this->seccion->id,
                'fecha' => $fecha,
                'asistencias' => [
                    ['matricula_id' => $this->matricula->id, 'estado' => 'presente'],
                ],
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('asistencias', [
            'matricula_id' => $this->matricula->id,
            'seccion_id' => $this->seccion->id,
            'fecha' => $fecha,
            'estado' => 'presente',
        ]);
    }

    public function test_guarda_falta_correctamente(): void
    {
        $fecha = now()->format('Y-m-d');

        $this->actingAs($this->docenteUser)
            ->post('/docente/asistencia/guardar', [
                'seccion_id' => $this->seccion->id,
                'fecha' => $fecha,
                'asistencias' => [
                    ['matricula_id' => $this->matricula->id, 'estado' => 'falta'],
                ],
            ]);

        $this->assertDatabaseHas('asistencias', [
            'matricula_id' => $this->matricula->id,
            'estado' => 'falta',
        ]);
    }
}
