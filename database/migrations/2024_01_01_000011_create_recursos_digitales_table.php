<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recursos_digitales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->comment('Usuario que subió el recurso');
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->enum('tipo', ['documento', 'video', 'enlace', 'imagen', 'audio', 'otro'])->default('documento');
            $table->string('archivo_path')->nullable()->comment('Ruta del archivo subido');
            $table->string('archivo_nombre')->nullable();
            $table->string('url_externa')->nullable()->comment('Enlace externo (YouTube, etc.)');
            $table->string('materia')->nullable()->comment('Materia/curso relacionado');
            $table->string('nivel')->nullable()->comment('Nivel educativo: inicial, primaria, secundaria');
            $table->boolean('publico')->default(true)->comment('Visible para todos los usuarios del colegio');
            $table->unsignedInteger('descargas')->default(0);
            $table->timestamps();

            $table->index(['colegio_id', 'tipo']);
            $table->index(['colegio_id', 'materia']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recursos_digitales');
    }
};
