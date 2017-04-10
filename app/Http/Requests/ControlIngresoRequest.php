<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Input;
use Validator;
use Response;

class ControlIngresoRequest extends Request
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
        // $dispositivo = count($this->get('Dispositivo_idDispositivo')); 
        // $marca = count($this->get('Marca_idMarca')); 
        // $retiro = count($this->get('retiraDispositivoControlIngresoDetalle'));

        // $validacion = array("TipoIdentificacion_idTipoIdentificacion" => "required|max:0",
        //     "numeroDocumentoVisitanteControlIngreso" => "required|int",
        //     "nombreVisitanteControlIngreso" => "required|string|max:80",
        //     "apellidoVisitanteControlIngreso" => "required|string|max:80",
        //     "Tercero_idResponsable" => "required|max:0");

        //     for($i = 0; $i < $dispositivo; $i++)
        //     {
        //         if(trim($this->get('Dispositivo_idDispositivo')[$i]) == '' or trim($this->get('Dispositivo_idDispositivo')[$i]) == 0)
        //         {    
        //             $validacion['Dispositivo_idDispositivo'.$i] =  'required';
        //         }
        //     }

        //     for($i = 0; $i < $marca; $i++)
        //     {
        //         if(trim($this->get('Marca_idMarca')[$i]) == '' or trim($this->get('Marca_idMarca')[$i]) == 0)
        //         {    
        //             $validacion['Marca_idMarca'.$i] =  'required';
        //         }
        //     }

        //     for($i = 0; $i < $retiro; $i++)
        //     {
        //         if(trim($this->get('retiraDispositivoControlIngresoDetalle')[$i]) == 0)
        //         {    
        //             $validacion['observacionControlIngresoDetalle'.$i] =  'required';
        //         }
        //     }

        // return $validacion;

        return [
            "TipoIdentificacion_idTipoIdentificacion" => "required",
            "numeroDocumentoVisitanteControlIngreso" => "required|int",
            "nombreVisitanteControlIngreso" => "required|string|max:80",
            "apellidoVisitanteControlIngreso" => "required|string|max:80",
            "Tercero_idResponsable" => "required"];

            // $validator = Validator::make(Input::all(), [
            //      "TipoIdentificacion_idTipoIdentificacion" => "required|max:0",
            //     "numeroDocumentoVisitanteControlIngreso" => "required|int",
            //     "nombreVisitanteControlIngreso" => "required|string|max:80",
            //     "apellidoVisitanteControlIngreso" => "required|string|max:80",
            //     "Tercero_idResponsable" => "required|max:0"
            // ]);

            // if ($validator->fails()) {    
            //     return response()->json($validator->messages(), 200);
            // }
                    
    }
}
