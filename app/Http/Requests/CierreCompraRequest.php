<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CierreCompraRequest extends Request
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
        $validacion = array("numeroCierreCompra" => "required|string|unique:cierrecompra,numeroCierreCompra,".$this->get('idCierreCompra') .",idCierreCompra",
            "fechaCierreCompra" => "required|date");

            if($this->get('nombreProveedorCierreCompra') != '' && $this->get('Tercero_idProveedor') == '')
            {
                $validacion["Tercero_idProveedor"] = "required";    
            }
        
        return $validacion;
    }

    public function messages()
    {
        return 
        [
            'Tercero_idProveedor.required' => 'No se puede ingresar un proveedor manualmente, seleccione uno por favor.',
        ];
    }
}
