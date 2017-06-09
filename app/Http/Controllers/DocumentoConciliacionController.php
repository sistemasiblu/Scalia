<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\DocumentoConciliacionRequest;
use App\Http\Controllers\Controller;
use DB;
include public_path().'/ajax/consultarPermisos.php';

class DocumentoConciliacionController extends Controller
{

    
        /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function indexValorConciliacionGrid()
    {
        return view('valorconciliaciongridselect');
    }

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
            return view('documentoconciliaciongrid', compact('datos'));
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

        //  Enviamos la lista de documentos comerciales
        $documento = DB::table(\Session::get("baseDatosCompania").'.Documento')
            ->where('afectaContabilidadDocumento','=','SI')
            ->whereOr('afectaContabilidadNIIFDocumento','=','SI')
            ->lists('nombreDocumento','idDocumento');

        return view('documentoconciliacion', compact('documento'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(DocumentoConciliacionRequest $request)
    {
        \App\DocumentoConciliacion::create([
            'Documento_idDocumento' => $request['Documento_idDocumento'],
            'Compania_idCompania' => \Session::get("idCompania")
            ]);

        $documentoconciliacion = \App\DocumentoConciliacion::All()->last();

        $this->grabarDetalle($documentoconciliacion->idDocumentoConciliacion,$request);

        return redirect('/documentoconciliacion');
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
        $documentoconciliacion = \App\DocumentoConciliacion::find($id);

        //  Enviamos la lista de documentos comerciales
        $documento = DB::table(\Session::get("baseDatosCompania").'.Documento')
            ->where('afectaContabilidadDocumento','=','SI')
            ->whereOr('afectaContabilidadNIIFDocumento','=','SI')
            ->lists('nombreDocumento','idDocumento');

        // Consultamos la tabla de detalle comercial
        // $comercial = DB::table('documentoconciliacioncomercial')
        //     ->leftjoin('valorconciliacion','ValorConciliacion_idValorConciliacion','=','idValorConciliacion')
        //     ->where('DocumentoConciliacion_idDocumentoConciliacion','=',$id)
        //     ->select(DB::raw('idDocumentoConciliacionComercial', 'ValorConciliacion_idValorConciliacion', 'nombreValorConciliacion', 'cuentasLocalDocumentoConciliacionComercial', 'cuentasNiifDocumentoConciliacionComercial'));

        $comercial = DB::select(
            'SELECT idDocumentoConciliacionComercial, ValorConciliacion_idValorConciliacion AS ValorConciliacion_idValorConciliacionCom, 
                    nombreValorConciliacion AS nombreValorConciliacionCom,cuentasLocalDocumentoConciliacionComercial, cuentasNiifDocumentoConciliacionComercial
            FROM documentoconciliacioncomercial
            LEFT JOIN valorconciliacion
            ON ValorConciliacion_idValorConciliacion = idValorConciliacion
            WHERE DocumentoConciliacion_idDocumentoConciliacion = '.$id);

        $cartera = DB::select(
            'SELECT idDocumentoConciliacionCartera, ValorConciliacion_idValorConciliacion AS ValorConciliacion_idValorConciliacionCar, 
                    nombreValorConciliacion AS nombreValorConciliacionCar,cuentasLocalDocumentoConciliacionCartera, cuentasNiifDocumentoConciliacionCartera
            FROM documentoconciliacioncartera
            LEFT JOIN valorconciliacion
            ON ValorConciliacion_idValorConciliacion = idValorConciliacion
            WHERE DocumentoConciliacion_idDocumentoConciliacion = '.$id);

       $comercial = $this->convertirArray($comercial);

        return view('documentoconciliacion',['documentoconciliacion'=>$documentoconciliacion], compact('documento', 'comercial','cartera'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update($id,DocumentoConciliacionRequest $request)
    {
        
        $documentoconciliacion = \App\DocumentoConciliacion::find($id);
        $documentoconciliacion->fill($request->all());
        $documentoconciliacion->save();

        $this->grabarDetalle($id,$request);

        return redirect('/documentoconciliacion');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    

    public function destroy($id)
    {
        \App\DocumentoConciliacion::destroy($id);
        return redirect('/documentoconciliacion');
    }

    protected function grabarDetalle($id, $request)
    {

        // en el formulario hay un campo oculto en el que almacenamos los id que se eliminan separados por coma
        // en este documentoconciliacion lo convertimos en array y eliminamos dichos id de la tabla de detalle
        $idsEliminar = explode(',', $request['eliminarDocumentoConciliacionComercial']);
        \App\DocumentoConciliacionComercial::whereIn('idDocumentoConciliacionComercial',$idsEliminar)->delete();

        $contadorDetalle = count($request['ValorConciliacion_idValorConciliacionCom']);
            
        for($i = 0; $i < $contadorDetalle; $i++)
        {
            
            $indice = array('idDocumentoConciliacionComercial' => $request['idDocumentoConciliacionComercial'][$i]);

            $data = array(
            'DocumentoConciliacion_idDocumentoConciliacion' => $id,
            'ValorConciliacion_idValorConciliacion' => $request['ValorConciliacion_idValorConciliacionCom'][$i],
            'cuentasLocalDocumentoConciliacionComercial' => $request['cuentasLocalDocumentoConciliacionComercial'][$i],
            'cuentasNiifDocumentoConciliacionComercial' => $request['cuentasNiifDocumentoConciliacionComercial'][$i] );


            $insertar = \App\DocumentoConciliacionComercial::updateOrCreate($indice, $data);

        }



        // en el formulario hay un campo oculto en el que almacenamos los id que se eliminan separados por coma
        // en este documentoconciliacion lo convertimos en array y eliminamos dichos id de la tabla de detalle
        $idsEliminar = explode(',', $request['eliminarDocumentoConciliacionCartera']);
        \App\DocumentoConciliacionCartera::whereIn('idDocumentoConciliacionCartera',$idsEliminar)->delete();

        $contadorDetalle = count($request['idDocumentoConciliacionCartera']);
        for($i = 0; $i < $contadorDetalle; $i++)
        {
            $indice = array(
             'idDocumentoConciliacionCartera' => $request['idDocumentoConciliacionCartera'][$i]);

            $data = array(
            'DocumentoConciliacion_idDocumentoConciliacion' => $id,
            'ValorConciliacion_idValorConciliacion' => $request['ValorConciliacion_idValorConciliacionCar'][$i],
            'cuentasLocalDocumentoConciliacionCartera' => $request['cuentasLocalDocumentoConciliacionCartera'][$i],
            'cuentasNiifDocumentoConciliacionCartera' => $request['cuentasNiifDocumentoConciliacionCartera'][$i] );


            $insertar = \App\DocumentoConciliacionCartera::updateOrCreate($indice, $data);

        }
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
