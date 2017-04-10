<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class DocumentoFinancieroRequest extends Request
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
        // return [
        // "ListaFinanciacion_idListaFinanciacion" => "required",
        //     "numeroDocumentoFinanciero" => "required"
        // ];     

        $fechaProrroga = count($this->get('fechaProrrogaDocumentoFinancieroProrroga'));

        $validacion = array(
            "ListaFinanciacion_idListaFinanciacion" => "required",
            "numeroDocumentoFinanciero" => "required");
        
        for($i = 0; $i < $fechaProrroga; $i++)
        {
            if(trim($this->get('fechaProrrogaDocumentoFinancieroProrroga')[$i]) == '')
            {  
                    $validacion['fechaProrrogaDocumentoFinancieroProrroga'.$i] =  'required';    
            }
            else
            {
                if ($i > 0) 
                {
                    $posAnt = $i - 1;
                    $validacion['fechaProrrogaDocumentoFinancieroProrroga'.$i] =  '"required|date|after:'.trim($this->get('fechaProrrogaDocumentoFinancieroProrroga')[$posAnt]).'"';
                }
            }
        }

        return $validacion;
    }
}
