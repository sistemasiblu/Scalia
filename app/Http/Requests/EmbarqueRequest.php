<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class EmbarqueRequest extends Request
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
        $pago = count($this->get('pagoEmbarqueDetalle')); 
        $fechaReal = count($this->get('fechaRealEmbarqueDetalle')); 
        

        $validacion = array("numeroEmbarque" => "required|string|unique:embarque,numeroEmbarque,".$this->get('idEmbarque') .",idEmbarque",
            "tipoTransporteEmbarque" => "required|string|max:80",
            "puertoCargaEmbarque" => "required|string|max:80",
            "puertoDescargaEmbarque" => "required|string|max:80");

            if($this->get('bodegaEmbarque') == 'on')
            {
                $validacion["fechaRealEmbarque"] = "required";    
            }

            if($this->get('otmEmbarque') == 'on')
            {
                $validacion["fechaRealEmbarque"] = "required";    
            }

            // for($i = 0; $i < $pago; $i++)
            // {
            //     if(trim($this->get('pagoEmbarqueDetalle')[$i]) == '1')
            //     {    
            //         $validacion['fechaRealEmbarque'] =  'required';
            //     }
            // }    

            // for($i = 0; $i < $fechaReal; $i++)
            // {
            //     $fechaReserva = $this->get('fechaReservaEmbarqueDetalle'.$i); 
            //     if(trim($this->get('fechaRealEmbarqueDetalle')[$i]) < trim($this->get('fechaReservaEmbarqueDetalle')[$i]))
            //     {    
            //         $validacion['fechaRealEmbarqueDetalle'.$i] =  'required|after_or_equal:'.$fechaReserva;
            //     }
            // }    

        
        return $validacion;
    }

    public function messages()
    {
        return 
        [
            'fechaRealEmbarqueDetalle.greater_than_field' => 'El total del :attribute no debe ser mayor al valor del forward.'
        ];
    }
}
