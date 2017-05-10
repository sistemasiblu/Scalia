<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class activoRequest extends Request
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
        "codigoActivo" => "required|string|max:20|unique:activo,codigoActivo,".$this->get('idActivo') .",idActivo",
            "nombreActivo" => "required|string|max:80",

           'TipoActivo_idTipoActivo'=>"required",
            'codigobarraActivo'=>"required",
            'estadoActivo'=>"required",
            'clasificacionActivo'=>"required",
            'marcaActivo'=>"required",
            'serieActivo'=>"required",
            'pesoActivo'=>"required",
            'altoActivo'=>"required",
            'anchoActivo'=>"required",
            'largoActivo'=>"required",
            'modeloActivo'=>"required",
            'volumenActivo'=>"required",
            
        ];
    }
}
