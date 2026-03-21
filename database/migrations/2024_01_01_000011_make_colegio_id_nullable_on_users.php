<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Superadmin users don't belong to any colegio
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('colegio_id')->nullable()->change();
        });

        // Drop the composite unique to allow null colegio_id
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['colegio_id', 'email']);
            $table->unique('email');
        });

        // pagos_suscripcion.suscripcion_id may be null when payment is pending
        Schema::table('pagos_suscripcion', function (Blueprint $table) {
            $table->foreignId('suscripcion_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('pagos_suscripcion', function (Blueprint $table) {
            $table->foreignId('suscripcion_id')->nullable(false)->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['email']);
            $table->unique(['colegio_id', 'email']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('colegio_id')->nullable(false)->change();
        });
    }
};
