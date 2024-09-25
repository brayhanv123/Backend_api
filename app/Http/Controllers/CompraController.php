<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompraRequest;
use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Producto;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
    public function index()
    {
        // Obtener todas las compras activas
        $compras = Compra::where('status_comp', 1)
            ->with('detallesCompra', 'proveedor')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $compras
        ], 200);
    }

    public function store(CompraRequest $request)
    {
        DB::beginTransaction(); // Comienza la transacción
        try {
            // Crear una nueva compra
            $compra = Compra::create([
                'fecha_comp' => $request->fecha_comp,
                'total_comp' => 0, // Se calculará después
                'id_prov' => $request->id_prov
            ]);

            $totalCompra = 0;

            // Guardar los detalles de la compra y actualizar el stock de los productos
            foreach ($request->detalles as $detalle) {
                // Validación de existencia de producto
                $producto = Producto::find($detalle['id_prod']);
                if (!$producto) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Producto no encontrado: ' . $detalle['id_prod']
                    ], 404);
                }

                // Crear el detalle de compra
                DetalleCompra::create([
                    'id_comp' => $compra->id_comp,
                    'id_prod' => $detalle['id_prod'],
                    'cantidad_comp' => $detalle['cantidad_comp'],
                    'precio_comp' => $detalle['precio_comp'],
                ]);

                // Actualizar el stock del producto
                $producto->stock_prod += $detalle['cantidad_comp']; // Sumar la cantidad comprada al stock actual
                $producto->save();

                // Calcular el total de la compra
                $totalCompra += $detalle['cantidad_comp'] * $detalle['precio_comp'];
            }

            // Actualizar el total de la compra
            $compra->total_comp = $totalCompra;
            $compra->save();

            DB::commit(); // Confirma la transacción

            return response()->json([
                'status' => 'success',
                'data' => $compra->load('detallesCompra') // Retornar la compra con sus detalles
            ], 201);
        } catch (Exception $e) {
            DB::rollBack(); // Revierte la transacción en caso de error
            return response()->json([
                'status' => 'error',
                'message' => 'Error al crear la compra',
                'error' => $e->getMessage() // Devolver detalles del error para debug
            ], 500);
        }
    }

    public function show($id_comp)
    {
        try {
            $compra = Compra::with('proveedor', 'detallesCompra.producto')
                ->where('id_comp', $id_comp)
                ->where('status_comp', 1) // Solo obtén compras activas
                ->firstOrFail();

            return response()->json([
                'status' => 'success',
                'data' => $compra
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Compra no encontrada o inactiva'
            ], 404);
        }
    }

    public function update(CompraRequest $request, $id_comp)
    {
        DB::beginTransaction(); // Comienza la transacción
        try {
            $compra = Compra::with('detallesCompra')->findOrFail($id_comp);

            // Revertir el stock de los productos por los detalles anteriores
            foreach ($compra->detallesCompra as $detalle) {
                $producto = Producto::findOrFail($detalle->id_prod);
                $producto->stock_prod -= $detalle->cantidad_comp; // Revertir la cantidad comprada al stock actual
                $producto->save();
            }

            // Actualizar los datos de la compra
            $compra->update([
                'fecha_comp' => $request->fecha_comp,
                'total_comp' => 0, // Se calculará después
                'id_prov' => $request->id_prov
            ]);

            // Eliminar los detalles anteriores
            $compra->detallesCompra()->delete();

            $totalCompra = 0;

            // Guardar los nuevos detalles de la compra y actualizar el stock
            foreach ($request->detalles as $detalle) {
                // Validación de existencia de producto
                $producto = Producto::find($detalle['id_prod']);
                if (!$producto) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Producto no encontrado: ' . $detalle['id_prod']
                    ], 404);
                }

                DetalleCompra::create([
                    'id_comp' => $compra->id_comp,
                    'id_prod' => $detalle['id_prod'],
                    'cantidad_comp' => $detalle['cantidad_comp'],
                    'precio_comp' => $detalle['precio_comp'],
                ]);

                // Actualizar el stock con los nuevos detalles
                $producto->stock_prod += $detalle['cantidad_comp']; // Sumar la nueva cantidad comprada al stock actual
                $producto->save();

                // Calcular el total de la compra
                $totalCompra += $detalle['cantidad_comp'] * $detalle['precio_comp'];
            }

            // Actualizar el total de la compra
            $compra->total_comp = $totalCompra;
            $compra->save();

            DB::commit(); // Confirma la transacción

            return response()->json([
                'status' => 'success',
                'data' => $compra->load('detallesCompra')
            ], 200);
        } catch (Exception $e) {
            DB::rollBack(); // Revierte la transacción en caso de error
            return response()->json([
                'status' => 'error',
                'message' => 'Error al actualizar la compra',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id_comp)
    {
        DB::beginTransaction(); // Comienza la transacción
        try {
            $compra = Compra::with('detallesCompra')->findOrFail($id_comp);

            // Revertir el stock de los productos
            foreach ($compra->detallesCompra as $detalle) {
                $producto = Producto::findOrFail($detalle->id_prod);
                $producto->stock_prod -= $detalle->cantidad_comp; // Restar la cantidad comprada al stock actual
                $producto->save();
            }

            // Marcar la compra como inactiva
            $compra->status_comp = 0;
            $compra->save();

            DB::commit(); // Confirma la transacción

            return response()->json([
                'status' => 'success',
                'message' => 'Compra inactivada correctamente'
            ], 200);
        } catch (ModelNotFoundException $e) {
            DB::rollBack(); // Revierte la transacción en caso de error
            return response()->json([
                'status' => 'error',
                'message' => 'Compra no encontrada'
            ], 404);
        } catch (Exception $e) {
            DB::rollBack(); // Revierte la transacción en caso de error
            return response()->json([
                'status' => 'error',
                'message' => 'Error al inactivar la compra',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}