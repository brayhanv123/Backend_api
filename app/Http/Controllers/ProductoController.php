<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductoRequest;
use App\Services\ProductoService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class ProductoController extends Controller
{
    protected $productoService;

    public function __construct(ProductoService $productoService)
    {
        $this->productoService = $productoService;
    }

    public function index()
    {
        $productos = $this->productoService->getAllActiveProductos();
        
        return response()->json([
            'status' => 'success',
            'data' => $productos
        ], 200);
    }

    public function store(ProductoRequest $request)
    {
        try {
            $producto = $this->productoService->createProducto($request->validated());

            return response()->json([
                'status' => 'success',
                'data' => $producto
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al crear el producto.'
            ], 500);
        }
    }

    public function show(string $id_prod)
    {
        try {
            $producto = $this->productoService->findProducto($id_prod);
            
            return response()->json([
                'status' => 'success',
                'data' => $producto
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Producto no encontrado.'
            ], 404);
        }
    }

    public function update(ProductoRequest $request, string $id_prod)
    {
        try {
            $producto = $this->productoService->findProducto($id_prod);
            $updatedProducto = $this->productoService->updateProducto($producto, $request->validated());

            return response()->json([
                'status' => 'success',
                'data' => $updatedProducto
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Producto no encontrado.'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al actualizar el productor.'
            ], 500);
        }
    }

    public function destroy(string $id_prod)
    {
        try {
            $producto = $this->productoService->findProducto($id_prod);
            $this->productoService->inactivateProducto($producto);

            return response()->json([
                'status' => 'success',
                'message' => 'Producto inactivado correctamente.'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Producto no encontrado.'
            ], 404);
        }
    }
}