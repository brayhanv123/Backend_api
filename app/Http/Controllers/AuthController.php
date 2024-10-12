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
     * 
     * @param Request $request Los datos de la solicitud.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el token y mensaje de éxito.
     */
    public function register(Request $request)
    {
        // Validar los datos del formulario.
        $validatedData = $request->validate([
            'name' => 'required|string|max:255', // El nombre es obligatorio, debe ser un string y no puede superar los 255 caracteres.
            'email' => 'required|string|email|max:255|unique:users', // El email es obligatorio, debe ser único en la tabla 'users'.
            'password' => 'required|string|min:6|confirmed', // La contraseña es obligatoria, debe ser un string, mínimo de 6 caracteres y confirmada.
        ]);

        // Crear un nuevo usuario con los datos validados y encriptar la contraseña.
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']), // Se encripta la contraseña antes de guardarla.
        ]);

        // Generar el token JWT para el usuario recién registrado.
        $token = JWTAuth::fromUser($user);

        // Devolver una respuesta JSON con un mensaje de éxito y el token.
        return response()->json([
            'message' => 'Usuario registrado con éxito',
            'token' => $token
        ], 201);
    }

    /**
     * Iniciar sesión y devolver el token JWT.
     * 
     * @param Request $request Los datos de la solicitud.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el token o un mensaje de error.
     */
    public function login(Request $request)
    {
        // Obtener solo el email y la contraseña de la solicitud.
        $credentials = $request->only('email', 'password');

        try {
            // Intentar autenticar al usuario con las credenciales proporcionadas.
            if (!$token = JWTAuth::attempt($credentials)) {
                // Si las credenciales no son correctas, devolver un error.
                return response()->json(['error' => 'Credenciales inválidas'], 400);
            }
        } catch (JWTException $e) {
            // Si ocurre un error al generar el token, devolver un error 500.
            return response()->json(['error' => 'No se pudo crear el token'], 500);
        }

        // Devolver una respuesta JSON con un mensaje de éxito y el token.
        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'token' => $token
        ]);
    }

    /**
     * Obtener el usuario autenticado.
     * 
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con los datos del usuario autenticado o un error.
     */
    public function getAuthenticatedUser()
    {
        try {
            // Intentar obtener al usuario autenticado a partir del token.
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                // Si no se encuentra el usuario, devolver un error 404.
                return response()->json(['error' => 'Usuario no encontrado'], 404);
            }
        } catch (JWTException $e) {
            // Si hay un problema con el token, devolver un error con el código correspondiente.
            return response()->json(['error' => 'Error con el token'], $e->getStatusCode());
        }

        // Devolver una respuesta JSON con los datos del usuario autenticado.
        return response()->json(compact('user'));
    }

    /**
     * Cerrar sesión y invalidar el token JWT.
     * 
     * @param Request $request La solicitud.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con un mensaje de éxito o error.
     */
    public function logout(Request $request)
    {
        try {
            // Invalida el token actual para que ya no sea válido.
            JWTAuth::invalidate(JWTAuth::getToken());

            // Devolver una respuesta de éxito al cerrar la sesión.
            return response()->json(['message' => 'Logout exitoso.']);
        } catch (JWTException $e) {
            // Si hay un error al invalidar el token, devolver un mensaje de error.
            return response()->json(['error' => 'No se pudo cerrar la sesión, intente nuevamente.'], 500);
        }
    }
}