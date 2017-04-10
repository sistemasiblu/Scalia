<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
include public_path().'/ajax/consultarPermisos.php';

class DocumentoImportacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vista = basename($_SERVER["PHP_SELF"]);
        $datos = consultarPermisos($vista);

        if($datos != null)
            return view('documentoimportaciongrid', compact('datos'));
        else
            return view('accesodenegado');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $idRol = \App\Rol::All()->lists('idRol');
        $nombreRol = \App\Rol::All()->lists('nombreRol');
        $idDocumento = \App\Documento::All()->lists('idDocumento');
        $nombreDocumento = \App\Documento::All()->lists('nombreDocumento');
        $sistemainformacion = \App\SistemaInformacion::where('webSistemaInformacion', "=", 1)->lists('nombreSistemaInformacion','idSistemaInformacion');

        return view('documentoimportacion',compact('idRol','nombreRol','sistemainformacion','idDocumento','nombreDocumento'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        \App\DocumentoImportacion::create([
        'codigoDocumentoImportacion' => $request['codigoDocumentoImportacion'],
        'nombreDocumentoImportacion' => $request['nombreDocumentoImportacion'],
        'origenDocumentoImportacion' => $request['origenDocumentoImportacion'],
        'SistemaInformacion_idSistemaInformacion' => ($request['SistemaInformacion_idSistemaInformacion'] == '' ? null : $request['SistemaInformacion_idSistemaInformacion']) ,
        'tipoDocumentoImportacion' => $request['tipoDocumentoImportacion'],
        'Compania_idCompania' => \Session::get("idCompania")
        ]);

        $documentoimportacion = \App\DocumentoImportacion::All()->last();

        $this->grabarDetalle($documentoimportacion->idDocumentoImportacion, $request);

        return redirect('/documentoimportacion');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $documentoimportacion = \App\DocumentoImportacion::find($id);
        $idRol = \App\Rol::All()->lists('idRol');
        $nombreRol = \App\Rol::All()->lists('nombreRol');
        $idDocumento = \App\Documento::All()->lists('idDocumento');
        $nombreDocumento = \App\Documento::All()->lists('nombreDocumento');
        $sistemainformacion = \App\SistemaInformacion::All()->lists('nombreSistemaInformacion','idSistemaInformacion');

        $documentocorreo = DB::table('documentoimportacioncorreo')
        ->leftJoin('documento', 'documentoimportacioncorreo.Documento_idDocumento', '=', 'documento.idDocumento')
        ->select(DB::raw('idDocumentoImportacionCorreo, tipoDocumentoImportacionCorreo, nombreDocumento, DocumentoImportacion_idDocumentoImportacion, Documento_idDocumento'))
        ->orderBy('tipoDocumentoImportacionCorreo','ASC')
        ->where('DocumentoImportacion_idDocumentoImportacion','=',$id)
        ->get();

        return view('documentoimportacion',compact('idRol','nombreRol','sistemainformacion','idDocumento','nombreDocumento','documentocorreo'), ['documentoimportacion' => $documentoimportacion]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $documentoimportacion = \App\DocumentoImportacion::find($id);
        $documentoimportacion->fill($request->all());
        $documentoimportacion->SistemaInformacion_idSistemaInformacion = ($request['SistemaInformacion_idSistemaInformacion'] == '' ? null : $request['SistemaInformacion_idSistemaInformacion']);
        $documentoimportacion->save();

        $this->grabarDetalle($id, $request);

        return redirect('/documentoimportacion');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\DocumentoImportacion::destroy($id);
        return redirect('/documentoimportacion');
    }

    public function grabarDetalle($id, $request)
    {
        $idsEliminarCorreo = explode(',', $request['eliminarImportacionCorreo']);
        \App\DocumentoImportacionCorreo::whereIn('idDocumentoImportacionCorreo',$idsEliminarCorreo)->delete();
        for($i = 0; $i < count($request['tipoDocumentoImportacionCorreo']); $i++)
        {
            $indice = array(
                'idDocumentoImportacionCorreo' => $request['idDocumentoImportacionCorreo'][$i]);

            $datos= array(
                'DocumentoImportacion_idDocumentoImportacion' => $id,
                'tipoDocumentoImportacionCorreo' => $request['tipoDocumentoImportacionCorreo'][$i],
                'Documento_idDocumento' => $request['Documento_idDocumento'][$i]
                );

            $guardar = \App\DocumentoImportacionCorreo::updateOrCreate($indice, $datos);
        }


        $idsEliminar = explode(',', $request['eliminarImportacionPermiso']);
        \App\DocumentoImportacionPermiso::whereIn('idDocumentoImportacionPermiso',$idsEliminar)->delete();
        for($i = 0; $i < count($request['agregarDocumentoImportacionPermiso']); $i++)
        {
            $indice = array(
                'idDocumentoImportacionPermiso' => $request['idDocumentoImportacionPermiso'][$i]);

            $datos= array(
                'DocumentoImportacion_idDocumentoImportacion' => $id,
                'Rol_idRol' => $request['Rol_idRol'][$i],
                'agregarDocumentoImportacionPermiso' => $request['agregarDocumentoImportacionPermiso'][$i],
                'descargarDocumentoImportacionPermiso' => $request['descargarDocumentoImportacionPermiso'][$i],
                'consultarDocumentoImportacionPermiso' => $request['consultarDocumentoImportacionPermiso'][$i],
                'modificarDocumentoImportacionPermiso' => $request['modificarDocumentoImportacionPermiso'][$i],
                'imprimirDocumentoImportacionPermiso' => $request['imprimirDocumentoImportacionPermiso'][$i],
                'correoDocumentoImportacionPermiso' => $request['correoDocumentoImportacionPermiso'][$i],
                'eliminarDocumentoImportacionPermiso' => $request['eliminarDocumentoImportacionPermiso'][$i]
                );

            $guardar = \App\DocumentoImportacionPermiso::updateOrCreate($indice, $datos);
        }

    }
}
