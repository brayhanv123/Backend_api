<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory; // Habilita el uso de fábricas para crear instancias del modelo

    protected $table = 'tb_proveedores'; // Nombre de la tabla asociada en la base de datos

    // Indica que la clave primaria es `id_prov`
    protected $primaryKey = 'id_prov'; // Clave primaria del modelo

    // Atributos que se pueden asignar en masa
    protected $fillable = [
        'nombre_prov',    // Nombre del proveedor
        'direccion_prov', // Dirección del proveedor
        'telefono_prov',  // Teléfono del proveedor
    ];

    // Atributos por defecto al crear un nuevo registro
    protected $attributes = [
        'status_prov' => 1, // Establece el estado como activo por defecto (1)
    ];
}
