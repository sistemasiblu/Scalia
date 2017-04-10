<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CargoRequest extends Request
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
        $tarea = count($this->get('ListaGeneral_idTareaAltoRiesgo'));
        $vacuna = count($this->get('ListaGeneral_idVacuna'));
        $elemento = count($this->get('ElementoProteccion_idElementoProteccion'));
        $examen = count($this->get('FrecuenciaMedicion_idFrecuenciaMedicion'));
        $tipoexamen = count($this->get('TipoExamenMedico_idTipoExamenMedico'));

        $validacion = array('codigoCargo' => "required|numeric|unique:cargo,codigoCargo,".$this->get('idCargo') .",idCargo,Compania_idCompania,".(\Session::get('idCompania')),
            'nombreCargo' => "required|string|max:80|unique:cargo,nombreCargo,".$this->get('idCargo') .",idCargo,Compania_idCompania,".(\Session::get('idCompania')),
            'salarioBaseCargo' => 'required|numeric');
        
        for($i = 0; $i < $tarea; $i++)
        {
            if(trim($this->get('ListaGeneral_idTareaAltoRiesgo')[$i]) == '' or trim($this->get('ListaGeneral_idTareaAltoRiesgo')[$i]) == 0)
            {    
                $validacion['ListaGeneral_idTareaAltoRiesgo'.$i] =  'required';
            }
        }

        for($i = 0; $i < $vacuna; $i++)
        {
            if(trim($this->get('ListaGeneral_idVacuna')[$i]) == '' or trim($this->get('ListaGeneral_idVacuna')[$i]) == 0)
            {    
                $validacion['ListaGeneral_idVacuna'.$i] =  'required';
            }
        }

        for($i = 0; $i < $elemento; $i++)
        {
            if(trim($this->get('ElementoProteccion_idElementoProteccion')[$i]) == '' or trim($this->get('ElementoProteccion_idElementoProteccion')[$i]) == 0)
            {    
                $validacion['ElementoProteccion_idElementoProteccion'.$i] =  'required';
            }
        }

        for($i = 0; $i < $examen; $i++)
        {
            if(trim($this->get('FrecuenciaMedicion_idFrecuenciaMedicion')[$i]) == '' or trim($this->get('FrecuenciaMedicion_idFrecuenciaMedicion')[$i]) == 0)
            {    
                $validacion['FrecuenciaMedicion_idFrecuenciaMedicion'.$i] =  'required';
            }
        }

        for($i = 0; $i < $examen; $i++)
        {
            if(trim($this->get('TipoExamenMedico_idTipoExamenMedico')[$i]) == '' or trim($this->get('TipoExamenMedico_idTipoExamenMedico')[$i]) == 0)
            {    
                $validacion['TipoExamenMedico_idTipoExamenMedico'.$i] =  'required';
            }
        }    

        return $validacion;
    }
}
