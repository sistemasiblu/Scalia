<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class DocumentoCRMRequest extends Request
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
        
        $campo = count($this->get('CampoCRM_idCampoCRM'));
        $compania = count($this->get('Compania_idCompania'));
        $rol = count($this->get('Rol_idRol'));

        $validacion = array('codigoDocumentoCRM' => "required|string|unique:documentocrm,codigoDocumentoCRM,".$this->get('idDocumentoCRM') .",idDocumentoCRM",
            'nombreDocumentoCRM' => "required|string|max:80|unique:documentocrm,nombreDocumentoCRM,".$this->get('idDocumentoCRM') .",idDocumentoCRM");
        

        for($i = 0; $i < $campo; $i++)
        {
            if(trim($this->get('CampoCRM_idCampoCRM')[$i]) == '' or trim($this->get('CampoCRM_idCampoCRM')[$i]) == 0)
            {    
                $validacion['CampoCRM_idCampoCRM'.$i] =  'required';
            }
        }

        for($i = 0; $i < $compania; $i++)
        {
            if(trim($this->get('Compania_idCompania')[$i]) == '' or trim($this->get('Compania_idCompania')[$i]) == 0)
            {    
                $validacion['Compania_idCompania'.$i] =  'required';
            }
        }    

        for($i = 0; $i < $rol; $i++)
        {
            if(trim($this->get('Rol_idRol')[$i]) == '' or trim($this->get('Rol_idRol')[$i]) == 0)
            {    
                $validacion['Rol_idRol'.$i] =  'required';
            }
        }    

        return $validacion;
    }
}
