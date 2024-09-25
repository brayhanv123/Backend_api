<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\VentaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rutas de autenticación
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::middleware('jwt.auth')->get('user', [AuthController::class, 'getAuthenticatedUser']);

// Rutas protegidas para proveedores (requiere autenticación JWT)
Route::middleware('jwt.auth')->group(function () {
    Route::get('proveedores', [ProveedorController::class, 'index']);
    Route::post('proveedores', [ProveedorController::class, 'store']);
    Route::get('proveedores/{id}', [ProveedorController::class, 'show']);
    Route::put('proveedores/{id}', [ProveedorController::class, 'update']);
    Route::delete('proveedores/{id}', [ProveedorController::class, 'destroy']);
    Route::get('productos', [ProductoController::class, 'index']);
    Route::post('productos', [ProductoController::class, 'store']);
    Route::get('productos/{id}', [ProductoController::class, 'show']);
    Route::put('productos/{id}', [ProductoController::class, 'update']);
    Route::delete('productos/{id}', [ProductoController::class, 'destroy']);
    Route::get('compras', [CompraController::class, 'index']);
    Route::post('compras', [CompraController::class, 'store']);
    Route::get('compras/{id}', [CompraController::class, 'show']);
    Route::put('compras/{id}', [CompraController::class, 'update']);
    Route::delete('compras/{id}', [CompraController::class, 'destroy']);
    Route::get('clientes', [ClienteController::class, 'index']);
    Route::post('clientes', [ClienteController::class, 'store']);
    Route::get('clientes/{id}', [ClienteController::class, 'show']);
    Route::put('clientes/{id}', [ClienteController::class, 'update']);
    Route::delete('clientes/{id}', [ClienteController::class, 'destroy']);
    Route::get('ventas', [VentaController::class, 'index']);
    Route::post('ventas', [VentaController::class, 'store']);
    Route::get('ventas/{id}', [VentaController::class, 'show']);
    Route::put('ventas/{id}', [VentaController::class, 'update']);
    Route::delete('ventas/{id}', [VentaController::class, 'destroy']);

    // Ruta para cerrar sesión
    Route::post('logout', [AuthController::class, 'logout']);
});