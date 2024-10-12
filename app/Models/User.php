<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * Los atributos que se pueden asignar en masa.
     * 
     * @var array<int, string>
     */
    protected $fillable = [
        'name',    // El nombre del usuario.
        'email',   // El email del usuario.
        'password' // La contraseña encriptada del usuario.
    ];

    /**
     * Los atributos que deben estar ocultos para arrays.
     * 
     * @var array<int, string>
     */
    protected $hidden = [
        'password',         // Oculta la contraseña cuando se convierte el usuario a un array o JSON.
        'remember_token',   // Oculta el token de "recordar sesión" cuando se convierte el usuario a un array o JSON.
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     * 
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime', // Convierte 'email_verified_at' a un objeto de tipo DateTime.
    ];

    /**
     * Obtener el identificador que será almacenado en el JWT.
     * 
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        // Devuelve la clave primaria del usuario (por lo general, el ID).
        return $this->getKey();
    }

    /**
     * Devolver un array con cualquier claim adicional que quieras agregar al JWT.
     * 
     * @return array
     */
    public function getJWTCustomClaims()
    {
        // Devuelve un array vacío, pero aquí se podrían agregar datos adicionales al token si fuera necesario.
        return [];
    }
}