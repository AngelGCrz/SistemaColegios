<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tareas
        Schema::create('tareas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->foreignId('curso_seccion_id')->constrained('curso_seccion')->cascadeOnDelete();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('archivo_adjunto')->nullable();
            $table->dateTime('fecha_limite')->nullable();
            $table->decimal('puntaje_maximo', 5, 2)->default(20);
            $table->boolean('publicada')->default(false);
            $table->timestamps();

            $table->index(['colegio_id', 'curso_seccion_id']);
        });

        // Entregas de tareas
        Schema::create('entregas_tareas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->foreignId('tarea_id')->constrained('tareas')->cascadeOnDelete();
            $table->foreignId('alumno_id')->constrained('alumnos')->cascadeOnDelete();
            $table->text('contenido')->nullable();
            $table->string('archivo')->nullable();
            $table->decimal('calificacion', 5, 2)->nullable();
            $table->text('comentario_docente')->nullable();
            $table->dateTime('fecha_entrega');
            $table->timestamps();

            $table->unique(['tarea_id', 'alumno_id']);
            $table->index('colegio_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entregas_tareas');
        Schema::dropIfExists('tareas');
    }
};
