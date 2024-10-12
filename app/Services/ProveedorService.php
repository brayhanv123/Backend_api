<?php

namespace App\Services;

use App\Models\Proveedor;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProveedorService
{
    public function getAllActiveProveedores()
    {
        return Proveedor::where('status_prov', 1)->get();
    }

    public function createProveedor(array $data)
    {
        return Proveedor::create($data);
    }

    public function findProveedor(string $id_prov)
    {
        return Proveedor::where('id_prov', $id_prov)
                        ->where('status_prov', 1)
                        ->firstOrFail();
    }

    public function updateProveedor(Proveedor $proveedor, array $data)
    {
        $proveedor->update($data);
        return $proveedor;
    }

    public function inactivateProveedor(Proveedor $proveedor)
    {
        $proveedor->status_prov = 0;
        $proveedor->save();
    }
}