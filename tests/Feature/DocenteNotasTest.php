<?php

namespace Tests\Feature;

use App\Models\Nota;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreaDatosDePrueba;
use Tests\TestCase;

class DocenteNotasTest extends TestCase
{
    use RefreshDatabase, CreaDatosDePrueba;

    protected function setUp(): void
    {
        parent::setUp();
        $this->crearDatosDePrueba();
    }

    public function test_docente_ve_selector_de_cursos(): void
    {
        $this->actingAs($this->docenteUser)
            ->get('/docente/notas')
            ->assertStatus(200);
    }

    public function test_docente_ve_planilla_de_notas(): void
    {
        $this->actingAs($this->docenteUser)
            ->get("/docente/notas/{$this->cursoSeccion->id}/{$this->bimestre->id}")
            ->assertStatus(200)
            ->assertSee('Test, Alumno');
    }

    public function test_docente_puede_guardar_notas(): void
    {
        $response = $this->actingAs($this->docenteUser)
            ->post("/docente/notas/{$this->cursoSeccion->id}/{$this->bimestre->id}", [
                'notas' => [
                    ['matricula_id' => $this->matricula->id, 'nota' => 18],
                ],
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('notas', [
            'matricula_id' => $this->matricula->id,
            'curso_seccion_id' => $this->cursoSeccion->id,
            'bimestre_id' => $this->bimestre->id,
            'nota' => 18,
        ]);
    }

    public function test_nota_genera_nota_letra_correcta(): void
    {
        $this->actingAs($this->docenteUser)
            ->post("/docente/notas/{$this->cursoSeccion->id}/{$this->bimestre->id}", [
                'notas' => [
                    ['matricula_id' => $this->matricula->id, 'nota' => 18],
                ],
            ]);

        $nota = Nota::where('matricula_id', $this->matricula->id)->first();
        $this->assertEquals('AD', $nota->nota_letra);
    }

    public function test_docente_no_puede_ver_planilla_de_otro_docente(): void
    {
        // Crear otro docente con otro curso
        $otroDocente = \App\Models\User::create([
            'colegio_id' => $this->colegio->id,
            'nombre' => 'Otro',
            'apellidos' => 'Docente',
            'email' => 'otro.docente@test.com',
            'password' => bcrypt('password'),
            'rol' => 'docente',
            'activo' => true,
        ]);

        \App\Models\Docente::create([
            'colegio_id' => $this->colegio->id,
            'user_id' => $otroDocente->id,
            'especialidad' => 'Ciencias',
        ]);

        $this->actingAs($otroDocente)
            ->get("/docente/notas/{$this->cursoSeccion->id}/{$this->bimestre->id}")
            ->assertStatus(403);
    }
}
