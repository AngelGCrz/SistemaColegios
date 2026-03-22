<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreaDatosDePrueba;
use Tests\TestCase;

class ReportesTest extends TestCase
{
    use RefreshDatabase, CreaDatosDePrueba;

    protected function setUp(): void
    {
        parent::setUp();
        $this->crearDatosDePrueba();
    }

    public function test_admin_ve_pagina_reportes(): void
    {
        $this->actingAs($this->adminUser)
            ->get('/admin/reportes')
            ->assertStatus(200)
            ->assertSee('Reportes');
    }

    public function test_admin_obtiene_notas_por_curso_json(): void
    {
        $this->actingAs($this->adminUser)
            ->getJson('/admin/reportes/api/notas-por-curso')
            ->assertStatus(200)
            ->assertJsonStructure(['labels', 'data']);
    }

    public function test_admin_obtiene_asistencia_mensual_json(): void
    {
        $this->actingAs($this->adminUser)
            ->getJson('/admin/reportes/api/asistencia-mensual')
            ->assertStatus(200)
            ->assertJsonStructure(['labels', 'datasets']);
    }

    public function test_admin_obtiene_pagos_mensual_json(): void
    {
        $this->actingAs($this->adminUser)
            ->getJson('/admin/reportes/api/pagos-mensual')
            ->assertStatus(200)
            ->assertJsonStructure(['labels', 'pagados', 'pendientes']);
    }

    public function test_admin_obtiene_matriculas_por_nivel_json(): void
    {
        $this->actingAs($this->adminUser)
            ->getJson('/admin/reportes/api/matriculas-por-nivel')
            ->assertStatus(200)
            ->assertJsonStructure(['labels', 'data']);
    }

    public function test_admin_obtiene_rendimiento_general_json(): void
    {
        $this->actingAs($this->adminUser)
            ->getJson('/admin/reportes/api/rendimiento-general')
            ->assertStatus(200)
            ->assertJsonStructure(['labels', 'data']);
    }

    public function test_docente_no_accede_reportes(): void
    {
        $this->actingAs($this->docenteUser)
            ->get('/admin/reportes')
            ->assertStatus(403);
    }
}
