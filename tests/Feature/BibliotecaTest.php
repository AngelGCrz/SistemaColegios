<?php

namespace Tests\Feature;

use App\Models\RecursoDigital;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\CreaDatosDePrueba;
use Tests\TestCase;

class BibliotecaTest extends TestCase
{
    use RefreshDatabase, CreaDatosDePrueba;

    protected function setUp(): void
    {
        parent::setUp();
        $this->crearDatosDePrueba();
    }

    public function test_admin_ve_biblioteca(): void
    {
        $this->actingAs($this->adminUser)
            ->get('/admin/biblioteca')
            ->assertStatus(200)
            ->assertSee('Biblioteca');
    }

    public function test_admin_ve_formulario_crear_recurso(): void
    {
        $this->actingAs($this->adminUser)
            ->get('/admin/biblioteca/create')
            ->assertStatus(200);
    }

    public function test_admin_crea_recurso_tipo_enlace(): void
    {
        $this->actingAs($this->adminUser)
            ->post('/admin/biblioteca', [
                'titulo' => 'Video educativo',
                'descripcion' => 'Un video sobre matemáticas',
                'tipo' => 'enlace',
                'url_externa' => 'https://www.example.com/video',
                'materia' => 'Matemáticas',
                'nivel' => 'primaria',
                'publico' => true,
            ])
            ->assertRedirect('/admin/biblioteca');

        $this->assertDatabaseHas('recursos_digitales', [
            'titulo' => 'Video educativo',
            'tipo' => 'enlace',
        ]);
    }

    public function test_admin_elimina_recurso(): void
    {
        $recurso = RecursoDigital::create([
            'colegio_id' => $this->adminUser->colegio_id,
            'user_id' => $this->adminUser->id,
            'titulo' => 'Recurso temporal',
            'tipo' => 'enlace',
            'url_externa' => 'https://example.com',
            'publico' => true,
        ]);

        $this->actingAs($this->adminUser)
            ->delete("/admin/biblioteca/{$recurso->id}")
            ->assertRedirect('/admin/biblioteca');

        $this->assertDatabaseMissing('recursos_digitales', ['id' => $recurso->id]);
    }

    public function test_usuario_ve_biblioteca_publica(): void
    {
        RecursoDigital::create([
            'colegio_id' => $this->alumnoUser->colegio_id,
            'user_id' => $this->adminUser->id,
            'titulo' => 'Recurso público',
            'tipo' => 'enlace',
            'url_externa' => 'https://example.com',
            'publico' => true,
        ]);

        $this->actingAs($this->alumnoUser)
            ->get('/biblioteca')
            ->assertStatus(200)
            ->assertSee('Recurso público');
    }

    public function test_docente_no_puede_administrar_biblioteca(): void
    {
        $this->actingAs($this->docenteUser)
            ->get('/admin/biblioteca')
            ->assertStatus(403);
    }
}
