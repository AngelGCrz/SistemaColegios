<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreaDatosDePrueba;
use Tests\TestCase;

class ApiAlumnoTest extends TestCase
{
    use RefreshDatabase, CreaDatosDePrueba;

    protected function setUp(): void
    {
        parent::setUp();
        $this->crearDatosDePrueba();
    }

    private function alumnoToken(): string
    {
        return $this->alumnoUser->createToken('test')->plainTextToken;
    }

    public function test_alumno_ve_dashboard_api(): void
    {
        $this->getJson('/api/v1/alumno/dashboard', [
            'Authorization' => 'Bearer ' . $this->alumnoToken(),
        ])->assertStatus(200)
          ->assertJsonStructure(['alumno', 'matricula']);
    }

    public function test_alumno_ve_notas_api(): void
    {
        $this->getJson('/api/v1/alumno/notas', [
            'Authorization' => 'Bearer ' . $this->alumnoToken(),
        ])->assertStatus(200);
    }

    public function test_alumno_ve_asistencia_api(): void
    {
        $this->getJson('/api/v1/alumno/asistencia', [
            'Authorization' => 'Bearer ' . $this->alumnoToken(),
        ])->assertStatus(200);
    }

    public function test_alumno_ve_tareas_api(): void
    {
        $this->getJson('/api/v1/alumno/tareas', [
            'Authorization' => 'Bearer ' . $this->alumnoToken(),
        ])->assertStatus(200);
    }

    public function test_alumno_ve_pagos_api(): void
    {
        $this->getJson('/api/v1/alumno/pagos', [
            'Authorization' => 'Bearer ' . $this->alumnoToken(),
        ])->assertStatus(200);
    }

    public function test_docente_no_accede_alumno_api(): void
    {
        $token = $this->docenteUser->createToken('test')->plainTextToken;

        $this->getJson('/api/v1/alumno/dashboard', [
            'Authorization' => "Bearer {$token}",
        ])->assertStatus(403);
    }
}
