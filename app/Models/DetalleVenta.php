<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    use HasFactory;

    protected $table = 'tb_detalle_ventas';
    protected $primaryKey = 'id_detalle_vent';

    protected $fillable = [
        'id_vent',
        'id_prod',
        'cantidad_vent',
        'precio_vent',
    ];

    // Relación con compra
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_vent', 'id_vent');
    }

    // Relación con producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_prod', 'id_prod');
    }
}


