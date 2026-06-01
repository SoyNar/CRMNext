<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'nit' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[\d\.]{6,15}-\d{1}$/',
                Rule::unique('clients')->ignore($this->route('client')),
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^\+?[\d\s\-]{7,15}$/',
            ],
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
            'nit.regex' => 'El NIT debe tener el formato 900.000.000-0',
            'phone.regex' => 'EL telefono debe tener el formato 301 458 96 32',
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
