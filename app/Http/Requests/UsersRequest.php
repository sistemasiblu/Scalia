<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class UsersRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "name" => "required|string|max:255",
            "email" => "required|email|max:255|unique:users,email,".$this->get('id') .",id",
            "password" => "required|min:4|max:20|confirmed",
            "password_confirmation" => "required|min:4|max:20",
            "Compania_idCompania" => "required",
            "Rol_idRol" => "required"
        ];
    }
}
