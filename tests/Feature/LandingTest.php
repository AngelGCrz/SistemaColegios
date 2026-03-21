<?php

namespace Tests\Feature;

use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreaDatosDePrueba;
use Tests\TestCase;

class LandingTest extends TestCase
{
    use RefreshDatabase, CreaDatosDePrueba;

    protected function setUp(): void
    {
        parent::setUp();
        $this->crearDatosDePrueba();
    }

    public function test_landing_page_carga_correctamente(): void
    {
        $response = $this->get(route('landing'));
        $response->assertOk();
        $response->assertViewIs('landing');
        $response->assertViewHas('planes');
    }

    public function test_landing_muestra_planes_activos(): void
    {
        Plan::create([
            'nombre' => 'Premium',
            'slug' => 'premium',
            'precio_mensual' => 55,
            'precio_anual' => 550,
            'max_alumnos' => 1000,
            'caracteristicas' => ['Todo'],
            'activo' => true,
            'orden' => 3,
        ]);

        $response = $this->get(route('landing'));
        $response->assertOk();
        $response->assertSee('Básico');
        $response->assertSee('Premium');
    }

    public function test_landing_no_muestra_planes_inactivos(): void
    {
        Plan::create([
            'nombre' => 'Oculto',
            'slug' => 'oculto',
            'precio_mensual' => 99,
            'precio_anual' => 999,
            'max_alumnos' => 50,
            'caracteristicas' => [],
            'activo' => false,
            'orden' => 99,
        ]);

        $response = $this->get(route('landing'));
        $response->assertOk();
        $response->assertDontSee('Oculto');
    }

    public function test_login_muestra_enlace_registro(): void
    {
        $response = $this->get(route('login'));
        $response->assertOk();
        $response->assertSee(route('registro'));
    }
}
