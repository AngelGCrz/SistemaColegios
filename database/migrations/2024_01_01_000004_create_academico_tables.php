<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Periodos académicos (ej: 2025, 2026)
        Schema::create('periodos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->string('nombre'); // "2025", "2025-I"
            $table->year('anio');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['colegio_id', 'activo']);
        });

        // Niveles educativos (Inicial, Primaria, Secundaria)
        Schema::create('niveles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->string('nombre'); // Inicial, Primaria, Secundaria
            $table->integer('orden')->default(0);
            $table->timestamps();

            $table->index('colegio_id');
        });

        // Grados (1er grado, 2do grado...)
        Schema::create('grados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->foreignId('nivel_id')->constrained('niveles')->cascadeOnDelete();
            $table->string('nombre'); // "1er Grado", "2do Grado"
            $table->integer('orden')->default(0);
            $table->timestamps();

            $table->index('colegio_id');
        });

        // Secciones (A, B, C por grado)
        Schema::create('secciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->foreignId('grado_id')->constrained('grados')->cascadeOnDelete();
            $table->foreignId('periodo_id')->constrained('periodos')->cascadeOnDelete();
            $table->string('nombre'); // "A", "B"
            $table->integer('capacidad')->default(30);
            $table->timestamps();

            $table->index(['colegio_id', 'periodo_id']);
        });

        // Cursos / Asignaturas
        Schema::create('cursos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->string('nombre');
            $table->string('codigo', 20)->nullable();
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['colegio_id', 'activo']);
        });

        // Asignación de curso a sección con docente
        Schema::create('curso_seccion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->foreignId('curso_id')->constrained('cursos')->cascadeOnDelete();
            $table->foreignId('seccion_id')->constrained('secciones')->cascadeOnDelete();
            $table->foreignId('docente_id')->constrained('docentes')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['curso_id', 'seccion_id']);
            $table->index('colegio_id');
            $table->index('docente_id');
        });

        // Matrículas
        Schema::create('matriculas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->foreignId('alumno_id')->constrained('alumnos')->cascadeOnDelete();
            $table->foreignId('seccion_id')->constrained('secciones')->cascadeOnDelete();
            $table->foreignId('periodo_id')->constrained('periodos')->cascadeOnDelete();
            $table->enum('estado', ['activa', 'retirada', 'trasladada'])->default('activa');
            $table->date('fecha_matricula');
            $table->timestamps();

            $table->unique(['alumno_id', 'periodo_id']);
            $table->index(['colegio_id', 'periodo_id']);
            $table->index('seccion_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matriculas');
        Schema::dropIfExists('curso_seccion');
        Schema::dropIfExists('cursos');
        Schema::dropIfExists('secciones');
        Schema::dropIfExists('grados');
        Schema::dropIfExists('niveles');
        Schema::dropIfExists('periodos');
    }
};
