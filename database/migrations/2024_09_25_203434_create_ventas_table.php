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
        Schema::create('tb_ventas', function (Blueprint $table) {
            $table->id('id_vent');
            $table->decimal('total_vent', 10, 2);
            $table->dateTime('fecha_vent');
            $table->tinyInteger('status_vent')->default(1);
            $table->foreignId('id_clie')->constrained('tb_clientes', 'id_clie');
            $table->timestamps();
        });;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_ventas');
    }
};