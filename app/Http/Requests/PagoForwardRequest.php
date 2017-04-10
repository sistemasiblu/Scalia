<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class PagoForwardRequest extends Request
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
        $lista = count($this->get('ListaFinanciacion_idListaFinanciacion')); 
        $fecha=strftime( "%Y-%m-%d",time());
            
        $validacion = array(
            "Forward_idForward" => "required|",
            "fechaPagoForward" => "required|date|before:".$fecha,
            'valorTotalPagoForward' => 'required_with:valorDolarPagoForward|numeric|min:1',
            'valorDolarPagoForward' => 'required_with:valorTotalPagoForward|numeric|greater_than_field:valorTotalPagoForward'); 

            for($i = 0; $i < $lista; $i++)
            {
                if(trim($this->get('ListaFinanciacion_idListaFinanciacion')[$i]) == '' or trim($this->get('ListaFinanciacion_idListaFinanciacion')[$i]) == 0)
                {    
                    $validacion['ListaFinanciacion_idListaFinanciacion'.$i] =  'required';
                }
           

                // if(trim($this->get('valorFacturaPagoForwardDetalle')[$i]) < trim($this->get('valorPagadoPagoForwardDetalle')[$i]))
                // {    
                    $validacion['valorPagadoPagoForwardDetalle'.$i] = 'required_with:valorFacturaPagoForwardDetalle'.$i.'|numeric|min:1';

                    $validacion['valorFacturaPagoForwardDetalle'.$i] =  'required_with:valorPagadoPagoForwardDetalle'.$i.'|numeric|greater_than_field:valorPagadoPagoForwardDetalle'.$i;
                // }
            }

        return $validacion;
    }

    public function messages()
    {
        return 
        [
            'valorDolarPagoForward.greater_than_field' => 'El total del :attribute no debe ser mayor al valor del forward.'
        ];
    }
}
