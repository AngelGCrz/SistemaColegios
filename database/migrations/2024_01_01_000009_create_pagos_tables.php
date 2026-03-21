<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Conceptos de pago
        Schema::create('conceptos_pago', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->string('nombre'); // Matrícula, Pensión Marzo, etc.
            $table->decimal('monto', 10, 2);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index('colegio_id');
        });

        // Pagos
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->foreignId('alumno_id')->constrained('alumnos')->cascadeOnDelete();
            $table->foreignId('concepto_pago_id')->constrained('conceptos_pago')->cascadeOnDelete();
            $table->foreignId('periodo_id')->constrained('periodos')->cascadeOnDelete();
            $table->decimal('monto', 10, 2);
            $table->enum('estado', ['pendiente', 'pagado', 'anulado'])->default('pendiente');
            $table->date('fecha_pago')->nullable();
            $table->string('metodo_pago', 50)->nullable(); // efectivo, transferencia, yape
            $table->string('numero_recibo')->nullable();
            $table->text('observacion')->nullable();
            $table->timestamps();

            $table->index(['colegio_id', 'alumno_id']);
            $table->index(['colegio_id', 'estado']);
            $table->index('periodo_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
        Schema::dropIfExists('conceptos_pago');
    }
};
