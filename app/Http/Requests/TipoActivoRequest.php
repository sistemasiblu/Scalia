<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class TipoActivoRequest extends Request
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
            "nombreTipoActivo" => "required|string|max:80"
            //
        ];
    }
}
