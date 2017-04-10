<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CompraRequest extends Request
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
        $unique = ($this->get('accion') == 'crear') ? "unique:compra,numeroCompra,".$this->get('idCompra') .",idCompra" : '';

        return [
            "Temporada_idTemporada" => "required",
            "fechaCompra" => "required|date",
            "numeroCompra" => "required|string|".$unique,
            "Tercero_idProveedor" => "required",
            "FormaPago_idFormaPago" => "required",
            "Tercero_idCliente" => "required",
            "valorCompra" => "required|max:26",
            "cantidadCompra" => "required|max:17",
            // "Ciudad_idPuerto" => "required",
            "diaPagoClienteCompra" => "integer|min:0",
            "tiempoBodegaCompra" => "integer|min:0",    
        ];
    }

    public function messages()
    {
        return [
            'Temporada_idTemporada.required' => 'No se puede ingresar una temporada manualmente, seleccione una por favor.',
            'Tercero_idProveedor.required' => 'No se puede ingresar un proveedor manualmente, seleccione uno por favor.',
            'Tercero_idCliente.required' => 'No se puede ingresar un cliente manualmente, seleccione uno por favor.',
            'FormaPago_idFormaPago.required' => 'No se puede ingresar una forma de pago del proveedor manualmente, seleccione una por favor.',
            'Ciudad_idPuerto.required' => 'No se puede ingresar una ciudad manualmente, seleccione una por favor.',
        ];
    }
}
