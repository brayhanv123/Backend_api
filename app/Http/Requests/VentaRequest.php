<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VentaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id_clie' => ['required', 'exists:tb_clientes,id_clie'],  // Verifica que el cliente exista
            'detalles' => ['required', 'array'],  // Los detalles de la venta deben ser un array
            'detalles.*.id_prod' => ['required', 'exists:tb_productos,id_prod'],  // Verifica que el producto exista
            'detalles.*.cantidad_vent' => ['required', 'integer', 'min:1'],  // La cantidad de venta debe ser al menos 1
            'detalles.*.precio_vent' => ['required', 'numeric', 'min:0'],  // El precio de venta debe ser un número mayor o igual a 0
        ];
    }

    public function messages()
    {
        return [
            'id_clie.required' => 'El cliente es obligatorio.',
            'id_clie.exists' => 'El cliente seleccionado no existe.',
            
            'detalles.required' => 'Es necesario agregar al menos un producto en los detalles de la venta.',
            'detalles.array' => 'Los detalles de la venta deben estar en formato de array.',

            'detalles.*.id_prod.required' => 'El producto es obligatorio en cada detalle de venta.',
            'detalles.*.id_prod.exists' => 'El producto seleccionado no existe.',

            'detalles.*.cantidad_vent.required' => 'La cantidad de venta es obligatoria.',
            'detalles.*.cantidad_vent.integer' => 'La cantidad de venta debe ser un número entero.',
            'detalles.*.cantidad_vent.min' => 'La cantidad de venta debe ser al menos 1.',

            'detalles.*.precio_vent.required' => 'El precio de venta es obligatorio.',
            'detalles.*.precio_vent.numeric' => 'El precio de venta debe ser un número.',
            'detalles.*.precio_vent.min' => 'El precio de venta no puede ser menor que 0.',
        ];
    }
}
