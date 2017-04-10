<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class EventoCRMRequest extends Request
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
            "codigoEventoCRM" => "required|string|max:20|unique:eventocrm,codigoEventoCRM,".$this->get('idEventoCRM') .",idEventoCRM",
            "nombreEventoCRM" => "required|string|max:80"
        ];
    }
}
