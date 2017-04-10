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
            'codigoDocumentoConciliacion' => $request['codigoDocumentoConciliacion'],
            'nombreDocumentoConciliacion' => $request['nombreDocumentoConciliacion'],
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
        return view('documentoconciliacion',['documentoconciliacion'=>$documentoconciliacion]);
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
        $idsEliminar = explode(',', $request['eliminarOperacion']);
        \App\DocumentoConciliacionComercial::whereIn('idDocumentoConciliacionComercial',$idsEliminar)->delete();

        $contadorDetalle = count($request['ordenDocumentoConciliacionComercial']);
        for($i = 0; $i < $contadorDetalle; $i++)
        {
            $indice = array(
             'idDocumentoConciliacionComercial' => $request['idDocumentoConciliacionComercial'][$i]);

            $data = array(
            'DocumentoConciliacion_idDocumentoConciliacion' => $id,
            'ordenDocumentoConciliacionComercial' => $request['ordenDocumentoConciliacionComercial'][$i],
            'nombreDocumentoConciliacionComercial' => $request['nombreDocumentoConciliacionComercial'][$i],
            'samDocumentoConciliacionComercial' => $request['samDocumentoConciliacionComercial'][$i],
            'observacionDocumentoConciliacionComercial' => $request['observacionDocumentoConciliacionComercial'][$i] );


            $insertar = \App\DocumentoConciliacionComercial::updateOrCreate($indice, $data);

        }
    }
}
