<?php

namespace App\Services;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ClienteService
{
    public function getAllActiveClientes()
    {
        return Cliente::where('status_clie', 1)->get();
    }

    public function createCliente(array $data)
    {
        return Cliente::create($data);
    }

    public function findCliente(string $id_clie)
    {
        return Cliente::where('id_clie', $id_clie)
                        ->where('status_clie', 1)
                        ->firstOrFail();
    }

    public function updateCliente(Cliente $cliente, array $data)
    {
        $cliente->update($data);
        return $cliente;
    }

    public function inactivateCliente(Cliente $cliente)
    {
        $cliente->status_clie = 0;
        $cliente->save();
    }
}