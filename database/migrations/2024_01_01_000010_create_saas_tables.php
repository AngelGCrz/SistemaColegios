<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Expandir enum 'rol' para incluir 'superadmin'
        DB::statement("ALTER TABLE users MODIFY COLUMN rol ENUM('superadmin','admin','docente','alumno','padre') NOT NULL");

        // 2) Tabla de planes de suscripción
        Schema::create('planes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50);              // basico, estandar, premium
            $table->string('slug', 30)->unique();       // basico, estandar, premium
            $table->decimal('precio_mensual', 8, 2);    // USD
            $table->decimal('precio_anual', 8, 2);      // USD (con descuento)
            $table->integer('max_alumnos');              // límite de alumnos
            $table->json('caracteristicas');             // features habilitadas
            $table->boolean('activo')->default(true);
            $table->integer('orden')->default(0);
            $table->timestamps();
        });

        // 3) Tabla de suscripciones
        Schema::create('suscripciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained('planes');
            $table->enum('estado', ['trial', 'activa', 'vencida', 'suspendida', 'cancelada'])->default('trial');
            $table->enum('ciclo', ['mensual', 'anual'])->default('mensual');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->date('trial_hasta')->nullable();
            $table->decimal('monto', 8, 2);
            $table->string('referencia_pago')->nullable(); // ID de MercadoPago
            $table->timestamps();

            $table->index(['colegio_id', 'estado']);
        });

        // 4) Historial de pagos de suscripción
        Schema::create('pagos_suscripcion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->foreignId('suscripcion_id')->constrained('suscripciones')->cascadeOnDelete();
            $table->decimal('monto', 8, 2);
            $table->string('moneda', 3)->default('USD');
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado', 'reembolsado'])->default('pendiente');
            $table->string('metodo_pago', 50)->nullable();  // mercadopago, paypal, transferencia
            $table->string('referencia_externa')->nullable(); // ID pago externo
            $table->json('metadata')->nullable();             // respuesta completa del gateway
            $table->timestamp('pagado_en')->nullable();
            $table->timestamps();

            $table->index(['colegio_id', 'estado']);
        });

        // 5) Agregar campos de suscripción al colegio
        Schema::table('colegios', function (Blueprint $table) {
            $table->string('subdominio', 50)->nullable()->unique()->after('logo');
            $table->string('contacto_nombre')->nullable()->after('fecha_vencimiento');
            $table->string('contacto_telefono', 20)->nullable()->after('contacto_nombre');
            $table->string('contacto_email')->nullable()->after('contacto_telefono');
        });
    }

    public function down(): void
    {
        Schema::table('colegios', function (Blueprint $table) {
            $table->dropColumn(['subdominio', 'contacto_nombre', 'contacto_telefono', 'contacto_email']);
        });

        Schema::dropIfExists('pagos_suscripcion');
        Schema::dropIfExists('suscripciones');
        Schema::dropIfExists('planes');

        DB::statement("ALTER TABLE users MODIFY COLUMN rol ENUM('admin','docente','alumno','padre') NOT NULL");
    }
};
