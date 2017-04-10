<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class GrupoEstadoRequest extends Request
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
        $detalle = count($this->get('nombreEstadoCRM'));
        
        $validacion = array(
            "codigoGrupoEstado" => "required|unique:grupoestado,codigoGrupoEstado,".$this->get('idGrupoEstado') .",idGrupoEstado,Compania_idCompania,".(\Session::get('idCompania')),
            "nombreGrupoEstado" => "required|string|max:80");

        for($i = 0; $i < $detalle; $i++)
        {
            if(trim($this->get('nombreEstadoCRM')[$i]) == '')
            {    
                $validacion['nombreEstadoCRM'.$i] =  'required';
            }

            if(trim($this->get('tipoEstadoCRM')[$i]) == '')
            {    
                $validacion['tipoEstadoCRM'.$i] =  'required';
            }
        }
        return $validacion;
    }
}
