<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class SitioWebRequest extends Request
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
            "descripcionSitioWeb" => "required|string|max:45",
            "urlSitioWeb" => "required|string|max:45"
        ];
    }
}