<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ForwardRequest extends Request
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
        $validacion = array("numeroForward" => "required|unique:forward,numeroForward,".$this->get('idForward') .",idForward",
            "fechaVencimientoForward" => "required|date|after:fechaNegociacionForward",
            'totalForward' => 'required_with:valorDolarForward|numeric',
            // 'valorDolarForward' => 'required_with:totalForward|numeric|greater_than_field:totalForward',
            "Tercero_idBanco" => "required|int");
        
        return $validacion;
    }

    // public function messages()
    // {
    //     return 
    //     [
    //         'valorDolarForward.greater_than_field' => 'El total del :attribute no debe ser mayor al valor del forward.'
    //     ];
    // }
}
