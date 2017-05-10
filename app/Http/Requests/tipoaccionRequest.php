<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class tipoaccionRequest extends Request
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
         "codigoTipoAccion" => "required|string|max:20|unique:tipoaccion,codigoTipoAccion,".$this->get('idTipoAccion') .",idTipoAccion",
            "nombreTipoAccion" => "required|string|max:80"
            //
        ];
    }
}
