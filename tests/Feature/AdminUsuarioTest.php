<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreaDatosDePrueba;
use Tests\TestCase;

class AdminUsuarioTest extends TestCase
{
    use RefreshDatabase, CreaDatosDePrueba;

    protected function setUp(): void
    {
        parent::setUp();
        $this->crearDatosDePrueba();
    }

    public function test_admin_ve_lista_de_usuarios(): void
    {
        $this->actingAs($this->adminUser)
            ->get('/admin/usuarios')
            ->assertStatus(200)
            ->assertSee('admin@test.com');
    }

    public function test_admin_ve_formulario_crear_usuario(): void
    {
        $this->actingAs($this->adminUser)
            ->get('/admin/usuarios/create')
            ->assertStatus(200);
    }

    public function test_admin_puede_crear_usuario_docente(): void
    {
        $response = $this->actingAs($this->adminUser)->post('/admin/usuarios', [
            'nombre' => 'Nuevo',
            'apellidos' => 'Docente',
            'email' => 'nuevo.docente@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'rol' => 'docente',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'nuevo.docente@test.com',
            'rol' => 'docente',
            'colegio_id' => $this->colegio->id,
        ]);
        $user = User::where('email', 'nuevo.docente@test.com')->first();
        $this->assertNotNull($user->docente);
    }

    public function test_admin_puede_crear_usuario_alumno(): void
    {
        $response = $this->actingAs($this->adminUser)->post('/admin/usuarios', [
            'nombre' => 'Nuevo',
            'apellidos' => 'Alumno',
            'email' => 'nuevo.alumno@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'rol' => 'alumno',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'nuevo.alumno@test.com',
            'rol' => 'alumno',
        ]);
    }

    public function test_admin_puede_editar_usuario(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->put("/admin/usuarios/{$this->docenteUser->id}", [
                'nombre' => 'Editado',
                'apellidos' => 'Test',
                'email' => 'docente@test.com',
                'rol' => 'docente',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $this->docenteUser->id,
            'nombre' => 'Editado',
        ]);
    }

    public function test_admin_puede_eliminar_usuario(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->delete("/admin/usuarios/{$this->docenteUser->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('users', [
            'id' => $this->docenteUser->id,
        ]);
    }

    public function test_crear_usuario_con_email_duplicado_falla(): void
    {
        $response = $this->actingAs($this->adminUser)->post('/admin/usuarios', [
            'nombre' => 'Otro',
            'apellidos' => 'Admin',
            'email' => 'admin@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'rol' => 'admin',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_crear_usuario_sin_nombre_falla(): void
    {
        $response = $this->actingAs($this->adminUser)->post('/admin/usuarios', [
            'nombre' => '',
            'apellidos' => 'Test',
            'email' => 'fail@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'rol' => 'admin',
        ]);

        $response->assertSessionHasErrors('nombre');
    }
}
