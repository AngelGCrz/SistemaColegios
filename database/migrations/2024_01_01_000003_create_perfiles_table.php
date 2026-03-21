<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Datos extendidos de alumnos
        Schema::create('alumnos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('codigo_alumno')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->enum('genero', ['M', 'F'])->nullable();
            $table->text('direccion')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index('colegio_id');
            $table->unique(['colegio_id', 'codigo_alumno']);
        });

        // Datos extendidos de docentes
        Schema::create('docentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('especialidad')->nullable();
            $table->timestamps();

            $table->index('colegio_id');
        });

        // Datos extendidos de padres
        Schema::create('padres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('ocupacion')->nullable();
            $table->timestamps();

            $table->index('colegio_id');
        });

        // Relación muchos a muchos: alumno <-> padre
        Schema::create('alumno_padre', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->foreignId('alumno_id')->constrained('alumnos')->cascadeOnDelete();
            $table->foreignId('padre_id')->constrained('padres')->cascadeOnDelete();
            $table->string('parentesco', 50)->default('padre'); // padre, madre, tutor

            $table->unique(['alumno_id', 'padre_id']);
            $table->index('colegio_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumno_padre');
        Schema::dropIfExists('padres');
        Schema::dropIfExists('docentes');
        Schema::dropIfExists('alumnos');
    }
};
