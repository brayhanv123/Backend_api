<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proveedor; 
use App\Http\Requests\ProveedorRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;





class ProveedorController extends Controller
{
        
    public function index()
    {
        $proveedores = Proveedor::where('status_prov', 1)->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $proveedores
        ], 200);
    }

    public function store(ProveedorRequest $request)
    {
        try {
            $proveedor = Proveedor::create($request->validated());

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
            $proveedor = $this->findProveedor($id_prov);
            
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
            $proveedor = $this->findProveedor($id_prov);
            $proveedor->update($request->validated());

            return response()->json([
                'status' => 'success',
                'data' => $proveedor
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
            $proveedor = Proveedor::where('id_prov', $id_prov)->firstOrFail();
            $proveedor->status_prov = 0; 
            $proveedor->save();

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

  
    private function findProveedor(string $id_prov)
    {
        return Proveedor::where('id_prov', $id_prov)
                        ->where('status_prov', 1)
                        ->firstOrFail();
    }
}