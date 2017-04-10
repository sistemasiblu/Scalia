<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class DependenciaRequest extends Request
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
            "codigoDependencia" => "required|string|max:10|unique:dependencia,codigoDependencia,".$this->get('idDependencia') .",idDependencia",
            "nombreDependencia" => "required|string|max:80",
            "abreviaturaDependencia" => "required|string|max:10",
            "directorioDependencia" => "required|string|max:80",
        ];     
    }
}
