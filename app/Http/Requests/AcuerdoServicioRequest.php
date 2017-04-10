<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class AcuerdoServicioRequest extends Request
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
            "codigoAcuerdoServicio" => "required|string|max:20|unique:acuerdoservicio,codigoAcuerdoServicio,".$this->get('idAcuerdoServicio') .",idAcuerdoServicio",
            "nombreAcuerdoServicio" => "required|string|max:80",
            "tiempoAcuerdoServicio" => "required",
            "unidadTiempoAcuerdoServicio" => "required"
            
        ];
    }
}
