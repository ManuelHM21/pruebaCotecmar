<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePiezaRequest extends FormRequest
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
            'nombre' => 'required|string|max:255',
            'peso_teorico' => 'required|numeric|min:0',
            'bloque_id' => 'required|exists:bloques,id',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre de la pieza es obligatorio',
            'peso_teorico.required' => 'El peso teórico es obligatorio',
            'peso_teorico.numeric' => 'El peso teórico debe ser un número',
            'peso_teorico.min' => 'El peso teórico no puede ser negativo',
            'bloque_id.required' => 'Debe seleccionar un bloque',
            'bloque_id.exists' => 'El bloque seleccionado no existe',
        ];
    }
}
