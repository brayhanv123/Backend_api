<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompraRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'fecha_comp' => ['required', 'date'],
            'total_comp' => ['required', 'numeric'],
            'id_prov' => ['required', 'exists:tb_proveedores,id_prov'],
            'detalles' => ['required', 'array'],
            'detalles.*.id_prod' => ['required', 'exists:tb_productos,id_prod'],
            'detalles.*.cantidad_comp' => ['required', 'integer', 'min:1'],
            'detalles.*.precio_comp' => ['required', 'numeric'],
        ];
    }

    public function messages()
    {
        return [
            'fecha_comp.required' => 'La fecha de compra es obligatoria.',
            'total_comp.required' => 'El total de la compra es obligatorio.',
            'id_prov.required' => 'El proveedor es obligatorio.',
            'detalles.required' => 'Los detalles de la compra son obligatorios.',
        ];
    }
}
