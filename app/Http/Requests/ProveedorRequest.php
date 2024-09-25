<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProveedorRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nombre_prov' => ['required', 'regex:/^[a-zA-Z\s]+$/', 'max:100'], // Solo letras y espacios
            'direccion_prov' => ['required', 'regex:/^[a-zA-Z0-9\s,.-]+$/', 'max:45'], // Letras, números, espacios, coma, punto y guion
            'telefono_prov' => ['required', 'regex:/^[0-9]+$/', 'min:7', 'max:15'], // Solo números
        ];
    }

    public function messages()
    {
        return [
            'nombre_prov.required' => 'El nombre es obligatorio.',
            'nombre_prov.regex' => 'El nombre solo puede contener letras y espacios.',
            'nombre_prov.max' => 'El nombre no debe exceder los 100 caracteres.',

            'direccion_prov.required' => 'La dirección es obligatoria.',
            'direccion_prov.regex' => 'La dirección solo puede contener letras, números, espacios, coma, punto y guion.',
            'direccion_prov.max' => 'La dirección no debe exceder los 45 caracteres.',

            'telefono_prov.required' => 'El teléfono es obligatorio.',
            'telefono_prov.regex' => 'El teléfono solo puede contener números.',
            'telefono_prov.min' => 'El teléfono debe tener al menos 7 dígitos.',
            'telefono_prov.max' => 'El teléfono no debe exceder los 15 dígitos.',
        ];
    }
}
