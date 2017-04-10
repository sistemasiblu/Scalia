<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class LineaNegocioRequest extends Request
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
        
        return[
            "codigoLineaNegocio" => "required|string|max:20|unique:lineanegocio,codigoLineaNegocio,".$this->get('idLineaNegocio') .",idLineaNegocio,Compania_idCompania,".(\Session::get('idCompania')),
            "nombreLineaNegocio" => "required|string|max:80"
        ];
    }
}
