<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->foreignId('matricula_id')->constrained('matriculas')->cascadeOnDelete();
            $table->foreignId('seccion_id')->constrained('secciones')->cascadeOnDelete();
            $table->date('fecha');
            $table->enum('estado', ['presente', 'falta', 'tardanza', 'justificada'])->default('presente');
            $table->text('observacion')->nullable();
            $table->timestamps();

            $table->unique(['matricula_id', 'fecha'], 'asistencia_unica');
            $table->index(['colegio_id', 'fecha']);
            $table->index(['seccion_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};
