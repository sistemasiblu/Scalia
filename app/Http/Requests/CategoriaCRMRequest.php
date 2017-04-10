<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CategoriaCRMRequest extends Request
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
            "codigoCategoriaCRM" => "required|string|max:20|unique:categoriacrm,codigoCategoriaCRM,".$this->get('idCategoriaCRM') .",idCategoriaCRM",
            "nombreCategoriaCRM" => "required|string|max:80"
        ];
    }
}
