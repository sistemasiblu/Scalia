<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class tipoactivodocumentoRequest extends Request
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
        return 
        [
        "codigoTipoActivo" => "required|string|max:20|unique:tipoactivo,codigoTipoActivo,".$this->get('idTipoActivo') .",idTipoActivo",
            "nombreTipoActivo" => "required|string|max:80",
             "nombreTipoActivoCaracteristica"=>"required|string|max:80",
             "descripcionTipoActivoDocumento"=>"required|string|max:80",
             "serialTipoActivoDocumento"=>"required|string|max:20",
             "tipoTipoActivoDocumento"=>"required|string|max:5",
             "vigenciaTipoActivoDocumento"=>"required|string|max:15",
             "costoTipoActivoDocumento"=>"required|string|max:10",


            //
        ];
    }
}
