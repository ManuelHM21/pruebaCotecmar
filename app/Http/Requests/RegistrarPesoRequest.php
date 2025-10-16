<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistrarPesoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'pieza_id' => [
                'required',
                'exists:piezas,id',
                function ($attribute, $value, $fail) {
                    $pieza = \App\Models\Pieza::find($value);
                    if ($pieza && $pieza->estado !== 'Pendiente') {
                        $fail('La pieza seleccionada ya ha sido fabricada.');
                    }
                },
            ],
            'peso_real' => [
                'required',
                'numeric',
                'min:0.01',
                'max:999999.99',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'pieza_id.required' => 'Debe seleccionar una pieza',
            'pieza_id.exists' => 'La pieza seleccionada no existe',
            'peso_real.required' => 'El peso real es obligatorio',
            'peso_real.numeric' => 'El peso real debe ser un número',
            'peso_real.min' => 'El peso real debe ser mayor a 0',
            'peso_real.max' => 'El peso real no puede exceder 999999.99 kg',
        ];
    }
}
