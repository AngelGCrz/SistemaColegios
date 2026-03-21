<?php

namespace Tests\Feature;

use App\Models\Colegio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreaDatosDePrueba;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase, CreaDatosDePrueba;

    protected function setUp(): void
    {
        parent::setUp();
        $this->crearDatosDePrueba();
    }

    // --- Admin routes ---

    public function test_admin_puede_acceder_admin_dashboard(): void
    {
        $this->actingAs($this->adminUser)
            ->get('/admin/dashboard')
            ->assertStatus(200);
    }

    public function test_docente_no_puede_acceder_admin_dashboard(): void
    {
        $this->actingAs($this->docenteUser)
            ->get('/admin/dashboard')
            ->assertStatus(403);
    }

    public function test_alumno_no_puede_acceder_admin_dashboard(): void
    {
        $this->actingAs($this->alumnoUser)
            ->get('/admin/dashboard')
            ->assertStatus(403);
    }

    // --- Docente routes ---

    public function test_docente_puede_acceder_docente_dashboard(): void
    {
        $this->actingAs($this->docenteUser)
            ->get('/docente/dashboard')
            ->assertStatus(200);
    }

    public function test_admin_no_puede_acceder_docente_dashboard(): void
    {
        $this->actingAs($this->adminUser)
            ->get('/docente/dashboard')
            ->assertStatus(403);
    }

    // --- Alumno routes ---

    public function test_alumno_puede_acceder_alumno_dashboard(): void
    {
        $this->actingAs($this->alumnoUser)
            ->get('/alumno/dashboard')
            ->assertStatus(200);
    }

    public function test_docente_no_puede_acceder_alumno_dashboard(): void
    {
        $this->actingAs($this->docenteUser)
            ->get('/alumno/dashboard')
            ->assertStatus(403);
    }

    // --- Padre routes ---

    public function test_padre_puede_acceder_padre_dashboard(): void
    {
        $this->actingAs($this->padreUser)
            ->get('/padre/dashboard')
            ->assertStatus(200);
    }

    public function test_alumno_no_puede_acceder_padre_dashboard(): void
    {
        $this->actingAs($this->alumnoUser)
            ->get('/padre/dashboard')
            ->assertStatus(403);
    }

    // --- Colegio inactivo ---

    public function test_colegio_inactivo_redirige_a_login(): void
    {
        $this->colegio->update(['activo' => false]);

        $this->actingAs($this->adminUser)
            ->get('/admin/dashboard')
            ->assertRedirect(route('login'));
    }

    public function test_colegio_vencido_redirige_a_login(): void
    {
        $this->colegio->update(['fecha_vencimiento' => now()->subDay()]);

        $this->actingAs($this->adminUser)
            ->get('/admin/dashboard')
            ->assertRedirect(route('login'));
    }

    // --- Rutas compartidas (mensajes) ---

    public function test_cualquier_rol_accede_a_mensajes(): void
    {
        foreach ([$this->adminUser, $this->docenteUser, $this->alumnoUser, $this->padreUser] as $user) {
            $this->actingAs($user)
                ->get('/mensajes')
                ->assertStatus(200);
        }
    }
}
