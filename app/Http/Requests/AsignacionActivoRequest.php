<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class AsignacionActivoRequest extends Request
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
       

       $activo = count($this->get('idAsignacionActivoDetalle'));
       
        
        $validacion=array();
        $validacion = [ 'numeroAsignacionActivo'=>"required|string|max:10|unique:asignacionactivo,numeroAsignacionActivo,".$this->get('idAsignacionActivo') .",idAsignacionActivo",
            'fechaHoraAsignacionActivo'=>"required",
            'TransaccionActivo_idTransaccionActivo'=>"required",
            'documentoInternoAsignacionActivo'=>"required",
            'Users_idCrea'=>"required"];
        

        for($i = 0; $i < $activo; $i++)
        {
            
            $validacion['AsignacionActivo_idAsignacionActivo'.$i] =  'required';
            $validacion['MovimientoActivo_idMovimientoActivo'.$i] =  'required';
            $validacion['Activo_idActivo'.$i] =  "required|string|max:10|unique:asignacionactivodetalle,Activo_idActivo,".$this->get('idAsignacionActivoDetalle') .",idAsignacionActivoDetalle";
            $validacion['Localizacion_idLocalizacion'.$i] =  'required';
            $validacion['Tercero_idResponsable'.$i] =  'required';


        }


                   /* $posAnt = $i - 1;
                    $validacion['fechaProrrogaDocumentoFinancieroProrroga'.$i] =  '"required|date|after:'.trim($this->get('fechaProrrogaDocumentoFinancieroProrroga')[$posAnt]).'"';*/

           

        return $validacion;

            
            
        
    }
}
