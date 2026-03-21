<?php

namespace Tests\Feature;

use App\Models\EntregaTarea;
use App\Models\Tarea;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\CreaDatosDePrueba;
use Tests\TestCase;

class TareaDocenteTest extends TestCase
{
    use RefreshDatabase, CreaDatosDePrueba;

    protected function setUp(): void
    {
        parent::setUp();
        $this->crearDatosDePrueba();
    }

    public function test_docente_ve_lista_de_tareas(): void
    {
        Tarea::create([
            'colegio_id' => $this->colegio->id,
            'curso_seccion_id' => $this->cursoSeccion->id,
            'titulo' => 'Tarea de prueba',
            'puntaje_maximo' => 20,
            'publicada' => true,
        ]);

        $this->actingAs($this->docenteUser)
            ->get("/docente/tareas/{$this->cursoSeccion->id}")
            ->assertStatus(200)
            ->assertSee('Tarea de prueba');
    }

    public function test_docente_ve_formulario_crear_tarea(): void
    {
        $this->actingAs($this->docenteUser)
            ->get("/docente/tareas/{$this->cursoSeccion->id}/create")
            ->assertStatus(200)
            ->assertSee('Nueva Tarea');
    }

    public function test_docente_crea_tarea_sin_archivo(): void
    {
        $response = $this->actingAs($this->docenteUser)
            ->post("/docente/tareas/{$this->cursoSeccion->id}", [
                'titulo' => 'Ecuaciones cuadráticas',
                'descripcion' => 'Resolver los ejercicios 1 al 10',
                'fecha_limite' => '2026-04-15',
                'puntaje_maximo' => 20,
                'publicada' => '1',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('tareas', [
            'titulo' => 'Ecuaciones cuadráticas',
            'curso_seccion_id' => $this->cursoSeccion->id,
            'puntaje_maximo' => 20,
            'publicada' => true,
        ]);
    }

    public function test_docente_crea_tarea_con_archivo(): void
    {
        Storage::fake('local');

        $response = $this->actingAs($this->docenteUser)
            ->post("/docente/tareas/{$this->cursoSeccion->id}", [
                'titulo' => 'Tarea con PDF',
                'puntaje_maximo' => 15,
                'archivo_adjunto' => UploadedFile::fake()->create('ejercicios.pdf', 500, 'application/pdf'),
            ]);

        $response->assertRedirect();

        $tarea = Tarea::where('titulo', 'Tarea con PDF')->first();
        $this->assertNotNull($tarea);
        $this->assertNotNull($tarea->archivo_adjunto);
        Storage::disk('local')->assertExists($tarea->archivo_adjunto);
    }

    public function test_docente_edita_tarea(): void
    {
        $tarea = Tarea::create([
            'colegio_id' => $this->colegio->id,
            'curso_seccion_id' => $this->cursoSeccion->id,
            'titulo' => 'Título original',
            'puntaje_maximo' => 20,
            'publicada' => false,
        ]);

        $this->actingAs($this->docenteUser)
            ->get("/docente/tareas/{$this->cursoSeccion->id}/{$tarea->id}/edit")
            ->assertStatus(200)
            ->assertSee('Título original');

        $response = $this->actingAs($this->docenteUser)
            ->put("/docente/tareas/{$this->cursoSeccion->id}/{$tarea->id}", [
                'titulo' => 'Título actualizado',
                'puntaje_maximo' => 25,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tareas', [
            'id' => $tarea->id,
            'titulo' => 'Título actualizado',
            'puntaje_maximo' => 25,
        ]);
    }

    public function test_docente_elimina_tarea(): void
    {
        $tarea = Tarea::create([
            'colegio_id' => $this->colegio->id,
            'curso_seccion_id' => $this->cursoSeccion->id,
            'titulo' => 'Tarea a eliminar',
            'puntaje_maximo' => 20,
        ]);

        $response = $this->actingAs($this->docenteUser)
            ->delete("/docente/tareas/{$this->cursoSeccion->id}/{$tarea->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('tareas', ['id' => $tarea->id]);
    }

    public function test_docente_publica_despublica_tarea(): void
    {
        $tarea = Tarea::create([
            'colegio_id' => $this->colegio->id,
            'curso_seccion_id' => $this->cursoSeccion->id,
            'titulo' => 'Tarea toggle',
            'puntaje_maximo' => 20,
            'publicada' => false,
        ]);

        $response = $this->actingAs($this->docenteUser)
            ->patch("/docente/tareas/{$this->cursoSeccion->id}/{$tarea->id}/publicar");

        $response->assertRedirect();
        $this->assertTrue($tarea->fresh()->publicada);

        // Toggle back
        $this->actingAs($this->docenteUser)
            ->patch("/docente/tareas/{$this->cursoSeccion->id}/{$tarea->id}/publicar");

        $this->assertFalse($tarea->fresh()->publicada);
    }

    public function test_docente_ve_entregas(): void
    {
        $tarea = Tarea::create([
            'colegio_id' => $this->colegio->id,
            'curso_seccion_id' => $this->cursoSeccion->id,
            'titulo' => 'Tarea entregable',
            'puntaje_maximo' => 20,
            'publicada' => true,
        ]);

        EntregaTarea::create([
            'colegio_id' => $this->colegio->id,
            'tarea_id' => $tarea->id,
            'alumno_id' => $this->alumno->id,
            'contenido' => 'Mi respuesta',
            'fecha_entrega' => now(),
        ]);

        $this->actingAs($this->docenteUser)
            ->get("/docente/tareas/entregas/{$tarea->id}")
            ->assertStatus(200)
            ->assertSee('Test, Alumno');
    }

    public function test_docente_califica_entregas_en_lote(): void
    {
        $tarea = Tarea::create([
            'colegio_id' => $this->colegio->id,
            'curso_seccion_id' => $this->cursoSeccion->id,
            'titulo' => 'Tarea calificable',
            'puntaje_maximo' => 20,
            'publicada' => true,
        ]);

        $entrega = EntregaTarea::create([
            'colegio_id' => $this->colegio->id,
            'tarea_id' => $tarea->id,
            'alumno_id' => $this->alumno->id,
            'contenido' => 'Respuesta alumno',
            'fecha_entrega' => now(),
        ]);

        $response = $this->actingAs($this->docenteUser)
            ->post("/docente/tareas/calificar/{$tarea->id}", [
                'entregas' => [
                    $entrega->id => [
                        'calificacion' => 18,
                        'comentario' => 'Buen trabajo',
                    ],
                ],
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('entregas_tareas', [
            'id' => $entrega->id,
            'calificacion' => 18,
            'comentario_docente' => 'Buen trabajo',
        ]);
    }

    public function test_otro_docente_no_puede_ver_tareas_ajenas(): void
    {
        $otroDocente = \App\Models\User::create([
            'colegio_id' => $this->colegio->id,
            'nombre' => 'Otro',
            'apellidos' => 'Docente',
            'email' => 'otro@test.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'rol' => 'docente',
            'activo' => true,
        ]);
        \App\Models\Docente::create([
            'colegio_id' => $this->colegio->id,
            'user_id' => $otroDocente->id,
            'especialidad' => 'Ciencias',
        ]);

        $this->actingAs($otroDocente)
            ->get("/docente/tareas/{$this->cursoSeccion->id}")
            ->assertStatus(403);
    }
}
