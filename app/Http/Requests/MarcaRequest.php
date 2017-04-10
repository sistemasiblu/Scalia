<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class MarcaRequest extends Request
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
            "codigoMarca" => "required|string|max:20|unique:marca,codigoMarca,".$this->get('idMarca') .",idMarca",
            "nombreMarca" => "required|string|max:80",
        ];
    }
}
