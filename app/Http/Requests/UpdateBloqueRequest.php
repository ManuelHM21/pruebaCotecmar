<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBloqueRequest extends FormRequest
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
            'proyecto_id' => 'required|exists:proyectos,id',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del bloque es obligatorio',
            'proyecto_id.required' => 'Debe seleccionar un proyecto',
            'proyecto_id.exists' => 'El proyecto seleccionado no existe',
        ];
    }
}
