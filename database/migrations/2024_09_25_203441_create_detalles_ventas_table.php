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
        Schema::create('tb_detalle_ventas', function (Blueprint $table) {
            $table->id('id_detalle_vent');
            $table->foreignId('id_vent')->constrained('tb_ventas','id_vent');
            $table->foreignId('id_prod')->constrained('tb_productos','id_prod');
            $table->integer('cantidad_vent');
            $table->decimal('precio_vent', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_detalle_ventas');
    }
};
