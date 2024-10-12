<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClienteRequest;
use App\Services\ClienteService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class ClienteController extends Controller
{
    protected $clienteService;

    public function __construct(ClienteService $clienteService)
    {
        $this->clienteService = $clienteService;
    }

    public function index()
    {
        $clientes = $this->clienteService->getAllActiveClientes();
        
        return response()->json([
            'status' => 'success',
            'data' => $clientes
        ], 200);
    }

    public function store(ClienteRequest $request)
    {
        try {
            $cliente = $this->clienteService->createCliente($request->validated());

            return response()->json([
                'status' => 'success',
                'data' => $cliente
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al crear el cliente.'
            ], 500);
        }
    }

    public function show(string $id_clie)
    {
        try {
            $cliente = $this->clienteService->findCliente($id_clie);
            
            return response()->json([
                'status' => 'success',
                'data' => $cliente
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cliente no encontrado.'
            ], 404);
        }
    }

    public function update(ClienteRequest $request, string $id_clie)
    {
        try {
            $cliente = $this->clienteService->findCliente($id_clie);
            $updatedCliente = $this->clienteService->updateCliente($cliente, $request->validated());

            return response()->json([
                'status' => 'success',
                'data' => $updatedCliente
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cliente no encontrado.'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al actualizar el cliente.'
            ], 500);
        }
    }

    public function destroy(string $id_clie)
    {
        try {
            $cliente = $this->clienteService->findCliente($id_clie);
            $this->clienteService->inactivateCliente($cliente);

            return response()->json([
                'status' => 'success',
                'message' => 'Cliente inactivado correctamente.'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cliente no encontrado.'
            ], 404);
        }
    }
}