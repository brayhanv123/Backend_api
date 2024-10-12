<?php

namespace App\Services;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Exception;

class CompraService
{
    /**
     * Crear una compra con sus detalles y actualizar el stock de los productos.
     *
     * @param array $data
     * @return Compra
     * @throws Exception
     */
    public function crearCompra(array $data): Compra
    {
        DB::beginTransaction();
        try {
            // Crear la compra
            $compra = Compra::create([
                'fecha_comp' => $data['fecha_comp'],
                'total_comp' => 0,
                'id_prov' => $data['id_prov'],
            ]);

            $totalCompra = $this->guardarDetallesCompra($compra, $data['detalles']);

            // Actualizar el total de la compra
            $compra->total_comp = $totalCompra;
            $compra->save();

            DB::commit();
            return $compra->load('detallesCompra');
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Error al crear la compra: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar una compra existente, sus detalles y ajustar el stock.
     *
     * @param Compra $compra
     * @param array $data
     * @return Compra
     * @throws Exception
     */
    public function actualizarCompra(Compra $compra, array $data): Compra
    {
        DB::beginTransaction();
        try {
            // Revertir el stock de los productos actuales
            $this->revertirStock($compra);

            // Actualizar la compra
            $compra->update([
                'fecha_comp' => $data['fecha_comp'],
                'id_prov' => $data['id_prov'],
                'total_comp' => 0
            ]);

            // Eliminar los detalles existentes
            $compra->detallesCompra()->delete();

            // Guardar los nuevos detalles y calcular el total
            $totalCompra = $this->guardarDetallesCompra($compra, $data['detalles']);

            // Actualizar el total de la compra
            $compra->total_comp = $totalCompra;
            $compra->save();

            DB::commit();
            return $compra->load('detallesCompra');
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Error al actualizar la compra: ' . $e->getMessage());
        }
    }

    /**
     * Inactivar una compra y revertir el stock.
     *
     * @param Compra $compra
     * @return void
     * @throws Exception
     */
    public function inactivarCompra(Compra $compra): void
    {
        DB::beginTransaction();
        try {
            $this->revertirStock($compra);

            $compra->status_comp = 0;
            $compra->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Error al inactivar la compra: ' . $e->getMessage());
        }
    }

    /**
     * Guardar los detalles de una compra y actualizar el stock.
     *
     * @param Compra $compra
     * @param array $detalles
     * @return float
     * @throws Exception
     */
    private function guardarDetallesCompra(Compra $compra, array $detalles): float
    {
        $totalCompra = 0;

        foreach ($detalles as $detalle) {
            $producto = Producto::find($detalle['id_prod']);
            if (!$producto) {
                throw new Exception('Producto no encontrado: ' . $detalle['id_prod']);
            }

            DetalleCompra::create([
                'id_comp' => $compra->id_comp,
                'id_prod' => $detalle['id_prod'],
                'cantidad_comp' => $detalle['cantidad_comp'],
                'precio_comp' => $detalle['precio_comp'],
            ]);

            $producto->stock_prod += $detalle['cantidad_comp'];
            $producto->save();

            $totalCompra += $detalle['cantidad_comp'] * $detalle['precio_comp'];
        }

        return $totalCompra;
    }

    /**
     * Revertir el stock de los productos de una compra.
     *
     * @param Compra $compra
     * @return void
     */
    private function revertirStock(Compra $compra): void
    {
        foreach ($compra->detallesCompra as $detalle) {
            $producto = Producto::find($detalle->id_prod);
            if ($producto) {
                $producto->stock_prod -= $detalle->cantidad_comp;
                $producto->save();
            }
        }
    }
}