<?php

namespace Tests\Feature;

use App\Models\Colegio;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreaDatosDePrueba;
use Tests\TestCase;

class OnboardingTest extends TestCase
{
    use RefreshDatabase, CreaDatosDePrueba;

    protected function setUp(): void
    {
        parent::setUp();
        $this->crearDatosDePrueba();
    }

    public function test_formulario_registro_se_muestra(): void
    {
        $response = $this->get(route('registro'));
        $response->assertOk();
        $response->assertViewIs('onboarding.registro');
        $response->assertViewHas('planes');
    }

    public function test_registro_exitoso_crea_colegio_y_admin(): void
    {
        $response = $this->post(route('registro.store'), [
            'colegio_nombre' => 'Colegio Registrado',
            'colegio_email' => 'info@registrado.com',
            'colegio_telefono' => '999000111',
            'colegio_direccion' => 'Calle Test 456',
            'contacto_nombre' => 'Juan Director',
            'contacto_email' => 'juan@registrado.com',
            'contacto_telefono' => '999000222',
            'plan_id' => $this->plan->id,
            'admin_nombre' => 'Juan',
            'admin_apellidos' => 'Director',
            'admin_email' => 'juandir@registrado.com',
            'admin_password' => 'password123',
            'admin_password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('colegios', ['nombre' => 'Colegio Registrado']);
        $this->assertDatabaseHas('users', [
            'email' => 'juandir@registrado.com',
            'rol' => 'admin',
        ]);

        $colegio = Colegio::where('nombre', 'Colegio Registrado')->first();
        $this->assertNotNull($colegio);
        $this->assertEquals('basico', $colegio->plan);

        // Se crea suscripción trial
        $suscripcion = $colegio->suscripciones()->first();
        $this->assertNotNull($suscripcion);
        $this->assertEquals('trial', $suscripcion->estado);
        $this->assertNotNull($suscripcion->trial_hasta);
    }

    public function test_registro_falla_sin_datos_requeridos(): void
    {
        $response = $this->post(route('registro.store'), []);
        $response->assertSessionHasErrors([
            'colegio_nombre', 'contacto_nombre', 'contacto_email',
            'plan_id', 'admin_nombre', 'admin_apellidos',
            'admin_email', 'admin_password',
        ]);
    }

    public function test_registro_falla_con_email_duplicado(): void
    {
        $response = $this->post(route('registro.store'), [
            'colegio_nombre' => 'Otro Colegio',
            'contacto_nombre' => 'Contacto',
            'contacto_email' => 'contacto@test.com',
            'plan_id' => $this->plan->id,
            'admin_nombre' => 'Admin',
            'admin_apellidos' => 'Dup',
            'admin_email' => 'admin@test.com', // ya existe en CreaDatosDePrueba
            'admin_password' => 'password123',
            'admin_password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('admin_email');
    }

    public function test_registro_falla_sin_confirmacion_password(): void
    {
        $response = $this->post(route('registro.store'), [
            'colegio_nombre' => 'Otro Colegio',
            'contacto_nombre' => 'Contacto',
            'contacto_email' => 'contacto@test.com',
            'plan_id' => $this->plan->id,
            'admin_nombre' => 'Admin',
            'admin_apellidos' => 'Test',
            'admin_email' => 'nuevo@test.com',
            'admin_password' => 'password123',
            'admin_password_confirmation' => 'diferente',
        ]);

        $response->assertSessionHasErrors('admin_password');
    }
}
