<?php

namespace App\Services;

use App\Models\Producto;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductoService
{
    public function getAllActiveProductos()
    {
        return Producto::where('status_prod', 1)->get();
    }

    public function createProducto(array $data)
    {
        return Producto::create($data);
    }

    public function findProducto(string $id_prod)
    {
        return Producto::where('id_prod', $id_prod)
                        ->where('status_prod', 1)
                        ->firstOrFail();
    }

    public function updateProducto(Producto $producto, array $data)
    {
        $producto->update($data);
        return $producto;
    }

    public function inactivateProducto(Producto $producto)
    {
        $producto->status_prod = 0;
        $producto->save();
    }
}