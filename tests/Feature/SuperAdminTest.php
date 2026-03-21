<?php

namespace Tests\Feature;

use App\Models\Colegio;
use App\Models\Plan;
use App\Models\Suscripcion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreaDatosDePrueba;
use Tests\TestCase;

class SuperAdminTest extends TestCase
{
    use RefreshDatabase, CreaDatosDePrueba;

    protected function setUp(): void
    {
        parent::setUp();
        $this->crearDatosDePrueba();
    }

    // ── Dashboard ──────────────────────────────────────

    public function test_superadmin_puede_ver_dashboard(): void
    {
        $response = $this->actingAs($this->superadminUser)->get(route('superadmin.dashboard'));
        $response->assertOk();
        $response->assertViewIs('superadmin.dashboard');
        $response->assertViewHas('stats');
    }

    public function test_admin_no_puede_ver_dashboard_superadmin(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('superadmin.dashboard'));
        $response->assertForbidden();
    }

    public function test_docente_no_puede_ver_dashboard_superadmin(): void
    {
        $response = $this->actingAs($this->docenteUser)->get(route('superadmin.dashboard'));
        $response->assertForbidden();
    }

    // ── Colegios CRUD ──────────────────────────────────

    public function test_superadmin_puede_listar_colegios(): void
    {
        $response = $this->actingAs($this->superadminUser)->get(route('superadmin.colegios.index'));
        $response->assertOk();
        $response->assertViewIs('superadmin.colegios.index');
    }

    public function test_superadmin_puede_buscar_colegios(): void
    {
        $response = $this->actingAs($this->superadminUser)
            ->get(route('superadmin.colegios.index', ['buscar' => 'Test']));
        $response->assertOk();
    }

    public function test_superadmin_puede_ver_formulario_crear_colegio(): void
    {
        $response = $this->actingAs($this->superadminUser)->get(route('superadmin.colegios.create'));
        $response->assertOk();
    }

    public function test_superadmin_puede_crear_colegio(): void
    {
        $response = $this->actingAs($this->superadminUser)->post(route('superadmin.colegios.store'), [
            'nombre' => 'Colegio Nuevo',
            'email' => 'nuevo@colegio.com',
            'direccion' => 'Av. Principal 123',
            'telefono' => '999888777',
            'contacto_nombre' => 'Director Nuevo',
            'contacto_email' => 'director@nuevo.com',
            'contacto_telefono' => '999111222',
            'plan_id' => $this->plan->id,
            'ciclo' => 'mensual',
            'admin_email' => 'adminnuevo@colegio.com',
            'admin_password' => 'password123',
            'admin_nombre' => 'Admin',
            'admin_apellidos' => 'Nuevo',
        ]);

        $response->assertRedirect(route('superadmin.colegios.index'));
        $this->assertDatabaseHas('colegios', ['nombre' => 'Colegio Nuevo']);
        $this->assertDatabaseHas('users', ['email' => 'adminnuevo@colegio.com', 'rol' => 'admin']);
        $this->assertDatabaseHas('suscripciones', [
            'estado' => 'trial',
            'ciclo' => 'mensual',
        ]);
    }

    public function test_superadmin_puede_ver_detalle_colegio(): void
    {
        $response = $this->actingAs($this->superadminUser)
            ->get(route('superadmin.colegios.show', $this->colegio));
        $response->assertOk();
        $response->assertViewIs('superadmin.colegios.show');
    }

    public function test_superadmin_puede_editar_colegio(): void
    {
        $response = $this->actingAs($this->superadminUser)
            ->get(route('superadmin.colegios.edit', $this->colegio));
        $response->assertOk();
    }

    public function test_superadmin_puede_actualizar_colegio(): void
    {
        $response = $this->actingAs($this->superadminUser)
            ->put(route('superadmin.colegios.update', $this->colegio), [
                'nombre' => 'Colegio Actualizado',
                'activo' => true,
            ]);

        $response->assertRedirect(route('superadmin.colegios.show', $this->colegio));
        $this->assertDatabaseHas('colegios', ['id' => $this->colegio->id, 'nombre' => 'Colegio Actualizado']);
    }

    public function test_superadmin_puede_toggle_activo_colegio(): void
    {
        $this->assertTrue($this->colegio->activo);

        $response = $this->actingAs($this->superadminUser)
            ->patch(route('superadmin.colegios.toggle', $this->colegio));

        $response->assertRedirect();
        $this->assertFalse($this->colegio->fresh()->activo);
    }

    public function test_superadmin_puede_cambiar_plan_colegio(): void
    {
        $planPremium = Plan::create([
            'nombre' => 'Premium',
            'slug' => 'premium',
            'precio_mensual' => 55,
            'precio_anual' => 550,
            'max_alumnos' => 1000,
            'caracteristicas' => ['Todo incluido'],
            'activo' => true,
            'orden' => 3,
        ]);

        $response = $this->actingAs($this->superadminUser)
            ->post(route('superadmin.colegios.cambiarPlan', $this->colegio), [
                'plan_id' => $planPremium->id,
                'ciclo' => 'anual',
            ]);

        $response->assertRedirect();
        $this->colegio->refresh();
        $this->assertEquals('premium', $this->colegio->plan);

        // La suscripción anterior debe estar cancelada
        $this->assertEquals('cancelada', $this->suscripcion->fresh()->estado);

        // Debe existir una nueva suscripción activa
        $nuevaSuscripcion = $this->colegio->suscripciones()->where('estado', 'activa')->first();
        $this->assertNotNull($nuevaSuscripcion);
        $this->assertEquals($planPremium->id, $nuevaSuscripcion->plan_id);
    }

    // ── Planes CRUD ────────────────────────────────────

    public function test_superadmin_puede_listar_planes(): void
    {
        $response = $this->actingAs($this->superadminUser)->get(route('superadmin.planes.index'));
        $response->assertOk();
        $response->assertViewIs('superadmin.planes.index');
    }

    public function test_superadmin_puede_crear_plan(): void
    {
        $response = $this->actingAs($this->superadminUser)->post(route('superadmin.planes.store'), [
            'nombre' => 'Estándar',
            'slug' => 'estandar',
            'precio_mensual' => 35,
            'precio_anual' => 350,
            'max_alumnos' => 300,
            'caracteristicas' => "Gestión académica\nAula virtual",
            'orden' => 2,
        ]);

        $response->assertRedirect(route('superadmin.planes.index'));
        $this->assertDatabaseHas('planes', ['slug' => 'estandar', 'max_alumnos' => 300]);
    }

    public function test_superadmin_puede_editar_plan(): void
    {
        $response = $this->actingAs($this->superadminUser)
            ->get(route('superadmin.planes.edit', $this->plan));
        $response->assertOk();
    }

    public function test_superadmin_puede_actualizar_plan(): void
    {
        $response = $this->actingAs($this->superadminUser)
            ->put(route('superadmin.planes.update', $this->plan), [
                'nombre' => 'Básico Plus',
                'precio_mensual' => 25,
                'precio_anual' => 250,
                'max_alumnos' => 150,
                'caracteristicas' => "Mejoras incluidas",
                'orden' => 1,
                'activo' => true,
            ]);

        $response->assertRedirect(route('superadmin.planes.index'));
        $this->assertDatabaseHas('planes', ['id' => $this->plan->id, 'nombre' => 'Básico Plus']);
    }

    // ── Control de acceso ──────────────────────────────

    public function test_admin_no_puede_acceder_rutas_superadmin(): void
    {
        $routes = [
            ['GET', route('superadmin.colegios.index')],
            ['GET', route('superadmin.colegios.create')],
            ['GET', route('superadmin.planes.index')],
            ['GET', route('superadmin.planes.create')],
        ];

        foreach ($routes as [$method, $url]) {
            $response = $this->actingAs($this->adminUser)->call($method, $url);
            $this->assertEquals(403, $response->status(), "Admin no debería acceder a: {$url}");
        }
    }
}
