<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreaDatosDePrueba;
use Tests\TestCase;

class ExportTest extends TestCase
{
    use RefreshDatabase, CreaDatosDePrueba;

    protected function setUp(): void
    {
        parent::setUp();
        $this->crearDatosDePrueba();
    }

    public function test_admin_ve_pagina_exportar(): void
    {
        $this->actingAs($this->adminUser)
            ->get('/admin/exportar')
            ->assertStatus(200)
            ->assertSee('Exportar');
    }

    public function test_admin_exporta_alumnos_excel(): void
    {
        $this->actingAs($this->adminUser)
            ->get('/admin/exportar/alumnos')
            ->assertStatus(200)
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_admin_exporta_notas_excel(): void
    {
        $this->actingAs($this->adminUser)
            ->get('/admin/exportar/notas')
            ->assertStatus(200)
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_admin_exporta_asistencia_excel(): void
    {
        $this->actingAs($this->adminUser)
            ->get('/admin/exportar/asistencia')
            ->assertStatus(200)
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_admin_exporta_pagos_excel(): void
    {
        $this->actingAs($this->adminUser)
            ->get('/admin/exportar/pagos')
            ->assertStatus(200)
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_docente_no_puede_exportar(): void
    {
        $this->actingAs($this->docenteUser)
            ->get('/admin/exportar')
            ->assertStatus(403);
    }
}
