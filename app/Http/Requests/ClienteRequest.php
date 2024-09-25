<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClienteRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nombre_clie' => ['required', 'regex:/^[a-zA-Z\s]+$/', 'max:100'], // Solo letras y espacios
            'direccion_clie' => ['required', 'regex:/^[a-zA-Z0-9\s,.-]+$/', 'max:45'], // Letras, números, espacios, coma, punto y guion
            'telefono_clie' => ['required', 'regex:/^[0-9]+$/', 'min:7', 'max:15'], // Solo números
        ];
    }

    public function messages()
    {
        return [
            'nombre_clie.required' => 'El nombre es obligatorio.',
            'nombre_clie.regex' => 'El nombre solo puede contener letras y espacios.',
            'nombre_clie.max' => 'El nombre no debe exceder los 100 caracteres.',

            'direccion_clie.required' => 'La dirección es obligatoria.',
            'direccion_clie.regex' => 'La dirección solo puede contener letras, números, espacios, coma, punto y guion.',
            'direccion_clie.max' => 'La dirección no debe exceder los 45 caracteres.',

            'telefono_clie.required' => 'El teléfono es obligatorio.',
            'telefono_clie.regex' => 'El teléfono solo puede contener números.',
            'telefono_clie.min' => 'El teléfono debe tener al menos 7 dígitos.',
            'telefono_clie.max' => 'El teléfono no debe exceder los 15 dígitos.',
        ];
    }
}
