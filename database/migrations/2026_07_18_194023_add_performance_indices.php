<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->index('estado');
        });

        Schema::table('ventas', function (Blueprint $table) {
            $table->index('fecha_venta');
            $table->index('estado');
        });

        Schema::table('movimientos', function (Blueprint $table) {
            $table->index('fecha');
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropIndex(['estado']);
        });

        Schema::table('ventas', function (Blueprint $table) {
            $table->dropIndex(['fecha_venta']);
            $table->dropIndex(['estado']);
        });

        Schema::table('movimientos', function (Blueprint $table) {
            $table->dropIndex(['fecha']);
        });
    }
};
