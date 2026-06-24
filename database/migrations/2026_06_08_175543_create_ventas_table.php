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
    Schema::create('ventas', function (Blueprint $table) {
        $table->id();
        $table->foreignId('cliente_id')->nullable()->constrained('clientes')->onDelete('set null');
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->string('numero_boleta')->unique();
        $table->date('fecha_venta');
        $table->decimal('subtotal', 10, 2);
        $table->decimal('impuesto', 10, 2)->default(0);
        $table->decimal('total', 10, 2);
        $table->enum('estado', ['borrador', 'completado', 'pendiente', 'cancelado'])->default('completado');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
