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
        Schema::create('tb_proveedores', function (Blueprint $table) {
            $table->id('id_prov'); //id del proveedor
            $table->string('nombre_prov', 100); //nombre del proveedor
            $table->string('direccion_prov', 45); //direccion del proveedor
            $table->string('telefono_prov', 15); // telefono del proveedor
            $table->tinyInteger('status_prov')->default(1); //estado del proveedor
            $table->timestamps(); // marca de tiempo para crear y actualizar
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_proveedores');
    }
};
