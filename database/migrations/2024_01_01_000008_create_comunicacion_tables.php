<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Avisos generales
        Schema::create('avisos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('titulo');
            $table->text('contenido');
            $table->enum('destinatario', ['todos', 'docentes', 'alumnos', 'padres'])->default('todos');
            $table->foreignId('seccion_id')->nullable()->constrained('secciones')->nullOnDelete();
            $table->boolean('publicado')->default(true);
            $table->timestamps();

            $table->index(['colegio_id', 'destinatario']);
        });

        // Mensajes internos
        Schema::create('mensajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->foreignId('remitente_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('destinatario_id')->constrained('users')->cascadeOnDelete();
            $table->string('asunto');
            $table->text('contenido');
            $table->boolean('leido')->default(false);
            $table->timestamp('leido_at')->nullable();
            $table->timestamps();

            $table->index(['colegio_id', 'destinatario_id', 'leido']);
            $table->index(['colegio_id', 'remitente_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mensajes');
        Schema::dropIfExists('avisos');
    }
};
