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
                Schema::create('tb_detalle_compras', function (Blueprint $table) {
                    $table->id('id_detalle_comp');
                    $table->foreignId('id_comp')->constrained('tb_compras', 'id_comp');
                    $table->foreignId('id_prod')->constrained('tb_productos', 'id_prod');
                    $table->integer('cantidad_comp');
                    $table->decimal('precio_comp',10,2);
                    $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_detalle_compras');
    }
};
