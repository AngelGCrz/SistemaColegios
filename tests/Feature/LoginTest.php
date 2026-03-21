<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreaDatosDePrueba;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase, CreaDatosDePrueba;

    protected function setUp(): void
    {
        parent::setUp();
        $this->crearDatosDePrueba();
    }

    public function test_login_page_se_muestra(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Iniciar Sesión');
    }

    public function test_admin_puede_loguearse(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($this->adminUser);
    }

    public function test_docente_redirige_a_su_dashboard(): void
    {
        $response = $this->post('/login', [
            'email' => 'docente@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('docente.dashboard'));
        $this->assertAuthenticatedAs($this->docenteUser);
    }

    public function test_alumno_redirige_a_su_dashboard(): void
    {
        $response = $this->post('/login', [
            'email' => 'alumno@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('alumno.dashboard'));
        $this->assertAuthenticatedAs($this->alumnoUser);
    }

    public function test_padre_redirige_a_su_dashboard(): void
    {
        $response = $this->post('/login', [
            'email' => 'padre@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('padre.dashboard'));
        $this->assertAuthenticatedAs($this->padreUser);
    }

    public function test_credenciales_incorrectas_no_loguean(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'wrong',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_usuario_inactivo_no_puede_loguearse(): void
    {
        $this->adminUser->update(['activo' => false]);

        $response = $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_logout_cierra_sesion(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post('/logout');

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_invitado_no_puede_acceder_dashboard(): void
    {
        $this->get('/admin/dashboard')->assertRedirect('/login');
        $this->get('/docente/dashboard')->assertRedirect('/login');
        $this->get('/alumno/dashboard')->assertRedirect('/login');
        $this->get('/padre/dashboard')->assertRedirect('/login');
    }
}
