<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProyectoRequest extends FormRequest
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
            'id' => 'required|unique:proyectos,id|max:50',
            'nombre' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'El ID del proyecto es obligatorio',
            'id.unique' => 'Este ID de proyecto ya existe',
            'nombre.required' => 'El nombre del proyecto es obligatorio',
        ];
    }
}
