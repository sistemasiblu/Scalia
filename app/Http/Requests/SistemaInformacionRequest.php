<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class SistemaInformacionRequest extends Request
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
            "codigoSistemaInformacion" => "required|string|max:10|unique:sistemainformacion,codigoSistemaInformacion,".$this->get('idSistemaInformacion') .",idSistemaInformacion",
            "nombreSistemaInformacion" => "required|string|max:80",
            "ipSistemaInformacion" => "required|string|max:16",
            "puertoSistemaInformacion" => "required|string|max:10",
            "usuarioSistemaInformacion" => "required|string|max:25",
            "bdSistemaInformacion" => "required|string|max:45",
            "motorbdSistemaInformacion" => "required|string|max:45"

        ];     
    }
}
