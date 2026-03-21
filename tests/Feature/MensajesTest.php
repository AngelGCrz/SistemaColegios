<?php

namespace Tests\Feature;

use App\Models\Mensaje;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreaDatosDePrueba;
use Tests\TestCase;

class MensajesTest extends TestCase
{
    use RefreshDatabase, CreaDatosDePrueba;

    protected function setUp(): void
    {
        parent::setUp();
        $this->crearDatosDePrueba();
    }

    public function test_usuario_ve_bandeja_de_entrada(): void
    {
        $this->actingAs($this->docenteUser)
            ->get('/mensajes')
            ->assertStatus(200);
    }

    public function test_usuario_ve_mensajes_enviados(): void
    {
        $this->actingAs($this->docenteUser)
            ->get('/mensajes/enviados')
            ->assertStatus(200);
    }

    public function test_usuario_ve_formulario_crear_mensaje(): void
    {
        $this->actingAs($this->docenteUser)
            ->get('/mensajes/crear')
            ->assertStatus(200);
    }

    public function test_usuario_puede_enviar_mensaje(): void
    {
        $response = $this->actingAs($this->docenteUser)->post('/mensajes', [
            'destinatario_id' => $this->padreUser->id,
            'asunto' => 'Reunión de padres',
            'contenido' => 'Le informo sobre la reunión del viernes.',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('mensajes', [
            'remitente_id' => $this->docenteUser->id,
            'destinatario_id' => $this->padreUser->id,
            'asunto' => 'Reunión de padres',
        ]);
    }

    public function test_usuario_puede_ver_mensaje_recibido(): void
    {
        $mensaje = Mensaje::create([
            'colegio_id' => $this->colegio->id,
            'remitente_id' => $this->adminUser->id,
            'destinatario_id' => $this->docenteUser->id,
            'asunto' => 'Aviso importante',
            'contenido' => 'Contenido del aviso.',
            'leido' => false,
        ]);

        $this->actingAs($this->docenteUser)
            ->get("/mensajes/{$mensaje->id}")
            ->assertStatus(200)
            ->assertSee('Aviso importante');

        $mensaje->refresh();
        $this->assertTrue($mensaje->leido);
    }

    public function test_enviar_mensaje_sin_asunto_falla(): void
    {
        $response = $this->actingAs($this->docenteUser)->post('/mensajes', [
            'destinatario_id' => $this->padreUser->id,
            'contenido' => 'Sin asunto.',
        ]);

        $response->assertSessionHasErrors('asunto');
    }

    public function test_usuario_no_puede_ver_mensaje_ajeno(): void
    {
        $mensaje = Mensaje::create([
            'colegio_id' => $this->colegio->id,
            'remitente_id' => $this->adminUser->id,
            'destinatario_id' => $this->padreUser->id,
            'asunto' => 'Mensaje privado',
            'contenido' => 'Solo para el padre.',
            'leido' => false,
        ]);

        $this->actingAs($this->docenteUser)
            ->get("/mensajes/{$mensaje->id}")
            ->assertStatus(403);
    }
}
