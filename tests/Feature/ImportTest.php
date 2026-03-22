<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\CreaDatosDePrueba;
use Tests\TestCase;

class ImportTest extends TestCase
{
    use RefreshDatabase, CreaDatosDePrueba;

    protected function setUp(): void
    {
        parent::setUp();
        $this->crearDatosDePrueba();
    }

    public function test_admin_ve_pagina_importar(): void
    {
        $this->actingAs($this->adminUser)
            ->get('/admin/importar')
            ->assertStatus(200)
            ->assertSee('Importar');
    }

    public function test_admin_descarga_plantilla_csv(): void
    {
        $this->actingAs($this->adminUser)
            ->get('/admin/importar/plantilla')
            ->assertStatus(200)
            ->assertHeader('content-disposition');
    }

    public function test_admin_preview_csv_valido(): void
    {
        $csv = "nombre,apellidos,email,dni,fecha_nacimiento,genero,nivel,grado,direccion,telefono\n";
        $csv .= "Juan,Perez,juan.test@example.com,12345678,2010-01-01,M,primaria,1,Calle 1,999888777\n";

        $file = UploadedFile::fake()->createWithContent('alumnos.csv', $csv);

        $this->actingAs($this->adminUser)
            ->post('/admin/importar/preview', ['archivo' => $file])
            ->assertStatus(200)
            ->assertSee('Juan');
    }

    public function test_admin_preview_csv_sin_archivo(): void
    {
        $this->actingAs($this->adminUser)
            ->post('/admin/importar/preview', [])
            ->assertSessionHasErrors('archivo');
    }

    public function test_docente_no_puede_importar(): void
    {
        $this->actingAs($this->docenteUser)
            ->get('/admin/importar')
            ->assertStatus(403);
    }
}
