<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\DocumentoRequest;
use App\Http\Controllers\Controller;
use DB;
use Config;
include public_path().'/ajax/consultarPermisos.php';

class DocumentoController extends Controller
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
            return view('documentogrid', compact('datos'));
        else
            return view('accesodenegado');
    }

    public function indexDocumentoGrid()
    {
        return view ('documentogridselect');
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
        $idCompania = \App\Compania::All()->lists('idCompania');
        $nombreCompania = \App\Compania::All()->lists('nombreCompania');
        $sistemainformacion = \App\SistemaInformacion::All()->lists('nombreSistemaInformacion','idSistemaInformacion');
        $idLista = \App\Lista::All()->lists('idLista');
        $nombreLista = \App\Lista::All()->lists('nombreLista');

        return view('documento',compact('idRol','nombreRol','idCompania','nombreCompania','sistemainformacion','idLista','nombreLista'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DocumentoRequest $request)
    {
        \App\Documento::create([
        'codigoDocumento' => $request['codigoDocumento'],
        'nombreDocumento' => $request['nombreDocumento'],
        'directorioDocumento' => $request['directorioDocumento'],
        'tipoDocumento' => $request['tipoDocumento'],
        'origenDocumento' => $request['origenDocumento'],
        'SistemaInformacion_idSistemaInformacion' => ($request['SistemaInformacion_idSistemaInformacion'] == '' ? null : $request['SistemaInformacion_idSistemaInformacion']) ,
        'tipoConsultaDocumento' => $request['tipoConsultaDocumento'],
        'tablaDocumento' => $request['tablaDocumento'],
        'consultaDocumento' => $request['consultaDocumento'],
        'filtroDocumento' => $request['filtroDocumento'],
        'controlVersionDocumento' => $request['controlVersionDocumento'],
        'trazabilidadMetadatosDocumento' => $request['trazabilidadMetadatosDocumento'],
        'concatenarNombreDocumento' => $request['concatenarNombreDocumento']
        ]);

        $documento = \App\Documento::All()->last();
        $contadorDocumentoVersion = count($request['nivelDocumentoVersion']);
        for($i = 0; $i < $contadorDocumentoVersion; $i++)
        {
            \App\DocumentoVersion::create([
            'Documento_idDocumento' => $documento->idDocumento,
            'nivelDocumentoVersion' => $request['nivelDocumentoVersion'][$i],
            'tipoDocumentoVersion' => $request['tipoDocumentoVersion'][$i],
            'longitudDocumentoVersion' => $request['longitudDocumentoVersion'][$i],
            'inicioDocumentoVersion' => $request['inicioDocumentoVersion'][$i],
            'rellenoDocumentoVersion' => $request['rellenoDocumentoVersion'][$i]
            ]);
        }

        $contadorDocumentoPropiedad = count($request['ordenDocumentoPropiedad']);
        for($i = 0; $i < $contadorDocumentoPropiedad; $i++)
        {
            \App\DocumentoPropiedad::create([
            'Documento_idDocumento' => $documento->idDocumento,
            'ordenDocumentoPropiedad' => $request['ordenDocumentoPropiedad'][$i],
            'Metadato_idMetadato' => $request['Metadato_idMetadato'][$i],
            'campoDocumentoPropiedad' => ($request['campoDocumentoPropiedad'][$i]) == '' ? "null" : $request['campoDocumentoPropiedad'][$i],
            'gridDocumentoPropiedad' => $request['gridDocumentoPropiedad'][$i],
            'indiceDocumentoPropiedad' => $request['indiceDocumentoPropiedad'][$i],
            'versionDocumentoPropiedad' => $request['versionDocumentoPropiedad'][$i],
            'validacionDocumentoPropiedad' => $request['validacionDocumentoPropiedad'][$i]
            ]); 
        }

        $contadorDocumentoPermiso = count($request['cargarDocumentoPermiso']);
        for($i = 0; $i < $contadorDocumentoPermiso; $i++)
        {
            \App\DocumentoPermiso::create([
            'Documento_idDocumento' => $documento->idDocumento,
            'Rol_idRol' => $request['Rol_idRol'][$i],
            'cargarDocumentoPermiso' => $request['cargarDocumentoPermiso'][$i],
            'descargarDocumentoPermiso' => $request['descargarDocumentoPermiso'][$i],
            'eliminarDocumentoPermiso' => $request['eliminarDocumentoPermiso'][$i],
            'modificarDocumentoPermiso' => $request['modificarDocumentoPermiso'][$i],
            'consultarDocumentoPermiso' => $request['consultarDocumentoPermiso'][$i],
            'correoDocumentoPermiso' => $request['correoDocumentoPermiso'][$i],
            'imprimirDocumentoPermiso' => $request['imprimirDocumentoPermiso'][$i]
            ]);
        }

        $contadorDocumentoPermisoCompania = count($request['Compania_idCompania']);
        for($i = 0; $i < $contadorDocumentoPermisoCompania; $i++)
        {
            \App\DocumentoPermisoCompania::create([
            'Documento_idDocumento' => $documento->idDocumento,
            'Compania_idCompania' => $request['Compania_idCompania'][$i],
            ]);
        }
        return redirect('/documento');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $titulo = DB::table('documentopropiedad')
        ->leftJoin('documento', 'documentopropiedad.Documento_idDocumento', "=", 'documento.idDocumento')
        ->leftjoin('metadato','documentopropiedad.Metadato_idMetadato', "=", 'metadato.idMetadato')
        ->select(DB::raw('documento.*, metadato.*'))
        ->where('Documento_idDocumento', "=", $id)
        ->get();


        $metadatos = DB::select('SELECT numeroRadicadoVersion, idRadicadoVersion, radicadodocumentopropiedad.*, documento.nombreDocumento, metadato.tipoMetadato 
                    from(
                        Select  
                            Radicado_idRadicado, 
                            numeroRadicadoVersion, 
                            idRadicadoVersion  
                        from (
                                Select 
                                Radicado_idRadicado, 
                                numeroRadicadoVersion, 
                                idRadicadoVersion  
                                from radicadoversion
                                group by Radicado_idRadicado, numeroRadicadoVersion desc
                            ) as ver
                            
                            group by Radicado_idRadicado
                        ) 
                        as datos

                    left join radicadodocumentopropiedad
                    on datos.Radicado_idRadicado = radicadodocumentopropiedad.Radicado_idRadicado 
                    and radicadodocumentopropiedad.RadicadoVersion_idRadicadoVersion = datos.idRadicadoVersion

                    left join documentopropiedad 
                    on radicadodocumentopropiedad.DocumentoPropiedad_idDocumentoPropiedad = documentopropiedad.idDocumentoPropiedad

                    left join metadato 
                    on documentopropiedad.Metadato_idMetadato = metadato.idMetadato

                    left join documento
                    on documentopropiedad.Documento_idDocumento = documento.idDocumento

                    where Documento_idDocumento = '.$id.'

                    order by Radicado_idRadicado');

        return view('formatos.impresionTrazabilidad', compact('titulo', 'metadatos'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $documento = \App\Documento::find($id);
        $idRol = \App\Rol::All()->lists('idRol');
        $nombreRol = \App\Rol::All()->lists('nombreRol');
        $idCompania = \App\Compania::All()->lists('idCompania');
        $nombreCompania = \App\Compania::All()->lists('nombreCompania');
        $sistemainformacion = \App\SistemaInformacion::All()->lists('nombreSistemaInformacion','idSistemaInformacion');
        $idLista = \App\Lista::All()->lists('idLista');
        $nombreLista = \App\Lista::All()->lists('nombreLista');
        return view('documento',compact('idRol','nombreRol','idCompania','nombreCompania','sistemainformacion','idLista','nombreLista'), ['documento' => $documento]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DocumentoRequest $request, $id)
    {
        $documento = \App\Documento::find($id);
        $documento->controlVersionDocumento = (isset($request['controlVersionDocumento']) ? 1 : 0);
        $documento->trazabilidadMetadatosDocumento = (isset($request['trazabilidadMetadatosDocumento']) ? 1 : 0);
        $documento->concatenarNombreDocumento = (isset($request['concatenarNombreDocumento']) ? 1 : 0);
        $documento->fill($request->all());
        $documento->SistemaInformacion_idSistemaInformacion = ($request['SistemaInformacion_idSistemaInformacion'] == '' ? null : $request['SistemaInformacion_idSistemaInformacion']);
        $documento->save();

        // SE GUARDA LOS DATOS DE LA VERSION DEL DOCUMENTO

        // en el formulario hay un campo oculto en el que almacenamos los id que se eliminan separados por coma
        // en este proceso lo convertimos en array y eliminamos dichos id de la tabla de detalle
        $idsEliminar = explode(',', $request['eliminarVersion']);
        \App\DocumentoVersion::whereIn('idDocumentoVersion',$idsEliminar)->delete();
        $contadorDocumentoVersion = count($request['nivelDocumentoVersion']);
        for($i = 0; $i < $contadorDocumentoVersion; $i++)
        {
            $index = array(
                'idDocumentoVersion' => $request['idDocumentoVersion'][$i]);

            $data= array(
                'Documento_idDocumento' => $documento->idDocumento,
                'nivelDocumentoVersion' => $request['nivelDocumentoVersion'][$i],
                'tipoDocumentoVersion' => $request['tipoDocumentoVersion'][$i],
                'longitudDocumentoVersion' => $request['longitudDocumentoVersion'][$i],
                'inicioDocumentoVersion' =>$request['inicioDocumentoVersion'][$i],
                'rellenoDocumentoVersion' => $request['rellenoDocumentoVersion'][$i]);
            
            $save = \App\DocumentoVersion::updateOrCreate($index, $data);
        }

        // SE GUARDAN LAS PROPIEDADES DEL DOCUMENTO
        $idsEliminar = explode(',', $request['eliminarPropiedad']);
        \App\DocumentoPropiedad::whereIn('idDocumentoPropiedad',$idsEliminar)->delete();
        $contadorDocumentoPropiedad = count($request['ordenDocumentoPropiedad']);
        for($i = 0; $i < $contadorDocumentoPropiedad; $i++)
        {
            $indice = array(
                'idDocumentoPropiedad' => $request['idDocumentoPropiedad'][$i]);

            $datos= array(
                'Documento_idDocumento' => $documento->idDocumento,
                'ordenDocumentoPropiedad' => $request['ordenDocumentoPropiedad'][$i],
                'Metadato_idMetadato' => $request['Metadato_idMetadato'][$i],
                'campoDocumentoPropiedad' => ($request['campoDocumentoPropiedad'][$i]) == '' ? "null" : $request['campoDocumentoPropiedad'][$i],
                'gridDocumentoPropiedad' =>$request['gridDocumentoPropiedad'][$i],
                'indiceDocumentoPropiedad' =>$request['indiceDocumentoPropiedad'][$i],
                'versionDocumentoPropiedad' =>$request['versionDocumentoPropiedad'][$i],
                'validacionDocumentoPropiedad' =>$request['validacionDocumentoPropiedad'][$i]);
            
            $guardar = \App\DocumentoPropiedad::updateOrCreate($indice, $datos);
        }

        // SE GUARDAN LOS PERMISOS DEL DOCUMENTO
        $idsEliminar = explode(',', $request['eliminarPermiso']);
        \App\DocumentoPermiso::whereIn('idDocumentoPermiso',$idsEliminar)->delete();
        $contadorDocumentoPermiso = count($request['cargarDocumentoPermiso']);
        for($i = 0; $i < $contadorDocumentoPermiso; $i++)
        {
            $indice = array(
                'idDocumentoPermiso' => $request['idDocumentoPermiso'][$i]);

            $datos= array(
                'Documento_idDocumento' => $documento->idDocumento,
                'Rol_idRol' => $request['Rol_idRol'][$i],
                'cargarDocumentoPermiso' => $request['cargarDocumentoPermiso'][$i],
                'descargarDocumentoPermiso' =>$request['descargarDocumentoPermiso'][$i],
                'consultarDocumentoPermiso' => $request['consultarDocumentoPermiso'][$i],
                'modificarDocumentoPermiso' =>$request['modificarDocumentoPermiso'][$i],
                'imprimirDocumentoPermiso' =>$request['imprimirDocumentoPermiso'][$i],
                'correoDocumentoPermiso' =>$request['correoDocumentoPermiso'][$i],
                'eliminarDocumentoPermiso' =>$request['eliminarDocumentoPermiso'][$i]);

            $guardar = \App\DocumentoPermiso::updateOrCreate($indice, $datos);
        }

        // SE GUARDAN LOS PERMISOS DEL DOCUMENTO POR COMPAÑIA
        $idsEliminar = explode(',', $request['eliminarDocumentoPermisoCompania']);
        \App\DocumentoPermisoCompania::whereIn('idDocumentoPermisoCompania',$idsEliminar)->delete();
        $contadorDocumentoPermisoCompania = count($request['Compania_idCompania']);
        for($i = 0; $i < $contadorDocumentoPermisoCompania; $i++)
        {
            $index = array(
                'idDocumentoPermisoCompania' => $request['idDocumentoPermisoCompania'][$i]);

            $data= array(
                'Documento_idDocumento' => $documento->idDocumento,
                'Compania_idCompania' => $request['Compania_idCompania'][$i]);

            $save = \App\DocumentoPermisoCompania::updateOrCreate($index, $data);
        }
        return redirect('/documento');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\Documento::destroy($id);
        return redirect('/documento');
    }

}

