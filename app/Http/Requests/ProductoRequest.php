<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nombre_prod' => ['required', 'regex:/^[a-zA-Z\s]+$/', 'max:100'], // Solo letras y espacios
            'descripcion_prod' => ['nullable', 'string', 'max:255'], // Texto opcional
            'precio_comp' => ['required', 'numeric', 'min:0'], // Número positivo
            'precio_vent' => ['required', 'numeric', 'min:0'], // Número positivo
            'stock_prod' => ['required', 'integer', 'min:0'], // Número entero positivo
        ];
    }

    public function messages()
    {
        return [
            'nombre_prod.required' => 'El nombre del producto es obligatorio.',
            'nombre_prod.regex' => 'El nombre del producto solo puede contener letras y espacios.',
            'nombre_prod.max' => 'El nombre del producto no debe exceder los 100 caracteres.',

            'descripcion_prod.string' => 'La descripción debe ser un texto.',
            'descripcion_prod.max' => 'La descripción no debe exceder los 255 caracteres.',

            'precio_comp.required' => 'El precio de compra es obligatorio.',
            'precio_comp.numeric' => 'El precio de compra debe ser un número.',
            'precio_comp.min' => 'El precio de compra no puede ser negativo.',

            'precio_vent.required' => 'El precio de venta es obligatorio.',
            'precio_vent.numeric' => 'El precio de venta debe ser un número.',
            'precio_vent.min' => 'El precio de venta no puede ser negativo.',

            'stock_prod.required' => 'El stock es obligatorio.',
            'stock_prod.integer' => 'El stock debe ser un número entero.',
            'stock_prod.min' => 'El stock no puede ser negativo.',
        ];
    }
}
