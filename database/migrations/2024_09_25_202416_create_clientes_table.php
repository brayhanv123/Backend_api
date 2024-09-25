<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_clientes', function (Blueprint $table) {
            $table->id('id_clie');
            $table->string('nombre_clie', 100);
            $table->string('direccion_clie', 100);
            $table->string('telefono_clie', 15);
            $table->tinyInteger('status_clie')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_clientes');
    }
};
