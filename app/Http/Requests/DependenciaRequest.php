<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class DependenciaRequest extends Request
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
        $localizacion = count($this->get('estadoDependenciaLocalizacion')); 

        $validacion = array(
            'codigoDependencia' => "required|unique:dependencia,codigoDependencia,".$this->get('idDependencia').",idDependencia",
            "nombreDependencia" => "required|string|max:80",
            "abreviaturaDependencia" => "required|string|max:10",
            "directorioDependencia" => "required|string|max:80");

        for($i = 0; $i < $localizacion; $i++)
        {
            if(trim($this->get('estadoDependenciaLocalizacion')[$i]) == '')
            {    
                $validacion['estadoDependenciaLocalizacion'.$i] =  'required';
            }
        }

        return $validacion;
    }

    // public function messages()
    // {
    //     $localizacion = count($this->get('estadoDependenciaLocalizacion'));

    //     for ($i=0; $i < $localizacion; $i++) 
    //     { 
    //         $pos = $i+1;

    //         if(trim($this->get('estadoDependenciaLocalizacion')[$i]) == '')
    //         {    
    //             return 
    //             [
    //                 'estadoDependenciaLocalizacion'.$i.'.required' => 'Debe seleccionar un estado en el registro '.$pos
    //             ];   
    //         }
    //     }
    // }
}
