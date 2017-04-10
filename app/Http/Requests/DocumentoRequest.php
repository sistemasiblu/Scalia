<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class DocumentoRequest extends Request
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
        //     "codigoDocumento" => "required|string|max:10|unique:documento,codigoDocumento,".$this->get('idDocumento') .",idDocumento",
        //     "nombreDocumento" => "required|string|max:80",
        //     "directorioDocumento" => "required|string|max:85",
        //     "tipoDocumento" => "required|string|max:85"
        // ];     
        $validacion = array(
            "codigoDocumento" => "string|max:10|unique:documento,codigoDocumento,".$this->get('idDocumento') .",idDocumento",
            "nombreDocumento" => "required|string|max:80",
            "directorioDocumento" => "required|string|max:85",
            "tipoDocumento" => "required");
        
        return $validacion;
    }
}
