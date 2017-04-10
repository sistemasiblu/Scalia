<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class PresupuestoRequest extends Request
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
        $vendedor = count($this->get('Tercero_idVendedor'));

        $validacion = array(
            "fechaInicialPresupuesto" => "required|date",
            "fechaFinalPresupuesto" => "required|date",
            "descripcionPresupuesto" => "required|string",
            "DocumentoCRM_idDocumentoCRM" => "required|string",
            );
        
        for($i = 0; $i < $vendedor; $i++)
        {
            if(trim($this->get('Tercero_idVendedor')[$i]) == '' or trim($this->get('Tercero_idVendedor')[$i]) == 0)
            {    
                $validacion['Tercero_idVendedor'.$i] =  'required';
            }
        }
        return $validacion;
    }
}