<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class SerieRequest extends Request
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
            "directorioSerie" => "required|string|max:85",
            "codigoSerie" => "required|string|max:10|unique:serie,codigoSerie,".$this->get('idSerie') .",idSerie",
            "nombreSerie" => "required|string|max:80"

        ];     
    }
}
