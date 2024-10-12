<?php

namespace App\Services;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Exception;

class VentaService
{
    // Crear una nueva venta
    public function crearVenta(array $ventaData)
    {
        DB::beginTransaction();
        try {
            $venta = Venta::create([
                'id_clie' => $ventaData['id_clie'],
                'fecha_vent' => now(),
                'total_vent' => 0 // Se calculará después
            ]);

            $totalVenta = 0;

            foreach ($ventaData['detalles'] as $detalle) {
                $producto = $this->validarProducto($detalle['id_prod'], $detalle['cantidad_vent']);
                $subtotal = $this->calcularSubtotal($detalle['cantidad_vent'], $detalle['precio_vent']);
                $totalVenta += $subtotal;

                DetalleVenta::create([
                    'id_vent' => $venta->id_vent,
                    'id_prod' => $detalle['id_prod'],
                    'cantidad_vent' => $detalle['cantidad_vent'],
                    'precio_vent' => $detalle['precio_vent']
                ]);

                // Actualiza el stock del producto
                $this->actualizarStockProducto($producto, $detalle['cantidad_vent']);
            }

            $venta->total_vent = $totalVenta;
            $venta->save();

            DB::commit();
            return $venta;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Error al procesar la venta: " . $e->getMessage());
        }
    }

    // Obtener una venta específica
    public function obtenerVenta(string $id_vent)
    {
        return Venta::where('id_vent', $id_vent)
            ->where('status_vent', 1)
            ->with('detallesVenta', 'cliente')
            ->firstOrFail();
    }

    // Actualizar una venta existente
    public function actualizarVenta(string $id_vent, array $ventaData)
    {
        DB::beginTransaction();
        try {
            $venta = Venta::with('detallesVenta')->findOrFail($id_vent);
            $venta->detallesVenta()->delete();

            $totalVenta = 0;

            foreach ($ventaData['detalles'] as $detalle) {
                $producto = $this->validarProducto($detalle['id_prod'], $detalle['cantidad_vent']);
                $subtotal = $this->calcularSubtotal($detalle['cantidad_vent'], $detalle['precio_vent']);
                $totalVenta += $subtotal;

                DetalleVenta::create([
                    'id_vent' => $venta->id_vent,
                    'id_prod' => $detalle['id_prod'],
                    'cantidad_vent' => $detalle['cantidad_vent'],
                    'precio_vent' => $detalle['precio_vent']
                ]);

                // Actualiza el stock del producto
                $this->actualizarStockProducto($producto, $detalle['cantidad_vent']);
            }

            $venta->total_vent = $totalVenta;
            $venta->save();

            DB::commit();
            return $venta;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Error al actualizar la venta: " . $e->getMessage());
        }
    }

    // Inactivar una venta
    public function inactivarVenta(string $id_vent)
    {
        DB::beginTransaction();
        try {
            $venta = Venta::findOrFail($id_vent);
            $venta->status_vent = 0;

            foreach ($venta->detallesVenta as $detalle) {
                $producto = Producto::find($detalle->id_prod);
                $this->actualizarStockProducto($producto, -$detalle->cantidad_vent);
            }

            $venta->save();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Error al inactivar la venta: " . $e->getMessage());
        }
    }

    // Método para validar la existencia y stock del producto
    private function validarProducto(int $id_prod, int $cantidad)
    {
        $producto = Producto::find($id_prod);
        if (!$producto || $producto->stock_prod < $cantidad) {
            throw new Exception("Stock insuficiente o producto no encontrado: $id_prod");
        }
        return $producto;
    }

    // Método para calcular el subtotal de un detalle de venta
    private function calcularSubtotal(int $cantidad, float $precio)
    {
        return $cantidad * $precio;
    }

    // Método para actualizar el stock de un producto
    private function actualizarStockProducto(Producto $producto, int $cantidad)
    {
        $producto->stock_prod -= $cantidad;
        $producto->save();
    }
}