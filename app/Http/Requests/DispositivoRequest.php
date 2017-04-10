<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class DispositivoRequest extends Request
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
            "codigoDispositivo" => "required|string|max:20|unique:dispositivo,codigoDispositivo,".$this->get('idDispositivo') .",idDispositivo",
            "nombreDispositivo" => "required|string|max:80",
        ];
    }
}
