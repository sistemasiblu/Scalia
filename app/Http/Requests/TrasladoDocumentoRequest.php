<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class TrasladoDocumentoRequest extends Request
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
        $documento = count($this->get('documentoDestinoTrasladoDocumentoDetalle')); 
        $concepto = count($this->get('documentoConceptoDestinoTrasladoDocumentoDetalle')); 
        $tercero = count($this->get('terceroDestinoTrasladoDocumentoDetalle')); 
        

        $validacion = array(
            "numeroTrasladoDocumento" => "required|string|max:10|unique:trasladodocumento,numeroTrasladoDocumento,".$this->get('idTrasladoDocumento') .",idTrasladoDocumento",
            "descripcionTrasladoDocumento" => "required|string|max:80",
            "fechaElaboracionTrasladoDocumento" => "required|date",
            "estadoTrasladoDocumento" => "required|string|max:10",
            "SistemaInformacion_idOrigen" => "required|int",
            "SistemaInformacion_idDestino" => "required|int");

            if($this->get('bodegaEmbarque') == 'on')
            {
                $validacion["fechaRealEmbarque"] = "required";    
            }

            if($this->get('otmEmbarque') == 'on')
            {
                $validacion["fechaRealEmbarque"] = "required";    
            }

            for($i = 0; $i < $documento; $i++)
            {
                if(trim($this->get('documentoDestinoTrasladoDocumentoDetalle')[$i]) == '')
                {    
                    $validacion['documentoDestinoTrasladoDocumentoDetalle'.$i] =  'required';
                }
            }  

            for($i = 0; $i < $concepto; $i++)
            {
                if(trim($this->get('documentoConceptoDestinoTrasladoDocumentoDetalle')[$i]) == '')
                {    
                    $validacion['documentoConceptoDestinoTrasladoDocumentoDetalle'.$i] =  'required';
                }
            } 

            for($i = 0; $i < $tercero; $i++)
            {
                if(trim($this->get('terceroDestinoTrasladoDocumentoDetalle')[$i]) == '')
                {    
                    $validacion['terceroDestinoTrasladoDocumentoDetalle'.$i] =  'required';
                }
            }   

        
        return $validacion; 
    }
}
