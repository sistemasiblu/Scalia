<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ListaRequest extends Request
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
            "codigoLista" => "required|string|max:10|unique:lista,codigoLista,".$this->get('idLista') .",idLista",
            "nombreLista" => "required|string|max:80"

        ];     
    }
}
