<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\GrupoEstadoRequest;
use App\Http\Controllers\Controller;
use DB;
include public_path().'/ajax/consultarPermisos.php';

class GrupoEstadoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $vista = basename($_SERVER["PHP_SELF"]);
        $datos = consultarPermisos($vista);

        if($datos != null)
            return view('grupoestadogrid', compact('datos'));
        else
            return view('accesodenegado');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('grupoestado');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        if($request['respuesta'] != 'falso')
        {
            \App\GrupoEstado::create([
            'codigoGrupoEstado' => $request['codigoGrupoEstado'],
            'nombreGrupoEstado' => $request['nombreGrupoEstado'],
            'Compania_idCompania' => \Session::get("idCompania")
            ]);

            $grupoEstado = \App\GrupoEstado::All()->last();
            
            //---------------------------------
            // guardamos las tablas de detalle
            //---------------------------------
            $this->grabarDetalle($grupoEstado->idGrupoEstado, $request);
            
             return redirect('/grupoestado');
         }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $compania=\Session::get('baseDatosCompania');


       $asesoresg = DB::select(
        "select idGrupoEstadoAsesor, Tercero_idAsesor, tercero.nombre1Tercero
        from grupoestadoasesor 
        left join ".$compania.".Tercero as tercero
        on grupoestadoasesor.Tercero_idAsesor=tercero.idTercero  
        WHERE GrupoEstado_idGrupoEstado = ".$id);
            
        for ($i=0 ; $i < count( $asesoresg); $i++) 
        {  
            $asesor[] = get_object_vars($asesoresg[$i]);
        }

        $grupoEstado = \App\GrupoEstado::find($id);
        return view('grupoestado',compact('grupoEstado','asesor'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        if($request['respuesta'] != 'falso')
        {
            $grupoEstado = \App\GrupoEstado::find($id);
            $grupoEstado->fill($request->all());
            $grupoEstado->save();

            //---------------------------------
            // guardamos las tablas de detalle
            //---------------------------------
            $this->grabarDetalle($grupoEstado->idGrupoEstado, $request);
            
            return redirect('/grupoestado');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        \App\GrupoEstado::destroy($id);
        return redirect('/grupoestado');
    }

    protected function grabarDetalle($id, $request)
    {
        // -----------------------------------
        // ESTADOS
        // en el formulario hay un campo oculto en el que almacenamos los id que se eliminan separados por coma
        // en este proceso lo convertimos en array y eliminamos dichos id de la tabla de detalle
        $idsEliminar = explode(',', $request['eliminarEstado']);
        \App\EstadoCRM::whereIn('idEstadoCRM',$idsEliminar)->delete();

        $contadorEstado = count($request['nombreEstadoCRM']);
        for($i = 0; $i < $contadorEstado; $i++)
        {
            $indice = array(
             'idEstadoCRM' => $request['idEstadoCRM'][$i]);

            $data = array(
             'GrupoEstado_idGrupoEstado' => $id,
            'nombreEstadoCRM' => $request['nombreEstadoCRM'][$i],
            'tipoEstadoCRM' => $request['tipoEstadoCRM'][$i] );

            $guardar = \App\EstadoCRM::updateOrCreate($indice, $data);

        }
        
        // -----------------------------------
        // EVENTOS
        // en el formulario hay un campo oculto en el que almacenamos los id que se eliminan separados por coma
        // en este proceso lo convertimos en array y eliminamos dichos id de la tabla de detalle
        $idsEliminar = explode(',', $request['eliminarEvento']);
        \App\EventoCRM::whereIn('idEventoCRM',$idsEliminar)->delete();

        $contadorEvento = count($request['nombreEventoCRM']);
        for($i = 0; $i < $contadorEvento; $i++)
        {
            $indice = array(
             'idEventoCRM' => $request['idEventoCRM'][$i]);

            $data = array(
             'GrupoEstado_idGrupoEstado' => $id,
            'codigoEventoCRM' => $request['codigoEventoCRM'][$i],
            'nombreEventoCRM' => $request['nombreEventoCRM'][$i] );

            $guardar = \App\EventoCRM::updateOrCreate($indice, $data);

        }


        // -----------------------------------
        // CATEGORIAS
        // en el formulario hay un campo oculto en el que almacenamos los id que se eliminan separados por coma
        // en este proceso lo convertimos en array y eliminamos dichos id de la tabla de detalle
        $idsEliminar = explode(',', $request['eliminarCategoria']);
        \App\CategoriaCRM::whereIn('idCategoriaCRM',$idsEliminar)->delete();

        $contadorCategoria = count($request['nombreCategoriaCRM']);
        for($i = 0; $i < $contadorCategoria; $i++)
        {
            $indice = array(
             'idCategoriaCRM' => $request['idCategoriaCRM'][$i]);

            $data = array(
             'GrupoEstado_idGrupoEstado' => $id,
            'codigoCategoriaCRM' => $request['codigoCategoriaCRM'][$i],
            'nombreCategoriaCRM' => $request['nombreCategoriaCRM'][$i] );

            $guardar = \App\CategoriaCRM::updateOrCreate($indice, $data);

        }


        // -----------------------------------
        // ORIGENES
        // en el formulario hay un campo oculto en el que almacenamos los id que se eliminan separados por coma
        // en este proceso lo convertimos en array y eliminamos dichos id de la tabla de detalle
        $idsEliminar = explode(',', $request['eliminarOrigen']);
        \App\OrigenCRM::whereIn('idOrigenCRM',$idsEliminar)->delete();

        $contadorOrigen = count($request['nombreOrigenCRM']);
        for($i = 0; $i < $contadorOrigen; $i++)
        {
            $indice = array(
             'idOrigenCRM' => $request['idOrigenCRM'][$i]);

            $data = array(
             'GrupoEstado_idGrupoEstado' => $id,
            'codigoOrigenCRM' => $request['codigoOrigenCRM'][$i],
            'nombreOrigenCRM' => $request['nombreOrigenCRM'][$i] );

            $guardar = \App\OrigenCRM::updateOrCreate($indice, $data);

        }


        // -----------------------------------
        // ACUERDOS DE SERVICIO
        // en el formulario hay un campo oculto en el que almacenamos los id que se eliminan separados por coma
        // en este proceso lo convertimos en array y eliminamos dichos id de la tabla de detalle
        $idsEliminar = explode(',', $request['eliminarAcuerdo']);
        \App\AcuerdoServicio::whereIn('idAcuerdoServicio',$idsEliminar)->delete();

        $contadorAcuerdoServicio = count($request['nombreAcuerdoServicio']);
        for($i = 0; $i < $contadorAcuerdoServicio; $i++)
        {
            $indice = array(
             'idAcuerdoServicio' => $request['idAcuerdoServicio'][$i]);

            $data = array(
             'GrupoEstado_idGrupoEstado' => $id,
            'codigoAcuerdoServicio' => $request['codigoAcuerdoServicio'][$i],
            'nombreAcuerdoServicio' => $request['nombreAcuerdoServicio'][$i],
            'tiempoAcuerdoServicio' => $request['tiempoAcuerdoServicio'][$i],
            'unidadTiempoAcuerdoServicio' => $request['unidadTiempoAcuerdoServicio'][$i] );

            $guardar = \App\AcuerdoServicio::updateOrCreate($indice, $data);

        }


        // -----------------------------------
        // ASESORES
        // en el formulario hay un campo oculto en el que almacenamos los id que se eliminan separados por coma
        // en este proceso lo convertimos en array y eliminamos dichos id de la tabla de detalle
        $idAsesorEliminar = explode(',', $request['eliminarAsesor']);
        \App\GrupoEstadoAsesor::whereIn('idGrupoEstadoAsesor',$idAsesorEliminar)->delete();

        $contadorAsesor = count($request['nombre1Tercero']);
        echo $contadorAsesor;
        for($i = 0; $i < $contadorAsesor; $i++)
        {
            echo "entra";
            $indice = array(
             'idGrupoEstadoAsesor' => $request['idGrupoEstadoAsesor'][$i]);

            $data = array(
            'GrupoEstado_idGrupoEstado' => $id,
            'Tercero_idAsesor' => $request['Tercero_idAsesor'][$i]);

            $guardar = \App\GrupoEstadoAsesor::updateOrCreate($indice, $data);

        }



    }
}
