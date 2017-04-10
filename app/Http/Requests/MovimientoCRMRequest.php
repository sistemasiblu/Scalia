<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\DB;
class MovimientoCRMRequest extends Request
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
        
        $id = $this->get('DocumentoCRM_idDocumentoCRM'); 
        $rolUsuario = $this->get('rolUsuario'); 


        // Consultamos los campos que deben ser obligatorios, 
        // excluyendo los que se muestran en el modal de asignacion de asesor
        $campos = DB::select(
            'SELECT codigoDocumentoCRM, nombreDocumentoCRM, nombreCampoCRM,descripcionCampoCRM, mostrarGridDocumentoCRMCampo, 
                relacionTablaCampoCRM, relacionNombreCampoCRM, relacionAliasCampoCRM
            FROM documentocrm
            left join documentocrmcampo
            on documentocrm.idDocumentoCRM = documentocrmcampo.DocumentoCRM_idDocumentoCRM
            left join campocrm
            on documentocrmcampo.CampoCRM_idCampoCRM = campocrm.idCampoCRM
            where documentocrm.idDocumentoCRM = '.$id.' and 
               obligatorioDocumentoCRMCampo = 1  and 
               nombreCampoCRM NOT IN ("Tercero_idAsesor","AcuerdoServicio_idAcuerdoServicio","Tercero_idSupervisor") and 
                '.$rolUsuario.'DocumentoCRMCampo = 1');

        $validacion = array();
        $validacion["numeroMovimientoCRM"] = "required|string|unique:movimientocrm,numeroMovimientoCRM,".$this->get('idMovimientoCRM') .",idMovimientoCRM,Compania_idCompania,".(\Session::get('idCompania')).",DocumentoCRM_idDocumentoCRM,".$this->get('DocumentoCRM_idDocumentoCRM');
        $validacion["asuntoMovimientoCRM"] = "required|string|max:100";

        for($i = 0; $i < count($campos); $i++)
        {
            $datos = get_object_vars($campos[$i]); 
            $validacion[$datos["nombreCampoCRM"]] = "required";

            // si el campo de clasificacion esta marcado como obligatorio, adicionamos tambien la subclasificaciona que deben siempre llenarse juntos
            // if($datos["nombreCampoCRM"] == 'ClasificacionCRM_idClasificacionCRM')
            //     $validacion["ClasificacionCRMDetalles_idClasificacionCRMDetalles"] = "required";                
        }

        return $validacion;
    }

    public function messages()
    {
        $id = $this->get('DocumentoCRM_idDocumentoCRM');
        $rolUsuario = $this->get('rolUsuario'); 

        $campos = DB::select(
            'SELECT codigoDocumentoCRM, nombreDocumentoCRM, nombreCampoCRM,descripcionCampoCRM, mostrarGridDocumentoCRMCampo, 
                relacionTablaCampoCRM, relacionNombreCampoCRM, relacionAliasCampoCRM
            FROM documentocrm
            left join documentocrmcampo
            on documentocrm.idDocumentoCRM = documentocrmcampo.DocumentoCRM_idDocumentoCRM
            left join campocrm
            on documentocrmcampo.CampoCRM_idCampoCRM = campocrm.idCampoCRM
            where documentocrm.idDocumentoCRM = '.$id.' and 
                obligatorioDocumentoCRMCampo = 1  and 
                nombreCampoCRM NOT IN ("Tercero_idAsesor","AcuerdoServicio_idAcuerdoServicio","Tercero_idSupervisor") and 
                '.$rolUsuario.'DocumentoCRMCampo = 1');

        $mensajes = array();
        $mensajes["numeroMovimientoCRM.required"] = "El campo Número es obligatorio en modo Manual";
        $mensajes["asuntoMovimientoCRM.required"] = "El campo Asunto es obligatorio";

        for($i = 0; $i < count($campos); $i++)
        {
            $datos = get_object_vars($campos[$i]); 
            $mensajes[$datos["nombreCampoCRM"].'.required'] = "El campo ".$datos["descripcionCampoCRM"]." es obligatorio";
        }
        return $mensajes;

    }


}
