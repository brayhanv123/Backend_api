<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * Registrar un nuevo usuario y devolver el token JWT.
     */
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        // Generar el token para el usuario
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'Usuario registrado con éxito',
            'token' => $token
        ], 201);
    }

    /**
     * Iniciar sesión y devolver el token JWT.
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Credenciales inválidas'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'No se pudo crear el token'], 500);
        }

        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'token' => $token
        ]);
    }

    /**
     * Obtener el usuario autenticado.
     */
    public function getAuthenticatedUser()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['error' => 'Usuario no encontrado'], 404);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Error con el token'], $e->getStatusCode());
        }

        return response()->json(compact('user'));
    }

    /**
     * Cerrar sesión y invalidar el token JWT.
     */
    public function logout(Request $request)
    {
        try {
            // Invalida el token actual
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json(['message' => 'Logout exitoso.']);
        } catch (JWTException $e) {
            return response()->json(['error' => 'No se pudo cerrar la sesión, intente nuevamente.'], 500);
        }
    }
}
