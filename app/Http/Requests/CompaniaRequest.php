<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CompaniaRequest extends Request
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
            "codigoCompania" => "required|string|max:20|unique:compania,codigoCompania,".$this->get('idCompania') .",idCompania",
            "nombreCompania" => "required|string|max:80",
            "directorioCompania" => "required|string|max:80"
        ];
    }
}
