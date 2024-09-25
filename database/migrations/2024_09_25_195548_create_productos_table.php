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
        Schema::create('tb_productos', function (Blueprint $table) {
            $table->id('id_prod');
            $table->string('nombre_prod');
            $table->text('descripcion_prod')->nullable();
            $table->decimal('precio_comp',10,2)->default(0.00);
            $table->decimal('precio_vent',10,2)->default(0.00); // Calculado automáticamente
            $table->integer('stock_prod')->default(0.00);
            $table->tinyInteger('status_prod')->default(1); //estado del producto
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_productos');
    }
};
