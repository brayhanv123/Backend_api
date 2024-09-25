<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleCompra extends Model
{
    use HasFactory;

    protected $table = 'tb_detalle_compras';
    protected $primaryKey = 'id_detalle_comp';

    protected $fillable = [
        'id_comp',
        'id_prod',
        'cantidad_comp',
        'precio_comp',
    ];

    // Relación con Compra (muchos a uno)
    public function compra()
    {
        return $this->belongsTo(Compra::class, 'id_comp', 'id_comp');
    }

    // Relación con Producto (muchos a uno)
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_prod', 'id_prod');
    }
}
