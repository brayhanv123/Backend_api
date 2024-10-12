<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProveedorRequest;
use App\Services\ProveedorService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class ProveedorController extends Controller
{
    protected $proveedorService;

    public function __construct(ProveedorService $proveedorService)
    {
        $this->proveedorService = $proveedorService;
    }

    public function index()
    {
        $proveedores = $this->proveedorService->getAllActiveProveedores();
        
        return response()->json([
            'status' => 'success',
            'data' => $proveedores
        ], 200);
    }

    public function store(ProveedorRequest $request)
    {
        try {
            $proveedor = $this->proveedorService->createProveedor($request->validated());

            return response()->json([
                'status' => 'success',
                'data' => $proveedor
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al crear el proveedor.'
            ], 500);
        }
    }

    public function show(string $id_prov)
    {
        try {
            $proveedor = $this->proveedorService->findProveedor($id_prov);
            
            return response()->json([
                'status' => 'success',
                'data' => $proveedor
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Proveedor no encontrado.'
            ], 404);
        }
    }

    public function update(ProveedorRequest $request, string $id_prov)
    {
        try {
            $proveedor = $this->proveedorService->findProveedor($id_prov);
            $updatedProveedor = $this->proveedorService->updateProveedor($proveedor, $request->validated());

            return response()->json([
                'status' => 'success',
                'data' => $updatedProveedor
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Proveedor no encontrado.'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al actualizar el proveedor.'
            ], 500);
        }
    }

    public function destroy(string $id_prov)
    {
        try {
            $proveedor = $this->proveedorService->findProveedor($id_prov);
            $this->proveedorService->inactivateProveedor($proveedor);

            return response()->json([
                'status' => 'success',
                'message' => 'Proveedor inactivado correctamente.'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Proveedor no encontrado.'
            ], 404);
        }
    }
}