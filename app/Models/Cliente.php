<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;
    // Especificar el nombre de la tabla en la base de datos
    protected $table = 'tb_clientes';

    // Indica que la clave primaria es `id_prov`
    protected $primaryKey = 'id_clie';

    protected $fillable = [
        'nombre_clie',
        'direccion_clie',
        'telefono_clie',
    ];

    protected $attributes = [
        'status_clie' => 1, // Activo por defecto
    ];
}
