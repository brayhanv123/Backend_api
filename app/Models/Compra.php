<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    use HasFactory;

    protected $table = 'tb_compras';
    protected $primaryKey = 'id_comp';

    protected $fillable = [
        'fecha_comp',
        'total_comp',
        'id_prov',  // Llave foránea de Proveedor
    ];

    // Definir el valor por defecto del status_compra
    protected $attributes = [
        'status_comp' => 1, // Activo por defecto
    ];

    // Relación con DetalleCompra (uno a muchos)
    public function detallesCompra()
    {
        return $this->hasMany(DetalleCompra::class, 'id_comp', 'id_comp');
    }

    // Relación con Proveedor (muchos a uno)
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_prov', 'id_prov');
    }
}
