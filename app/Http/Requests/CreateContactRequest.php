<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateContactRequest extends FormRequest
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'position' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'is_primary' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Nombre es requerido',
            'first_name.string' => 'Nombre deber ser de tipo texto',
            'last_name.required' => 'Apellido es requerido',
            'last_name.string' => 'Apellido deber ser de tipo texto',
            'email.required' => 'Correo es requerido',
            'email.string' => 'Correo de  tipo texto',
            'position.required' => 'posicion es requerido',
            'position.string' => 'Posicion deberia ser de tipo texto',
            'phone.numeric' => 'telefono debe ser numerico',
            'is_primary.required' => 'estado es requerido',
            'is_primary.boolean' => 'estado deberia ser de tipo boolean',

        ];
    }
}
