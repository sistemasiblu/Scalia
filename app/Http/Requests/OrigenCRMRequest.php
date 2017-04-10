<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class OrigenCRMRequest extends Request
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
            "codigoOrigenCRM" => "required|string|max:20|unique:origencrm,codigoOrigenCRM,".$this->get('idOrigenCRM') .",idOrigenCRM",
            "nombreOrigenCRM" => "required|string|max:80"
        ];
    }
}
