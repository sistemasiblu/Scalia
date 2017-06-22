<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

class FiltroInventarioUbicacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dependencia = DB::Select(
        'SELECT
            idDependencia as id, nombreDependencia as nombre
        FROM
            dependencialocalizacion dl
                LEFT JOIN
            dependencia d ON dl.Dependencia_idDependencia = d.idDependencia
        GROUP BY idDependencia');
        $dependencia = $this->convertirArray($dependencia);

        return view('filtroinventarioubicacion', compact('dependencia'));
    }

    function convertirArray($dato)
    {
        $nuevo = array();
        for($i = 0; $i < count($dato); $i++) 
        {
          $nuevo[get_object_vars($dato[$i])["id"]] = get_object_vars($dato[$i])["nombre"] ;
        }
        return $nuevo;
    }

    function consultarInventarioUbicacion()
    {
        if ($_GET['tipoInv'] == 'Historias') 
        {
            $AND = ($_GET['fechaFin'] != '') ? ' AND fechaInicioContrato = '.$_GET['fechaIni'].' AND fechaTerminacionContrato = '.$_GET['fechaFin'] : '';

            $historias = DB::Select('
                SELECT 
                    apellidoATercero,
                    apellidoBTercero,
                    nombreATercero,
                    nombreBTercero,
                    nombreIdentificacion,
                    documentoTercero,
                    nombreTipoSoporteDocumental,
                    estadoTercero,
                    estadoUbicacionDocumento,
                    CONCAT(descripcionDependenciaLocalizacion," ",posicionUbicacionDocumento) AS posicionUbicacionDocumento,
                    observacionUbicacionDocumento
                FROM 
                    ubicaciondocumento ud
                        LEFT JOIN
                    '.\Session::get("baseDatosCompania").'.Tercero t ON ud.Tercero_idTercero = t.idTercero
                        LEFT JOIN
                    '.\Session::get("baseDatosCompania").'.TipoIdentificacion ti ON t.TipoIdentificacion_idIdentificacion = ti.idIdentificacion
                        LEFT JOIN
                    '.\Session::get("baseDatosCompania").'.Contrato ct ON t.idTercero = ct.Tercero_idCliente
                        LEFT JOIN
                    dependencialocalizacion dl ON ud.DependenciaLocalizacion_idDependenciaLocalizacion = dl.idDependenciaLocalizacion
                        LEFT JOIN
                    tiposoportedocumental tsd ON ud.TipoSoporteDocumental_idTipoSoportedocumental = tsd.idTipoSoporteDocumental
                WHERE tipoUbicacionDocumento = "'.$_GET['tipoInv'].'"
                AND Dependencia_idDependencia = '.$_GET['dependencia'].' 
                '.$AND.'
                GROUP BY idUbicacionDocumento
                ORDER BY posicionUbicacionDocumento');

            return view('formatos.impresionInventarioHistorias',compact('historias'));
        }
        else
        {
            $AND = ($_GET['fechaFin'] != '') ? ' AND fechaInicialUbicacionDocumento = '.$_GET['fechaIni'].' AND fechaFinalUbicacionDocumento = '.$_GET['fechaFin'] : '';

            $otros = DB::Select('
                SELECT 
                    descripcionUbicacionDocumento,
                    fechaInicialUbicacionDocumento,
                    fechaFinalUbicacionDocumento,
                    numeroFolioUbicacionDocumento,
                    nombreTipoSoporteDocumental,
                    nombreDependencia,
                    nombreCompania,
                    CONCAT(descripcionDependenciaLocalizacion," ",posicionUbicacionDocumento) AS posicionUbicacionDocumento,
                    estadoUbicacionDocumento,
                    observacionUbicacionDocumento
                FROM 
                    ubicaciondocumento ud
                        LEFT JOIN
                    dependencia d ON ud.Dependencia_idProductora = d.idDependencia
                        LEFT JOIN
                    dependencialocalizacion dl ON ud.DependenciaLocalizacion_idDependenciaLocalizacion = dl.idDependenciaLocalizacion
                        LEFT JOIN
                    tiposoportedocumental tsd ON ud.TipoSoporteDocumental_idTipoSoportedocumental = tsd.idTipoSoporteDocumental
                        left join 
                    compania c ON ud.Compania_idCompania = c.idCompania
                WHERE tipoUbicacionDocumento = "'.$_GET['tipoInv'].'"
                AND Dependencia_idDependencia = '.$_GET['dependencia'].' 
                '.$AND.'
                ORDER BY posicionUbicacionDocumento ASC');

            return view('formatos.impresionInventarioDocumental',compact('otros'));
        }
    }

    
}
