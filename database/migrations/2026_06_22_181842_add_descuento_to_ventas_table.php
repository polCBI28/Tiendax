<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->enum('descuento_tipo', ['monto', 'porcentaje'])->nullable()->after('total');
            $table->decimal('descuento_valor', 10, 2)->default(0)->after('descuento_tipo');
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn(['descuento_tipo', 'descuento_valor']);
        });
    }
};
