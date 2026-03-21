<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('colegios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('codigo_modular')->nullable();
            $table->string('direccion')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('logo')->nullable();
            $table->string('plan', 20)->default('basico'); // basico, profesional, premium
            $table->boolean('activo')->default(true);
            $table->date('fecha_vencimiento')->nullable();
            $table->timestamps();

            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('colegios');
    }
};
