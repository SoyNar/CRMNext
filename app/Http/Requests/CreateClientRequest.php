<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
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
            'name' => 'required|string|max:255',
            'nit' => 'nullable|string|max:255|unique:clients',
            'phone' => 'nullable|string|max:255',
             'url_page' => 'required|string|max:255',
            'status' => 'required|in:active,inactive,prospect',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nombre del cliente es requerido',
            'name.string' => 'Nombre de cliente debe ser de tipo texto',
            'name.max' => 'nombre del cliente debe tener 255 caracteres',
            'nit.string' => 'El correo debe ser de tipo texto',
            'nit.max' => 'el correo debe tener 255 caracteres',
            'nit.unique' => 'ese email ya esta registrado',
            'phone.string' => 'el telefono debe ser de tipo texto',
            'phone.max' => 'El telefono debe tener 255 caracteres',
            'status.required' => 'EL estado es requerido',
            'status.in' => 'El estado debe ser un estado valido',

        ];
    }
}
