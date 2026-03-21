<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bimestres / Unidades de evaluación
        Schema::create('bimestres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->foreignId('periodo_id')->constrained('periodos')->cascadeOnDelete();
            $table->string('nombre'); // "Bimestre I", "Trimestre I"
            $table->integer('numero'); // 1, 2, 3, 4
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->timestamps();

            $table->index(['colegio_id', 'periodo_id']);
        });

        // Notas
        Schema::create('notas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->foreignId('matricula_id')->constrained('matriculas')->cascadeOnDelete();
            $table->foreignId('curso_seccion_id')->constrained('curso_seccion')->cascadeOnDelete();
            $table->foreignId('bimestre_id')->constrained('bimestres')->cascadeOnDelete();
            $table->decimal('nota', 5, 2)->nullable();
            $table->string('nota_letra', 5)->nullable(); // AD, A, B, C
            $table->text('observacion')->nullable();
            $table->timestamps();

            $table->unique(['matricula_id', 'curso_seccion_id', 'bimestre_id'], 'nota_unica');
            $table->index('colegio_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notas');
        Schema::dropIfExists('bimestres');
    }
};
