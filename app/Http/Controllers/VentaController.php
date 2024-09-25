<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Http\Requests\VentaRequest;
use Exception;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
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
        DB::beginTransaction(); // Comienza la transacción
        try {
            $ventaData = $request->validated();
            $venta = Venta::create([
                'id_clie' => $ventaData['id_clie'],
                'fecha_vent' => now(),
                'total_vent' => 0 // Se calculará después en base a los detalles
            ]);

            $totalVenta = 0;

            foreach ($ventaData['detalles'] as $detalle) {
                // Validación de existencia de producto
                $producto = Producto::find($detalle['id_prod']);
                if (!$producto) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Producto no encontrado: ' . $detalle['id_prod']
                    ], 404);
                }

                if ($producto->stock_prod < $detalle['cantidad_vent']) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Stock insuficiente para el producto: ' . $producto->id_prod
                    ], 400);
                }

                $subtotal = $detalle['cantidad_vent'] * $detalle['precio_vent'];
                $totalVenta += $subtotal;

                DetalleVenta::create([
                    'id_vent' => $venta->id_vent,
                    'id_prod' => $detalle['id_prod'],
                    'cantidad_vent' => $detalle['cantidad_vent'],
                    'precio_vent' => $detalle['precio_vent']
                ]);

                // Actualiza el stock del producto
                $producto->stock_prod -= $detalle['cantidad_vent'];
                $producto->save();
            }

            $venta->total_vent = $totalVenta;
            $venta->save();

            DB::commit(); // Confirma la transacción

            return response()->json([
                'status' => 'success',
                'data' => $venta->load('detallesVenta')
            ], 201);
        } catch (Exception $e) {
            DB::rollBack(); // Revierte los cambios en caso de error
            return response()->json([
                'status' => 'error',
                'message' => 'Error al procesar la venta.'
            ], 500);
        }
    }

    // Método para mostrar una venta específica por su ID
    public function show(string $id_vent)
    {
        try {
            $venta = Venta::where('id_vent', $id_vent)
                          ->where('status_vent', 1)
                          ->with('detallesVenta', 'cliente')
                          ->firstOrFail();

            return response()->json([
                'status' => 'success',
                'data' => $venta
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Venta no encontrada.'
            ], 404);
        }
    }

    // Método para actualizar una venta
    public function update(VentaRequest $request, string $id_vent)
    {
        DB::beginTransaction(); // Comienza la transacción
        try {
            $venta = Venta::where('id_vent', $id_vent)
                          ->where('status_vent', 1)
                          ->firstOrFail();

            $ventaData = $request->validated();
            $totalVenta = 0;

            // Borrar detalles previos de la venta
            $venta->detallesVenta()->delete();

            foreach ($ventaData['detalles'] as $detalle) {
                // Validación de existencia de producto
                $producto = Producto::find($detalle['id_prod']);
                if (!$producto) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Producto no encontrado: ' . $detalle['id_prod']
                    ], 404);
                }

                if ($producto->stock_prod < $detalle['cantidad_vent']) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Stock insuficiente para el producto: ' . $producto->id_prod
                    ], 400);
                }

                $subtotal = $detalle['cantidad_vent'] * $detalle['precio_vent'];
                $totalVenta += $subtotal;

                DetalleVenta::create([
                    'id_vent' => $venta->id_vent,
                    'id_prod' => $detalle['id_prod'],
                    'cantidad_vent' => $detalle['cantidad_vent'],
                    'precio_vent' => $detalle['precio_vent']
                ]);

                // Actualiza el stock del producto
                $producto->stock_prod -= $detalle['cantidad_vent'];
                $producto->save();
            }

            $venta->total_vent = $totalVenta;
            $venta->save();

            DB::commit(); // Confirma la transacción

            return response()->json([
                'status' => 'success',
                'data' => $venta->load('detallesVenta')
            ], 200);
        } catch (Exception $e) {
            DB::rollBack(); // Revierte los cambios en caso de error
            return response()->json([
                'status' => 'error',
                'message' => 'Error al actualizar la venta.'
            ], 500);
        }
    }

    // Método para inactivar una venta
    public function destroy(string $id_vent)
    {
        DB::beginTransaction(); // Comienza la transacción
        try {
            $venta = Venta::where('id_vent', $id_vent)->firstOrFail();
            // Cambiar el estado de la venta a inactiva
            $venta->status_vent = 0;

            // Regresar el stock de los productos de la venta
            foreach ($venta->detallesVenta as $detalle) {
                $producto = Producto::find($detalle->id_prod);
                if ($producto) {
                    $producto->stock_prod += $detalle->cantidad_vent;
                    $producto->save();
                }
            }

            $venta->save();
            DB::commit(); // Confirma la transacción

            return response()->json([
                'status' => 'success',
                'message' => 'Venta inactivada correctamente.'
            ], 200);
        } catch (Exception $e) {
            DB::rollBack(); // Revierte los cambios en caso de error
            return response()->json([
                'status' => 'error',
                'message' => 'Venta no encontrada.'
            ], 404);
        }
    }
}
