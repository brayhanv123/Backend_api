<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompraRequest;
use App\Models\Compra;
use App\Services\CompraService;
use Exception;
use Illuminate\Http\Request;

class CompraController extends Controller
{
    private $compraService;

    public function __construct(CompraService $compraService)
    {
        $this->compraService = $compraService;
    }

    public function index()
    {
        $compras = Compra::where('status_comp', 1)
            ->with('detallesCompra', 'proveedor')
            ->get();

        return response()->json(['status' => 'success', 'data' => $compras], 200);
    }

    public function store(CompraRequest $request)
    {
        try {
            $compra = $this->compraService->crearCompra($request->validated());

            return response()->json(['status' => 'success', 'data' => $compra], 201);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id_comp)
    {
        try {
            $compra = Compra::with('proveedor', 'detallesCompra.producto')
                ->where('id_comp', $id_comp)
                ->where('status_comp', 1)
                ->firstOrFail();

            return response()->json(['status' => 'success', 'data' => $compra], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Compra no encontrada o inactiva'], 404);
        }
    }

    public function update(CompraRequest $request, $id_comp)
    {
        try {
            $compra = Compra::findOrFail($id_comp);
            $compra = $this->compraService->actualizarCompra($compra, $request->validated());

            return response()->json(['status' => 'success', 'data' => $compra], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id_comp)
    {
        try {
            $compra = Compra::findOrFail($id_comp);
            $this->compraService->inactivarCompra($compra);

            return response()->json(['status' => 'success', 'message' => 'Compra inactivada correctamente'], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}