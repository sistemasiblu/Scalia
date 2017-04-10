<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class MensajeriaRequest extends Request
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
            "tipoCorrespondenciaMensajeria" => "required|string|max:80",
            "tipoEnvioMensajeria" => "required",
            "prioridadMensajeria" => "required|string",
            "estadoEntregaMensajeria" => "required",
            "destinatarioMensajeria" => "required",
            "fechaLimiteMensajeria" => "required|date",
        ];
    }
}