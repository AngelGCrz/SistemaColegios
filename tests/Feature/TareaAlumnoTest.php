<?php

namespace Tests\Feature;

use App\Models\EntregaTarea;
use App\Models\Tarea;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\CreaDatosDePrueba;
use Tests\TestCase;

class TareaAlumnoTest extends TestCase
{
    use RefreshDatabase, CreaDatosDePrueba;

    protected Tarea $tarea;

    protected function setUp(): void
    {
        parent::setUp();
        $this->crearDatosDePrueba();

        $this->tarea = Tarea::create([
            'colegio_id' => $this->colegio->id,
            'curso_seccion_id' => $this->cursoSeccion->id,
            'titulo' => 'Tarea visible',
            'descripcion' => 'Resolver ejercicios',
            'puntaje_maximo' => 20,
            'fecha_limite' => now()->addDays(7),
            'publicada' => true,
        ]);
    }

    public function test_alumno_ve_tareas_publicadas(): void
    {
        // Create unpublished task that should NOT show
        Tarea::create([
            'colegio_id' => $this->colegio->id,
            'curso_seccion_id' => $this->cursoSeccion->id,
            'titulo' => 'Tarea borrador',
            'puntaje_maximo' => 20,
            'publicada' => false,
        ]);

        $this->actingAs($this->alumnoUser)
            ->get('/alumno/tareas')
            ->assertStatus(200)
            ->assertSee('Tarea visible')
            ->assertDontSee('Tarea borrador');
    }

    public function test_alumno_entrega_tarea(): void
    {
        $response = $this->actingAs($this->alumnoUser)
            ->post("/alumno/tareas/{$this->tarea->id}/entregar", [
                'contenido' => 'Esta es mi respuesta',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('entregas_tareas', [
            'tarea_id' => $this->tarea->id,
            'alumno_id' => $this->alumno->id,
            'contenido' => 'Esta es mi respuesta',
        ]);
    }

    public function test_alumno_entrega_tarea_con_archivo(): void
    {
        Storage::fake('local');

        $response = $this->actingAs($this->alumnoUser)
            ->post("/alumno/tareas/{$this->tarea->id}/entregar", [
                'contenido' => 'Adjunto mi trabajo',
                'archivo' => UploadedFile::fake()->create('trabajo.pdf', 200, 'application/pdf'),
            ]);

        $response->assertRedirect();

        $entrega = EntregaTarea::where('tarea_id', $this->tarea->id)
            ->where('alumno_id', $this->alumno->id)
            ->first();

        $this->assertNotNull($entrega);
        $this->assertNotNull($entrega->archivo);
        Storage::disk('local')->assertExists($entrega->archivo);
    }

    public function test_alumno_ve_calificacion_en_tareas(): void
    {
        EntregaTarea::create([
            'colegio_id' => $this->colegio->id,
            'tarea_id' => $this->tarea->id,
            'alumno_id' => $this->alumno->id,
            'contenido' => 'Mi respuesta',
            'calificacion' => 18,
            'comentario_docente' => 'Excelente',
            'fecha_entrega' => now(),
        ]);

        $this->actingAs($this->alumnoUser)
            ->get('/alumno/tareas')
            ->assertStatus(200)
            ->assertSee('Calificada: 18');
    }

    public function test_alumno_ve_calendario(): void
    {
        $this->actingAs($this->alumnoUser)
            ->get('/alumno/calendario')
            ->assertStatus(200)
            ->assertSee('Calendario de Tareas');
    }

    public function test_alumno_ve_calendario_con_tarea_en_mes(): void
    {
        $mes = $this->tarea->fecha_limite->month;
        $anio = $this->tarea->fecha_limite->year;

        $this->actingAs($this->alumnoUser)
            ->get("/alumno/calendario?mes={$mes}&anio={$anio}")
            ->assertStatus(200)
            ->assertSee('Tarea visible');
    }

    public function test_alumno_ve_historial_de_entregas(): void
    {
        EntregaTarea::create([
            'colegio_id' => $this->colegio->id,
            'tarea_id' => $this->tarea->id,
            'alumno_id' => $this->alumno->id,
            'contenido' => 'Mi respuesta',
            'calificacion' => 15,
            'fecha_entrega' => now(),
        ]);

        $this->actingAs($this->alumnoUser)
            ->get('/alumno/historial')
            ->assertStatus(200)
            ->assertSee('Tarea visible')
            ->assertSee('15');
    }

    public function test_alumno_puede_descargar_archivo_tarea(): void
    {
        Storage::fake('local');
        $path = 'tareas/' . $this->colegio->id . '/test.pdf';
        Storage::disk('local')->put($path, 'contenido PDF');

        $this->tarea->update(['archivo_adjunto' => $path]);

        $this->actingAs($this->alumnoUser)
            ->get("/archivo/tarea/{$this->tarea->id}")
            ->assertStatus(200);
    }

    public function test_alumno_puede_descargar_archivo_entrega(): void
    {
        Storage::fake('local');
        $path = 'entregas/' . $this->colegio->id . '/test.pdf';
        Storage::disk('local')->put($path, 'contenido PDF');

        $entrega = EntregaTarea::create([
            'colegio_id' => $this->colegio->id,
            'tarea_id' => $this->tarea->id,
            'alumno_id' => $this->alumno->id,
            'contenido' => 'Test',
            'archivo' => $path,
            'fecha_entrega' => now(),
        ]);

        $this->actingAs($this->alumnoUser)
            ->get("/archivo/entrega/{$entrega->id}")
            ->assertStatus(200);
    }
}
