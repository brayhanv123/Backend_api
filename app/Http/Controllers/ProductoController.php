<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto; 
use App\Http\Requests\ProductoRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class ProductoController extends Controller
{
    /**
     * Muestra una lista de todos los productos.
     */
    public function index()
    {
        // Obtener solo productos con status_prod = 1 (activos)
        $productos = Producto::where('status_prod', 1)->get();
        
        // Retorna una respuesta JSON con el estado y los datos de los productos
        return response()->json([
            'status' => 'success',
            'data' => $productos
        ], 200);
    }

    /**
     * Almacena un nuevo producto en la base de datos.
     * 
     * @param ProductoRequest $request - Petición validada que contiene los datos del producto
     */
    public function store(ProductoRequest $request)
    {
        try {
            // Crea un nuevo producto con los datos validados
            $producto = Producto::create($request->validated()); // 'status_prod' se asigna automáticamente
            
            // Respuesta exitosa con el producto creado
            return response()->json([
                'status' => 'success',
                'data' => $producto
            ], 201);
        } catch (Exception $e) {
            // En caso de error, se responde con un mensaje de error
            return response()->json([
                'status' => 'error',
                'message' => 'Error al crear el producto.'
            ], 500);
        }
    }

    /**
     * Muestra los detalles de un producto específico.
     * 
     * @param string $id_prod - ID del producto a mostrar
     */
    public function show(string $id_prod)
    {
        try {
            // Busca el producto por su ID
            $producto = $this->findProducto($id_prod);
            
            // Respuesta exitosa con los datos del producto
            return response()->json([
                'status' => 'success',
                'data' => $producto
            ], 200);
        } catch (ModelNotFoundException $e) {
            // Si no se encuentra el producto, responde con un mensaje de error
            return response()->json([
                'status' => 'error',
                'message' => 'Producto no encontrado.'
            ], 404);
        }
    }

    /**
     * Actualiza los detalles de un producto específico.
     * 
     * @param ProductoRequest $request - Petición validada con los nuevos datos
     * @param string $id_prod - ID del producto a actualizar
     */
    public function update(ProductoRequest $request, string $id_prod)
    {
        try {
            // Busca el producto por su ID
            $producto = $this->findProducto($id_prod);
            
            // Actualiza los datos del producto
            $producto->update($request->validated());

            // Respuesta exitosa con los datos actualizados del producto
            return response()->json([
                'status' => 'success',
                'data' => $producto
            ], 200);
        } catch (ModelNotFoundException $e) {
            // Si no se encuentra el producto, responde con un mensaje de error
            return response()->json([
                'status' => 'error',
                'message' => 'Producto no encontrado.'
            ], 404);
        } catch (Exception $e) {
            // En caso de error durante la actualización, se responde con un mensaje de error
            return response()->json([
                'status' => 'error',
                'message' => 'Error al actualizar el producto.'
            ], 500);
        }
    }

    /**
     * Desactiva (inactiva) un producto específico cambiando su estado.
     * 
     * @param string $id_prod - ID del producto a desactivar
     */
    public function destroy(string $id_prod)
    {
        try {
            // Busca el producto por su ID
            $producto = Producto::where('id_prod', $id_prod)->firstOrFail();
            
            // Cambia el estado del producto a inactivo (0)
            $producto->status_prod = 0; 
            $producto->save(); // Guarda los cambios en la base de datos

            // Respuesta exitosa confirmando la desactivación
            return response()->json([
                'status' => 'success',
                'message' => 'Producto inactivado correctamente.'
            ], 200);
        } catch (ModelNotFoundException $e) {
            // Si no se encuentra el producto, responde con un mensaje de error
            return response()->json([
                'status' => 'error',
                'message' => 'Producto no encontrado.'
            ], 404);
        }
    }

    /**
     * Busca y devuelve un producto específico por su ID.
     * 
     * @param string $id_prod - ID del producto
     * @return Producto - Producto encontrado o se lanza una excepción si no existe
     */
    private function findProducto(string $id_prod)
    {
        // Buscar producto por ID y asegurarse de que esté activo (status_prod = 1)
        return Producto::where('id_prod', $id_prod)
                        ->where('status_prod', 1) // Solo productos activos
                        ->firstOrFail();
    }
}
