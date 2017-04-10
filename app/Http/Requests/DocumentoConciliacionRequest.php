<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class DocumentoConciliacionRequest extends Request
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
            "codigoDocumentoConciliacion" => "required|string|max:20|unique:documentoconciliacion,codigoDocumentoConciliacion,".$this->get('idDocumentoConciliacion') .",idDocumentoConciliacion,Compania_idCompania,".(\Session::get('idCompania')),
            "nombreDocumentoConciliacion" => "required|string|max:80"
        ];
    }
}
