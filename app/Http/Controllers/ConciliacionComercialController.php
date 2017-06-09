<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\ConciliacionComercialRequest;
use App\Http\Controllers\Controller;
use DB;
include public_path().'/ajax/consultarPermisos.php';

class ConciliacionComercialController extends Controller
{

    
        /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    // public function indexValorConciliacionGrid()
    // {
    //     return view('valorconciliaciongridselect');
    // }

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
            return view('conciliacioncomercialgrid', compact('datos'));
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
        //  Enviamos la lista de documentos comerciales parametrizados en scalia
        $documento = DB::table('documentoconciliacion')
            ->leftjoin(\Session::get("baseDatosCompania").'.Documento','documentoconciliacion.Documento_idDocumento','=','idDocumento')
            ->where('Compania_idCompania','=',\Session::get("idCompania"))
            ->lists('nombreDocumento','idDocumento');

        return view('conciliacioncomercial', compact('documento'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(ConciliacionComercialRequest $request)
    {
        \App\ConciliacionComercial::create([
            'fechaElaboracionConciliacionComercial' => $request['fechaElaboracionConciliacionComercial'],
            'Users_idCrea' => $request['Users_idCrea'],
            'fechaInicialConciliacionComercial' => $request['fechaInicialConciliacionComercial'],
            'fechaFinalConciliacionComercial' => $request['fechaFinalConciliacionComercial'],
            'Documento_idDocumento' => $request['Documento_idDocumento'],
            'Compania_idCompania' => \Session::get("idCompania")
            ]);

        $conciliacioncomercial = \App\ConciliacionComercial::All()->last();

        // $this->grabarDetalle($conciliaiconcomercial->idConciliacionComercial,$request);

        return redirect('/conciliacioncomercial');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $conciliacioncomercial = \App\ConciliacionComercial::find($id);

        //  Enviamos la lista de documentos comerciales
        $documento = DB::table('documentoconciliacion')
            ->leftjoin(\Session::get("baseDatosCompania").'.Documento','documentoconciliacion.Documento_idDocumento','=','idDocumento')
            ->where('Compania_idCompania','=',\Session::get("idCompania"))
            ->lists('nombreDocumento','idDocumento');

        //  Enviamos la lista de documentos comerciales
        $users = DB::select(
            "SELECT name, id
            FROM conciliacioncomercial 
            LEFT JOIN users
            ON Users_idCrea = id
            WHERE idConciliacionComercial = $id");

        $users = $this->convertirArray($users);

        return view('conciliacioncomercial',['conciliacioncomercial'=>$conciliacioncomercial], compact('documento','users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update($id,ConciliacionComercialRequest $request)
    {        
        $conciliacioncomercial = \App\ConciliacionComercial::find($id);
        $conciliacioncomercial->fill($request->all());
        $conciliacioncomercial->save();

        // $this->grabarDetalle($id,$request);

        return redirect('/conciliacioncomercial');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    

    public function destroy($id)
    {
        \App\ConciliacionComercial::destroy($id);
        return redirect('/conciliacioncomercial');
    }

    protected function grabarDetalle($id, $request)
    {
        // en el formulario hay un campo oculto en el que almacenamos los id que se eliminan separados por coma
        // en esta conciliaciondocumento lo convertimos en array y eliminamos dichos id de la tabla de detalle
        // $idsEliminar = explode(',', $request['eliminarDocumentoConciliacionComercial']);
        // \App\DocumentoConciliacionComercial::whereIn('idDocumentoConciliacionComercial',$idsEliminar)->delete();

        // $contadorDetalle = count($request['ValorConciliacion_idValorConciliacionCom']);
            
        // for($i = 0; $i < $contadorDetalle; $i++)
        // {
            
        //     $indice = array('idDocumentoConciliacionComercial' => $request['idDocumentoConciliacionComercial'][$i]);

        //     $data = array(
        //     'DocumentoConciliacion_idDocumentoConciliacion' => $id,
        //     'ValorConciliacion_idValorConciliacion' => $request['ValorConciliacion_idValorConciliacionCom'][$i],
        //     'cuentasLocalDocumentoConciliacionComercial' => $request['cuentasLocalDocumentoConciliacionComercial'][$i],
        //     'cuentasNiifDocumentoConciliacionComercial' => $request['cuentasNiifDocumentoConciliacionComercial'][$i] );


        //     $insertar = \App\DocumentoConciliacionComercial::updateOrCreate($indice, $data);

        // }



        // // en el formulario hay un campo oculto en el que almacenamos los id que se eliminan separados por coma
        // // en este documentoconciliacion lo convertimos en array y eliminamos dichos id de la tabla de detalle
        // $idsEliminar = explode(',', $request['eliminarDocumentoConciliacionCartera']);
        // \App\DocumentoConciliacionCartera::whereIn('idDocumentoConciliacionCartera',$idsEliminar)->delete();

        // $contadorDetalle = count($request['idDocumentoConciliacionCartera']);
        // for($i = 0; $i < $contadorDetalle; $i++)
        // {
        //     $indice = array(
        //      'idDocumentoConciliacionCartera' => $request['idDocumentoConciliacionCartera'][$i]);

        //     $data = array(
        //     'DocumentoConciliacion_idDocumentoConciliacion' => $id,
        //     'ValorConciliacion_idValorConciliacion' => $request['ValorConciliacion_idValorConciliacionCar'][$i],
        //     'cuentasLocalDocumentoConciliacionCartera' => $request['cuentasLocalDocumentoConciliacionCartera'][$i],
        //     'cuentasNiifDocumentoConciliacionCartera' => $request['cuentasNiifDocumentoConciliacionCartera'][$i] );


        //     $insertar = \App\DocumentoConciliacionCartera::updateOrCreate($indice, $data);

        // }
    }

    function convertirArray($dato)
    {
        $nuevo = array();

        for($i = 0; $i < count($dato); $i++) 
        {
          $nuevo[] = get_object_vars($dato[$i]) ;
        }
        return $nuevo;
    }
}
