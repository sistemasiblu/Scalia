<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class clasificacionCRMRequest extends Request
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

        "codigoClasificacionCRM" => "required|string|max:20|unique:clasificacioncrm,codigoClasificacionCRM,".$this->get('idClasificacionCRM') .",idClasificacionCRM",
        "nombreClasificacionCRM" => "required|string|max:80",
        "GrupoEstado_idGrupoEstado" => "required",
            
        ];

    }
}
