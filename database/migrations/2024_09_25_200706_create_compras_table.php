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
        Schema::create('tb_compras', function (Blueprint $table) {
            $table->id('id_comp');
            $table->dateTime('fecha_comp');
            $table->decimal('total_comp',10,2);
            $table->tinyInteger('status_comp')->default(1);
            $table->foreignId('id_prov')->constrained('tb_proveedores', 'id_prov');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_compras');
    }
};
