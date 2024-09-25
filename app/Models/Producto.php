<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'tb_productos';
    protected $primaryKey = 'id_prod';

    protected $fillable = [
        'nombre_prod',
        'descripcion_prod',
        'precio_comp',
        'precio_vent',
        'stock_prod',
    ];

    // Definir el valor por defecto del status_prod
    protected $attributes = [
        'status_prod' => 1, // Activo por defecto
    ];

    // Relación con DetalleVenta (muchos a muchos)
    public function detallesVenta()
    {
        return $this->hasMany(DetalleVenta::class, 'id_prod', 'id_prod');
    }
}
