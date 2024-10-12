<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Http\Requests\VentaRequest;
use App\Services\VentaService; // Usar un servicio para la lógica
use Exception;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    protected $ventaService;

    // Inyectar el servicio de ventas para la lógica de negocio
    public function __construct(VentaService $ventaService)
    {
        $this->ventaService = $ventaService;
    }

    // Método para obtener todas las ventas activas
    public function index()
    {
        $ventas = Venta::where('status_vent', 1)->with('detallesVenta', 'cliente')->get();

        return response()->json([
            'status' => 'success',
            'data' => $ventas
        ], 200);
    }

    // Método para almacenar una nueva venta
    public function store(VentaRequest $request)
    {
        try {
            $venta = $this->ventaService->crearVenta($request->validated());
            return response()->json([
                'status' => 'success',
                'data' => $venta->load('detallesVenta')
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al procesar la venta.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Método para mostrar una venta específica
    public function show(string $id_vent)
    {
        try {
            $venta = $this->ventaService->obtenerVenta($id_vent);
            return response()->json([
                'status' => 'success',
                'data' => $venta
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Venta no encontrada.',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    // Método para actualizar una venta
    public function update(VentaRequest $request, string $id_vent)
    {
        try {
            $venta = $this->ventaService->actualizarVenta($id_vent, $request->validated());
            return response()->json([
                'status' => 'success',
                'data' => $venta->load('detallesVenta')
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al actualizar la venta.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Método para inactivar una venta
    public function destroy(string $id_vent)
    {
        try {
            $this->ventaService->inactivarVenta($id_vent);
            return response()->json([
                'status' => 'success',
                'message' => 'Venta inactivada correctamente.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al inactivar la venta.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}