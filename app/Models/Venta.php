<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $table = 'tb_ventas';
    protected $primaryKey = 'id_vent';

    protected $fillable = [
        'fecha_vent',
        'total_vent',
        'id_clie',  // Llave foránea de Cliente
    ];

    // Definir el valor por defecto del status_vent
    protected $attributes = [
        'status_vent' => 1, // Activo por defecto
    ];

    // Relación con DetalleVenta (uno a muchos)
    public function detallesVenta()
    {
        return $this->hasMany(DetalleVenta::class, 'id_vent', 'id_vent');
    }

    // Relación con Cliente (muchos a uno)
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_clie', 'id_clie');
    }
}
