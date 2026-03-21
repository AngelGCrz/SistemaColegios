<?php

namespace Database\Seeders;

use App\Models\Alumno;
use App\Models\Asistencia;
use App\Models\Aviso;
use App\Models\Bimestre;
use App\Models\Colegio;
use App\Models\ConceptoPago;
use App\Models\Curso;
use App\Models\CursoSeccion;
use App\Models\Docente;
use App\Models\Grado;
use App\Models\Matricula;
use App\Models\Nivel;
use App\Models\Nota;
use App\Models\Padre;
use App\Models\Pago;
use App\Models\Periodo;
use App\Models\Seccion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Colegio demo ──
        $colegio = Colegio::create([
            'nombre' => 'Colegio Demo San Martín',
            'direccion' => 'Av. Educación 123, Lima',
            'telefono' => '01-234-5678',
            'email' => 'info@colegiosanmartin.edu.pe',
            'plan' => 'estandar',
            'activo' => true,
            'fecha_vencimiento' => now()->addYear(),
        ]);

        $c = $colegio->id;

        // ── Admin ──
        User::create([
            'colegio_id' => $c,
            'nombre' => 'Carlos',
            'apellidos' => 'Administrador',
            'email' => 'admin@demo.com',
            'password' => Hash::make('password'),
            'rol' => 'admin',
            'activo' => true,
        ]);

        // ── Docentes ──
        $docentesData = [
            ['María', 'García López', 'maria@demo.com'],
            ['Juan', 'Pérez Soto', 'juan@demo.com'],
            ['Ana', 'Rodríguez Díaz', 'ana@demo.com'],
        ];

        $docentes = [];
        foreach ($docentesData as $d) {
            $user = User::create([
                'colegio_id' => $c,
                'nombre' => $d[0],
                'apellidos' => $d[1],
                'email' => $d[2],
                'password' => Hash::make('password'),
                'rol' => 'docente',
                'activo' => true,
            ]);
            $docentes[] = Docente::create([
                'colegio_id' => $c,
                'user_id' => $user->id,
                'especialidad' => 'General',
            ]);
        }

        // ── Periodo y Bimestres ──
        $periodo = Periodo::create([
            'colegio_id' => $c,
            'nombre' => '2025',
            'anio' => 2025,
            'fecha_inicio' => '2025-03-01',
            'fecha_fin' => '2025-12-15',
            'activo' => true,
        ]);

        $bimestres = [];
        $bimestresData = [
            ['I Bimestre', '2025-03-01', '2025-05-15'],
            ['II Bimestre', '2025-05-16', '2025-07-25'],
            ['III Bimestre', '2025-08-11', '2025-10-17'],
            ['IV Bimestre', '2025-10-20', '2025-12-15'],
        ];
        foreach ($bimestresData as $idx => $b) {
            $bimestres[] = Bimestre::create([
                'colegio_id' => $c,
                'periodo_id' => $periodo->id,
                'nombre' => $b[0],
                'numero' => $idx + 1,
                'fecha_inicio' => $b[1],
                'fecha_fin' => $b[2],
            ]);
        }

        // ── Niveles, Grados, Secciones ──
        $primaria = Nivel::create(['colegio_id' => $c, 'nombre' => 'Primaria']);
        $secundaria = Nivel::create(['colegio_id' => $c, 'nombre' => 'Secundaria']);

        $grados = [];
        foreach (['1er Grado', '2do Grado', '3er Grado'] as $g) {
            $grados[] = Grado::create(['nivel_id' => $primaria->id, 'colegio_id' => $c, 'nombre' => $g]);
        }
        foreach (['1er Año', '2do Año'] as $g) {
            $grados[] = Grado::create(['nivel_id' => $secundaria->id, 'colegio_id' => $c, 'nombre' => $g]);
        }

        $secciones = [];
        foreach ($grados as $grado) {
            foreach (['A', 'B'] as $s) {
                $secciones[] = Seccion::create([
                    'grado_id' => $grado->id,
                    'colegio_id' => $c,
                    'periodo_id' => $periodo->id,
                    'nombre' => $s,
                    'capacidad' => 30,
                ]);
            }
        }

        // ── Cursos ──
        $cursosData = ['Matemáticas', 'Comunicación', 'Ciencias', 'Historia', 'Inglés', 'Arte'];
        $cursos = [];
        foreach ($cursosData as $nombre) {
            $cursos[] = Curso::create(['colegio_id' => $c, 'nombre' => $nombre]);
        }

        // ── Asignaciones (primeras 2 secciones × 6 cursos) ──
        $asignaciones = [];
        $docenteIndex = 0;
        foreach (array_slice($secciones, 0, 2) as $seccion) {
            foreach ($cursos as $curso) {
                $asignaciones[] = CursoSeccion::create([
                    'colegio_id' => $c,
                    'curso_id' => $curso->id,
                    'seccion_id' => $seccion->id,
                    'docente_id' => $docentes[$docenteIndex % count($docentes)]->id,
                ]);
                $docenteIndex++;
            }
        }

        // ── Alumnos (10 en sección[0], 8 en sección[1]) ──
        $nombresAlumnos = [
            ['Diego', 'Torres Mendoza'], ['Valeria', 'Castillo Ruiz'], ['Andrés', 'Morales Vega'],
            ['Camila', 'Jiménez Flores'], ['Sebastián', 'Herrera Lima'], ['Isabella', 'Ramos Cruz'],
            ['Mateo', 'Vargas Paredes'], ['Sofía', 'Mendoza Ríos'], ['Lucas', 'Salazar Gutiérrez'],
            ['Martina', 'Ortiz Campos'], ['Emilio', 'Navarro Peña'], ['Renata', 'Delgado Soto'],
            ['Nicolás', 'Quispe Huamán'], ['Valentina', 'Espinoza Torres'], ['Daniel', 'Chávez Medina'],
            ['Luciana', 'Rojas Paredes'], ['Alejandro', 'Bravo López'], ['Gabriela', 'Ponce Silva'],
        ];

        $alumnos = [];
        $matriculas = [];

        foreach ($nombresAlumnos as $i => $n) {
            $email = strtolower($n[0]) . ($i + 1) . '@demo.com';
            $user = User::create([
                'colegio_id' => $c,
                'nombre' => $n[0],
                'apellidos' => $n[1],
                'email' => $email,
                'password' => Hash::make('password'),
                'rol' => 'alumno',
                'activo' => true,
            ]);
            $alumno = Alumno::create([
                'colegio_id' => $c,
                'user_id' => $user->id,
                'codigo_alumno' => 'ALU-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'fecha_nacimiento' => fake()->dateTimeBetween('-14 years', '-6 years')->format('Y-m-d'),
            ]);
            $alumnos[] = $alumno;

            $seccionIndex = $i < 10 ? 0 : 1;
            $matriculas[] = Matricula::create([
                'colegio_id' => $c,
                'alumno_id' => $alumno->id,
                'periodo_id' => $periodo->id,
                'seccion_id' => $secciones[$seccionIndex]->id,
                'estado' => 'activa',
                'fecha_matricula' => '2025-03-01',
            ]);
        }

        // ── Padres (vinculados a los primeros 6 alumnos) ──
        $padresData = [
            ['Roberto', 'Torres Medina', 'roberto@demo.com', [0]],
            ['Carmen', 'Castillo Vega', 'carmen@demo.com', [1]],
            ['Pedro', 'Morales Flores', 'pedro@demo.com', [2, 3]],
            ['Laura', 'Herrera Soto', 'laura@demo.com', [4, 5]],
        ];

        foreach ($padresData as $p) {
            $user = User::create([
                'colegio_id' => $c,
                'nombre' => $p[0],
                'apellidos' => $p[1],
                'email' => $p[2],
                'password' => Hash::make('password'),
                'rol' => 'padre',
                'activo' => true,
            ]);
            $padre = Padre::create([
                'colegio_id' => $c,
                'user_id' => $user->id,
            ]);
            foreach ($p[3] as $alumnoIdx) {
                $padre->alumnos()->attach($alumnos[$alumnoIdx]->id, ['colegio_id' => $c]);
            }
        }

        // ── Notas (I Bimestre para sección[0]) ──
        foreach (array_slice($matriculas, 0, 10) as $matricula) {
            foreach (array_slice($asignaciones, 0, 6) as $asig) {
                $nota = fake()->numberBetween(8, 20);
                Nota::create([
                    'colegio_id' => $c,
                    'matricula_id' => $matricula->id,
                    'curso_seccion_id' => $asig->id,
                    'bimestre_id' => $bimestres[0]->id,
                    'nota' => $nota,
                    'nota_letra' => match (true) {
                        $nota >= 18 => 'AD',
                        $nota >= 14 => 'A',
                        $nota >= 11 => 'B',
                        default => 'C',
                    },
                ]);
            }
        }

        // ── Asistencia (últimos 5 días hábiles para sección[0]) ──
        $fechas = collect();
        $fecha = now();
        while ($fechas->count() < 5) {
            if ($fecha->isWeekday()) {
                $fechas->push($fecha->copy());
            }
            $fecha = $fecha->subDay();
        }

        $estados = ['presente', 'presente', 'presente', 'falta', 'tardanza', 'justificada'];
        foreach (array_slice($matriculas, 0, 10) as $matricula) {
            foreach ($fechas as $f) {
                Asistencia::create([
                    'colegio_id' => $c,
                    'matricula_id' => $matricula->id,
                    'seccion_id' => $secciones[0]->id,
                    'fecha' => $f->toDateString(),
                    'estado' => $estados[array_rand($estados)],
                ]);
            }
        }

        // ── Conceptos de pago y pagos ──
        $conceptos = [
            ConceptoPago::create(['colegio_id' => $c, 'nombre' => 'Matrícula 2025', 'monto' => 350]),
            ConceptoPago::create(['colegio_id' => $c, 'nombre' => 'Pensión Marzo', 'monto' => 450]),
            ConceptoPago::create(['colegio_id' => $c, 'nombre' => 'Pensión Abril', 'monto' => 450]),
        ];

        foreach (array_slice($alumnos, 0, 10) as $i => $alumno) {
            foreach ($conceptos as $j => $concepto) {
                $pagado = ($j === 0) || ($i < 5 && $j === 1);
                Pago::create([
                    'colegio_id' => $c,
                    'alumno_id' => $alumno->id,
                    'concepto_pago_id' => $concepto->id,
                    'periodo_id' => $periodo->id,
                    'monto' => $concepto->monto,
                    'estado' => $pagado ? 'pagado' : 'pendiente',
                    'fecha_pago' => $pagado ? now()->subDays(rand(1, 30)) : null,
                ]);
            }
        }

        // ── Avisos ──
        Aviso::create([
            'colegio_id' => $c,
            'user_id' => User::where('colegio_id', $c)->where('rol', 'admin')->first()->id,
            'titulo' => 'Bienvenidos al año escolar 2025',
            'contenido' => 'Les damos la bienvenida a un nuevo año escolar. Recuerden que las clases inician el 3 de marzo. ¡Éxitos!',
            'destinatario' => 'todos',
        ]);

        Aviso::create([
            'colegio_id' => $c,
            'user_id' => User::where('colegio_id', $c)->where('rol', 'admin')->first()->id,
            'titulo' => 'Reunión de padres - Marzo',
            'contenido' => 'Se convoca a reunión general de padres de familia para el viernes 14 de marzo a las 5:00 PM.',
            'destinatario' => 'padres',
        ]);

        // ── Resumen ──
        $this->command->info('✅ Seeder completado exitosamente:');
        $this->command->info('   Colegio: ' . $colegio->nombre);
        $this->command->info('   Admin: admin@demo.com / password');
        $this->command->info('   Docente: maria@demo.com / password');
        $this->command->info('   Alumno: diego1@demo.com / password');
        $this->command->info('   Padre: roberto@demo.com / password');
        $this->command->info('   18 alumnos, 3 docentes, 4 padres');
        $this->command->info('   6 cursos, 10 secciones, 12 asignaciones');
    }
}
