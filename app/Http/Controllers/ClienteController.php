<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente; 
use App\Http\Requests\ClienteRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class ClienteController extends Controller
{
    // Método para obtener todos los clientes
    public function index()
    {
        // Obtener solo clientes con status_clie = 1 (activos)
        $clientes = Cliente::where('status_clie', 1)->get();
        
        // Retorna una respuesta JSON con el estado y los datos de los clientes
        return response()->json([
            'status' => 'success', // Estado exitoso
            'data' => $clientes // Lista de clientes
        ], 200); // Código de respuesta HTTP 200 (OK)
    }

        /**
     * Almacena un nuevo cliente en la base de datos.
     * 
     * @param ClienteRequest $request - Petición validada que contiene los datos del cliente
     */
    public function store(ClienteRequest $request)
    {
        try {
            // Crea un nuevo cliente con los datos validados
            $cliente = Cliente::create($request->validated()); // 'status_clie' se asigna automáticamente
            
            // Respuesta exitosa con el cliente creado
            return response()->json([
                'status' => 'success', // Estado exitoso
                'data' => $cliente  // Datos del cliente creado
            ], 201); // Código de respuesta HTTP 201 (Creado)
        } catch (Exception $e) {
            // En caso de error, se responde con un mensaje de error
            return response()->json([
                'status' => 'error', // Estado de error
                'message' => 'Error al crear el cliente.'  // Mensaje de error
            ], 500); // Código de respuesta HTTP 500 (Error en el servidor)
        }
    }

    /**
     * Muestra los detalles de un cliente específico.
     * 
     * @param string $id_clie - ID del cliente a mostrar
     */
    public function show(string $id_clie)
    {
        try {
            // Busca el cliente por su ID
            $cliente = $this->findCliente($id_clie);
            
            // Respuesta exitosa con los datos del cliente
            return response()->json([
                'status' => 'success', // Estado exitoso
                'data' => $cliente  // Datos del cliente encontrado
            ], 200); // Código de respuesta HTTP 200 (OK)
        } catch (ModelNotFoundException $e) {
            // Si no se encuentra el cliente, responde con un mensaje de error
            return response()->json([
                'status' => 'error', // Estado de error
                'message' => 'Cliente no encontrado.'  // Mensaje de cliente no encontrado
            ], 404); // Código de respuesta HTTP 404 (No encontrado)
        }
    }
     /**
     * Actualiza los detalles de un cliente específico.
     * 
     * @param ClienteRequest $request - Petición validada con los nuevos datos
     * @param string $id_clie - ID del cliente a actualizar
     */
    public function update(ClienteRequest $request, string $id_clie)
    {
        try {
            // Busca el cliente por su ID
            $cliente = $this->findCliente($id_clie);
            
            // Actualiza los datos del cliente
            $cliente->update($request->validated());

            // Respuesta exitosa con los datos actualizados del cliente
            return response()->json([
                'status' => 'success', // Estado exitoso
                'data' => $cliente  // Datos del cliente actualizado
            ], 200); // Código de respuesta HTTP 200 (OK)
        } catch (ModelNotFoundException $e) {
            // Si no se encuentra el clieinte, responde con un mensaje de error
            return response()->json([
                'status' => 'error', // Estado de error
                'message' => 'Cliente no encontrado.'  // Mensaje de cliente no encontrado
            ], 404); // Código de respuesta HTTP 404 (No encontrado)
        } catch (Exception $e) {
            // En caso de error durante la actualización, se responde con un mensaje de error
            return response()->json([
                'status' => 'error', // Estado de error
                'message' => 'Error al actualizar el cliente.'  // Mensaje de error
            ], 500); // Código de respuesta HTTP 500 (Error en el servidor)
        }
    }

    // Método para inactivar/eliminar un cliente
     /**
     * Desactiva (inactiva) un cliente específico cambiando su estado.
     * 
     * @param string $id_clie - ID del cliente a desactivar
     */
    public function destroy(string $id_clie)
    {
        try {
            // Busca el cliente por su ID
            $cliente = Cliente::where('id_clie', $id_clie)->firstOrFail();
            
            // Cambia el estado del cliente a inactivo (0)
            $cliente->status_clie = 0; 
            $cliente->save(); // Guarda los cambios en la base de datos

            // Respuesta exitosa confirmando la desactivación
            return response()->json([
                'status' => 'success', // Estado exitoso
                'message' => 'Cliente inactivado correctamente.'  // Mensaje de confirmación
            ], 200); // Código de respuesta HTTP 200 (OK)
        } catch (ModelNotFoundException $e) {
            // Si no se encuentra el cliente, responde con un mensaje de error
            return response()->json([
                'status' => 'error', // Estado de error
                'message' => 'Cliente no encontrado.'  // Mensaje de cliente no encontrado
            ], 404); // Código de respuesta HTTP 404 (No encontrado)
        }
    }

     /**
     * Busca y devuelve un cliente específico por su ID.
     * 
     * @param string $id_clie - ID del cliente
     * @return Cliente - cliente encontrado o se lanza una excepción si no existe
     */
    private function findCliente(string $id_clie)
    {
        // Buscar cliente por ID y asegurarse de que esté activo (status_clie = 1)
        return Cliente::where('id_clie', $id_clie)
                        ->where('status_clie', 1) // Solo clientes activos
                        ->firstOrFail();
    }
}