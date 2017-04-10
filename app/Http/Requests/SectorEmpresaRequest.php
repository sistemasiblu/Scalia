<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class SectorEmpresaRequest extends Request
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
            "codigoSectorEmpresa" => "required|string|max:20|unique:sectorempresa,codigoSectorEmpresa,".$this->get('idSectorEmpresa') .",idSectorEmpresa,Compania_idCompania,".(\Session::get('idCompania')),
            "nombreSectorEmpresa" => "required|string|max:80"        ];
    }
}
