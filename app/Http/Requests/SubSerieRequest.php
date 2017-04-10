<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class SubSerieRequest extends Request
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
        $documento = count($this->get('Documento_idDocumento')); 
        
        $validacion = array("codigoSubSerie" => "required|string|unique:subserie,codigoSubSerie,".$this->get('idSubSerie') .",idSubSerie",
            "nombreSubSerie" => "required|string|max:80",
            "directorioSubSerie" => "required|string|max:80",
            "Serie_idSerie" => "required"); 

            for($i = 0; $i < $documento; $i++)
            {
                if(trim($this->get('Documento_idDocumento')[$i]) == '' or trim($this->get('Documento_idDocumento')[$i]) == 0)
                {    
                    $validacion['Documento_idDocumento'.$i] =  'required';
                }
            }

        return $validacion;
    }
}
