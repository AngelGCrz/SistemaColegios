<?php

namespace Tests;

use App\Models\Alumno;
use App\Models\Bimestre;
use App\Models\Colegio;
use App\Models\ConceptoPago;
use App\Models\Curso;
use App\Models\CursoSeccion;
use App\Models\Docente;
use App\Models\Grado;
use App\Models\Matricula;
use App\Models\Nivel;
use App\Models\Padre;
use App\Models\Periodo;
use App\Models\Plan;
use App\Models\Seccion;
use App\Models\Suscripcion;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

trait CreaDatosDePrueba
{
    protected Colegio $colegio;
    protected User $superadminUser;
    protected User $adminUser;
    protected User $docenteUser;
    protected User $alumnoUser;
    protected User $padreUser;
    protected Alumno $alumno;
    protected Docente $docente;
    protected Padre $padre;
    protected Periodo $periodo;
    protected Nivel $nivel;
    protected Grado $grado;
    protected Seccion $seccion;
    protected Curso $curso;
    protected CursoSeccion $cursoSeccion;
    protected Bimestre $bimestre;
    protected Matricula $matricula;
    protected Plan $plan;
    protected Suscripcion $suscripcion;

    protected function crearDatosDePrueba(): void
    {
        // Plan y superadmin
        $this->plan = Plan::create([
            'nombre' => 'Básico',
            'slug' => 'basico',
            'precio_mensual' => 19,
            'precio_anual' => 190,
            'max_alumnos' => 100,
            'caracteristicas' => ['Gestión académica', 'Control de asistencia'],
            'activo' => true,
            'orden' => 1,
        ]);

        $this->superadminUser = User::create([
            'nombre' => 'Super',
            'apellidos' => 'Admin',
            'email' => 'superadmin@test.com',
            'password' => Hash::make('password'),
            'rol' => 'superadmin',
            'activo' => true,
        ]);

        $this->colegio = Colegio::create([
            'nombre' => 'Colegio Test',
            'subdominio' => 'colegio-test',
            'activo' => true,
            'fecha_vencimiento' => now()->addYear(),
            'plan' => 'basico',
        ]);

        $this->suscripcion = Suscripcion::create([
            'colegio_id' => $this->colegio->id,
            'plan_id' => $this->plan->id,
            'estado' => 'activa',
            'ciclo' => 'anual',
            'fecha_inicio' => now(),
            'fecha_fin' => now()->addYear(),
            'monto' => 190,
        ]);

        $c = $this->colegio->id;

        $this->adminUser = User::create([
            'colegio_id' => $c,
            'nombre' => 'Admin',
            'apellidos' => 'Test',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'rol' => 'admin',
            'activo' => true,
        ]);

        $this->docenteUser = User::create([
            'colegio_id' => $c,
            'nombre' => 'Docente',
            'apellidos' => 'Test',
            'email' => 'docente@test.com',
            'password' => Hash::make('password'),
            'rol' => 'docente',
            'activo' => true,
        ]);
        $this->docente = Docente::create([
            'colegio_id' => $c,
            'user_id' => $this->docenteUser->id,
            'especialidad' => 'Matemáticas',
        ]);

        $this->alumnoUser = User::create([
            'colegio_id' => $c,
            'nombre' => 'Alumno',
            'apellidos' => 'Test',
            'email' => 'alumno@test.com',
            'password' => Hash::make('password'),
            'rol' => 'alumno',
            'activo' => true,
        ]);
        $this->alumno = Alumno::create([
            'colegio_id' => $c,
            'user_id' => $this->alumnoUser->id,
            'codigo_alumno' => 'ALU001',
        ]);

        $this->padreUser = User::create([
            'colegio_id' => $c,
            'nombre' => 'Padre',
            'apellidos' => 'Test',
            'email' => 'padre@test.com',
            'password' => Hash::make('password'),
            'rol' => 'padre',
            'activo' => true,
        ]);
        $this->padre = Padre::create([
            'colegio_id' => $c,
            'user_id' => $this->padreUser->id,
        ]);
        $this->padre->alumnos()->attach($this->alumno->id, ['colegio_id' => $c]);

        $this->periodo = Periodo::create([
            'colegio_id' => $c,
            'nombre' => 'Periodo 2026',
            'anio' => 2026,
            'fecha_inicio' => '2026-03-01',
            'fecha_fin' => '2026-12-15',
            'activo' => true,
        ]);

        $this->bimestre = Bimestre::create([
            'colegio_id' => $c,
            'periodo_id' => $this->periodo->id,
            'nombre' => 'I Bimestre',
            'numero' => 1,
            'fecha_inicio' => '2026-03-01',
            'fecha_fin' => '2026-05-31',
        ]);

        $this->nivel = Nivel::create([
            'colegio_id' => $c,
            'nombre' => 'Primaria',
        ]);

        $this->grado = Grado::create([
            'colegio_id' => $c,
            'nivel_id' => $this->nivel->id,
            'nombre' => '1er Grado',
            'orden' => 1,
        ]);

        $this->seccion = Seccion::create([
            'colegio_id' => $c,
            'grado_id' => $this->grado->id,
            'periodo_id' => $this->periodo->id,
            'nombre' => 'A',
            'capacidad' => 30,
        ]);

        $this->curso = Curso::create([
            'colegio_id' => $c,
            'nombre' => 'Matemáticas',
        ]);

        $this->cursoSeccion = CursoSeccion::create([
            'colegio_id' => $c,
            'curso_id' => $this->curso->id,
            'seccion_id' => $this->seccion->id,
            'docente_id' => $this->docente->id,
        ]);

        $this->matricula = Matricula::create([
            'colegio_id' => $c,
            'alumno_id' => $this->alumno->id,
            'seccion_id' => $this->seccion->id,
            'periodo_id' => $this->periodo->id,
            'estado' => 'activa',
            'fecha_matricula' => now(),
        ]);
    }

    protected function crearConceptoPago(string $nombre = 'Pensión', float $monto = 350.00): ConceptoPago
    {
        return ConceptoPago::create([
            'colegio_id' => $this->colegio->id,
            'nombre' => $nombre,
            'monto' => $monto,
        ]);
    }
}
