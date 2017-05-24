<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\RadicadoControllerRequest;
use App\Http\Controllers\Controller;
use Mail;
use Session;
use DB;
use Input;
use File;

use Validator;
use Response;

// include composer autoload
require '../vendor/autoload.php';
//use Intervention\Image\ImageManager ;

class RadicadoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('radicadocreate');
    }        
    //Llamo a la grid en el modal
    public function indexEtiquetaGrid()
    {
        return view('etiquetagridselect'); //Contiene las etiquetas
    }

    //Lamo al dropzone
    public function indexdropzone() 
    {
        return view('dropzone');
    }

    //Funcion para subir archivos con dropzone
    public function uploadFiles(Request $request) 
    {
 
        $input = Input::all();
 
        $rules = array(
        );
 
        $validation = Validator::make($input, $rules);
 
        if ($validation->fails()) {
            return Response::make($validation->errors->first(), 400);
        }
        
        $destinationPath = public_path() . '/repositorio/temporal'; //Guardo en la carpeta  temporal

        $extension = Input::file('file')->getClientOriginalExtension(); 
        $fileName = Input::file('file')->getClientOriginalName(); // nombre de archivo
        $upload_success = Input::file('file')->move($destinationPath, $fileName);
 
        if ($upload_success) {
            return Response::json('success', 200);
        } 
        else {
            return Response::json('error', 400);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $dependencia = \App\Dependencia::All()->lists('nombreDependencia','idDependencia');
        $serie = \App\Serie::All()->lists('nombreSerie','idSerie');
        $subserie = \App\SubSerie::All()->lists('nombreSubSerie','idSubSerie');
        $documento = \App\Documento::All()->lists('nombreDocumento','idDocumento');     
        return view('radicado',compact('dependencia','serie','subserie','documento'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        // *******************************************
        // 
        // GUARDO EL RADICADO TIPO GESTION DOCUMENTAL
        // 
        // ******************************************

            //Con una consulta a la tabla radicado selecciono la abreviatura de la dependencia, codigo serie y el codigo 
            //sub serie y los concateno con nueve 0 y el decimo digito es un número que va sumando de a 1 cada que 
            //radique un nuevo documento con este codigo
            $codigoRadicado = DB::table('radicado')
            ->leftJoin ('dependencia','radicado.Dependencia_idDependencia', "=", 'dependencia.idDependencia')
            ->leftJoin ('serie','radicado.Serie_idSerie', "=", 'serie.idSerie')
            ->leftJoin ('subserie','radicado.SubSerie_idSubSerie', "=", 'subserie.idSubSerie')     
            ->select (DB::raw("CONCAT(abreviaturaDependencia, codigoSerie, codigoSubSerie,LPAD((MAX(REPLACE ( codigoRadicado , CONCAT(abreviaturaDependencia, codigoSerie, codigoSubSerie), '' ))+1),10,'0')) as codigoRadicado"))
            ->where ('radicado.Dependencia_idDependencia', "=", $request['Dependencia_idDependencia'])
            ->where ('radicado.Serie_idSerie', "=", $request['Serie_idSerie'])
            ->where ('radicado.SubSerie_idSubSerie', "=", $request['SubSerie_idSubSerie'])
            ->get();

            $codigo = get_object_vars($codigoRadicado[0]);

            //Pregunto si el codigo existe y l dejo tal cual
            if(isset($codigo['codigoRadicado']))
            {
                
                $codigoR = $codigo['codigoRadicado'];
            }
            // Si no existe lo creo y le pongo un 1 al final indicando que este es el primer codigo de este consecutivo
            else
            {
                $dependencia = \App\Dependencia::where('idDependencia', "=", $request['Dependencia_idDependencia'])->lists('abreviaturaDependencia');
                $serie = \App\Serie::where('idSerie', "=", $request['Serie_idSerie'])->lists('codigoSerie');
                $subserie = \App\SubSerie::where('idSubSerie', "=", $request['SubSerie_idSubSerie'])->lists('codigoSubSerie');
                
                $codigoR = $dependencia[0].$serie[0].$subserie[0].'0000000001';
            }

            //Guardo en radicado sus respectivos campos
            \App\Radicado::create([
                'codigoRadicado' => $codigoR,
                'Dependencia_idDependencia' => ($request['Dependencia_idDependencia'] == '' ? null : $request['Dependencia_idDependencia']),
                'Serie_idSerie' => ($request['Serie_idSerie'] == '' ? null : $request['Serie_idSerie']),
                'SubSerie_idSubSerie' => ($request['SubSerie_idSubSerie'] == '' ? null : $request['SubSerie_idSubSerie']),
                'Documento_idDocumento' => $request['Documento_idDocumento'],
                'ubicacionEstanteRadicado' => $request['ubicacionEstanteRadicado'],
                'Compania_idCompania' => \Session::get("idCompania")
                ]);

            $radicado = \App\Radicado::All()->last();

            $carpetaReal = DB::Select('
                SELECT 
                    directorioDependencia,
                    directorioSerie,
                    directorioSubSerie,
                    directorioDocumento,
                    abreviaturaDependencia,
                    codigoSerie,
                    codigoSubSerie,
                    nombreDocumento
                FROM
                    retenciondocumental r
                        LEFT JOIN
                    dependencia d ON r.Dependencia_idDependencia = d.idDependencia
                        LEFT JOIN
                    serie s ON r.Serie_idSerie = s.idSerie
                        LEFT JOIN
                    subserie ss ON r.SubSerie_idSubserie = ss.idSubSerie
                        LEFT JOIN
                    documento doc ON r.Documento_idDocumento = doc.idDocumento
                WHERE
                    r.Documento_idDocumento = '.$request['Documento_idDocumento'].'
                        AND r.Dependencia_idDependencia = '.$request['Dependencia_idDependencia'].'
                        AND r.Serie_idSerie = '.$request['Serie_idSerie'].'
                        AND r.SubSerie_idSubSerie = '.$request['SubSerie_idSubSerie']);

            //Convierto array en string
            $carpeta = get_object_vars($carpetaReal[0]);

            //Si el archivo radicado no esta vacio incialmente se guarda en una carpeta temporal para luego ser movido a la carpeta de destino
            //dependneido de la serie, sub serie y documento asociados
            $destinationPath = '';
            if ($request['archivoRadicado'] != '') 
            {
                $origen = public_path() . '/repositorio/temporal/'.$request['archivoRadicado'];
                
                $ext = substr($request['archivoRadicado'], -4);

                if ($ext == 'xlsx') 
                {
                    $request['archivoRadicado'] = $carpeta['abreviaturaDependencia'].'_S'.$carpeta['codigoSerie'].'_SS'.$carpeta['codigoSubSerie'].'_'.$carpeta['nombreDocumento'].'_R'.$radicado->idRadicado.'.'.$ext;                    
                }
                elseif ($ext == 'docx') 
                {
                    $request['archivoRadicado'] = $carpeta['abreviaturaDependencia'].'_S'.$carpeta['codigoSerie'].'_SS'.$carpeta['codigoSubSerie'].'_'.$carpeta['nombreDocumento'].'_R'.$radicado->idRadicado.'.'.$ext;                    
                }
                else
                {
                    $request['archivoRadicado'] = $carpeta['abreviaturaDependencia'].'_S'.$carpeta['codigoSerie'].'_SS'.$carpeta['codigoSubSerie'].'_'.$carpeta['nombreDocumento'].'_R'.$radicado->idRadicado.$ext;
                }

                $destinationPath = public_path() . '/repositorio/'.$carpeta['directorioDependencia'].'/'.$carpeta['directorioSerie']."/".$carpeta['directorioSubSerie']."/".$carpeta['directorioDocumento']."/".$request['archivoRadicado'];

                if (file_exists($origen))
                {
                    copy($origen, $destinationPath);
                    unlink($origen);
                }
                else
                {
                    echo "No existe el archivo";
                }
            }

            //Guardo en radicadoetiqueta sus respectivos campos y con explode lo separo por comas (",")
            //Para guardarlos por registros independientes cada que de una vuelta en el for
            $etiquetaRadicado = explode(",",$request['etiquetaRadicado']);
            $contadorEtiqueta = count($etiquetaRadicado); 
            //Hago un ciclo para que me guarde en registro independientes
            for($i = 0; $i < $contadorEtiqueta; $i++)
            {
                \App\RadicadoEtiqueta::create([
                'Radicado_idRadicado' => $radicado->idRadicado,
                'Etiqueta_idEtiqueta' => ($etiquetaRadicado[$i] != '') ? $etiquetaRadicado[$i] : null
                ]);
            }

            //Guardo en documentoversion sus respectivos campos         
            \App\RadicadoVersion::create([
            'Radicado_idRadicado' => $radicado->idRadicado,
            'fechaRadicadoVersion' => $request['fechaRadicadoVersion'],
            'numeroRadicadoVersion' => $request['numeroRadicadoVersion'],
            'tipoRadicadoVersion' => $request['tipoRadicadoVersion'],
            'archivoRadicadoVersion' => $destinationPath
            ]);
            
            //Consulto a radicado version
            $radicadoVersion = \App\RadicadoVersion::All()->last();

            //Guarda en documentopropiedad sus respectivos campos
            //Realizo una consulta a la base de datos trayendo el idDocumento
            $metadatos = DB::table('documentopropiedad')
            ->leftjoin('metadato','documentopropiedad.Metadato_idMetadato', "=", 'metadato.idMetadato')
            ->select(DB::raw('documentopropiedad.*,metadato.*'))
            ->where('Documento_idDocumento', "=", $request['Documento_idDocumento'])
            ->get();

            //Hago un ciclo para que me guarde en registro independientes
            for($i = 0; $i < count($metadatos); $i++)
            {
                //Convertir array a string
                $nombremetadato = get_object_vars($metadatos[$i]);
                // Pregunto si es tipo editor para guardarlo en un campo diferente 
                $campo = ($nombremetadato["tipoMetadato"] == 'Editor') ? 'editorRadicadoDocumentoPropiedad' : 'valorRadicadoDocumentoPropiedad';
                $valor = $request[$nombremetadato["idDocumentoPropiedad"]];
                $valorCheck= $request[$nombremetadato["idDocumentoPropiedad"]];

                //Si un campo de documento propiedad es elecion multiple se separara por comas para gudarlos en un mismo registro
                if ($nombremetadato["tipoMetadato"] == 'EleccionMultiple')
                {
                    $valor = '';
                    foreach ($valorCheck as $key => $value)
                    {
                        $valor .= $value.',';
                    }
                    $valor = substr($valor, 0, strlen($valor)-1);
                }

                \App\RadicadoDocumentoPropiedad::create([
                'Radicado_idRadicado' => $radicado->idRadicado,
                'DocumentoPropiedad_idDocumentoPropiedad' => $nombremetadato["idDocumentoPropiedad"],
                $campo => $valor,
                'RadicadoVersion_idRadicadoVersion' => $radicadoVersion->idRadicadoVersion
                ]);
            }

                if($request->ajax()) 
                {
                    return response()->json('RADICADO No '.$codigoR);
                }
                return redirect('/radicado');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function show(Request $request)
    {
        //Se realiza un condicional IF para preguntar si el id de radicado esta vacio (radicando) o esta lleno (consultando)
        //En caso de estar lleno se mostraran solo las series y sub series relacionadas con este documento

        if ($request['idRadicado'] == 0)
        {
            if(isset($request['dependenciaClasificacionDocumental']))
            {
                $clasificaciondocumental = DB::table('clasificaciondocumental')
                ->leftjoin('serie', 'clasificaciondocumental.Serie_idSerie', "=", 'serie.idSerie')
                ->leftjoin('subserie', 'subserie.Serie_idSerie', "=", 'serie.idSerie')
                ->leftjoin('subseriedetalle','subseriedetalle.SubSerie_idSubSerie', "=", 'subserie.idSubSerie')
                ->leftjoin('documento', 'subseriedetalle.Documento_idDocumento', "=", 'documento.idDocumento')
                ->select (DB::raw('idSerie, nombreSerie'))
                ->where ('dependenciaClasificacionDocumental', "like", '% '.$request['dependenciaClasificacionDocumental'].',%')
                ->groupBy('serie.idSerie')
                // ->where('Documento_idDocumento', "=", $request['Documento'])
                ->get();

                if($request->ajax())
                    {
                        return response()->json([
                            $clasificaciondocumental
                        ]);
                    }   
            } 

            if(isset($request['Serie_idSerie']))
                {
                   $Subserie = DB::table('subserie')
                   ->leftjoin('subseriedetalle','subseriedetalle.SubSerie_idSubSerie', "=", 'subserie.idSubSerie')
                   ->leftjoin('documento', 'subseriedetalle.Documento_idDocumento', "=", 'documento.idDocumento')
                   ->select(DB::raw('idSubSerie, nombreSubSerie'))
                   ->where ('Serie_idSerie', "=", $request['Serie_idSerie'])
                   ->groupBy('subserie.idSubSerie')
                   // ->where ('Documento_idDocumento', "=", $request['Documento'])
                   ->get();

                    if($request->ajax())
                    {
                        return response()->json([
                            $Subserie
                        ]);
                    }               
                }
        }
        else
        {
          if(isset($request['dependenciaClasificacionDocumental']))
            {
                $clasificaciondocumental = DB::table('clasificaciondocumental')
                ->leftjoin('serie', 'clasificaciondocumental.Serie_idSerie', "=", 'serie.idSerie')
                ->leftjoin('subserie', 'subserie.Serie_idSerie', "=", 'serie.idSerie')
                ->leftjoin('subseriedetalle','subseriedetalle.SubSerie_idSubSerie', "=", 'subserie.idSubSerie')
                ->leftjoin('documento', 'subseriedetalle.Documento_idDocumento', "=", 'documento.idDocumento')
                ->select (DB::raw('idSerie, nombreSerie'))
                ->where ('dependenciaClasificacionDocumental', "like", '% '.$request['dependenciaClasificacionDocumental'].',%')
                ->where('Documento_idDocumento', "=", $request['Documento'])
                ->groupBy('serie.idSerie')
                ->get();



                if($request->ajax())
                    {
                        return response()->json([
                            $clasificaciondocumental
                        ]);
                    }   
            } 

            if(isset($request['Serie_idSerie']))
                {
                   $Subserie = DB::table('subserie')
                   ->select(DB::raw('idSubSerie, nombreSubSerie'))
                   ->leftjoin('subseriedetalle','subseriedetalle.SubSerie_idSubSerie', "=", 'subserie.idSubSerie')
                   ->leftjoin('documento', 'subseriedetalle.Documento_idDocumento', "=", 'documento.idDocumento')
                   ->where ('Serie_idSerie', "=", $request['Serie_idSerie'])
                   ->where ('Documento_idDocumento', "=", $request['Documento'])
                   ->groupBy('subserie.idSubSerie')
                   ->get();

                    if($request->ajax())
                    {
                        return response()->json([
                            $Subserie
                        ]);
                    }               
                }  
        }
    }

    public function radicar(Request $request)
    {
        if ($request['accion'] == 'radicar') 
        {
            $codigoRadicado = $_GET['codigoRadicado'];
            $fecha = $_GET['fecha'];
            $ubicacion = $_GET['ubicacion'];
            $etiqueta = $_GET['etiqueta'];
            return view('formatos.impresionRadicado',compact('codigoRadicado', 'fecha', 'ubicacion','etiqueta'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       $dependencia = \App\Dependencia::All()->lists('nombreDependencia','idDependencia');
        $serie = \App\Serie::All()->lists('nombreSerie','idSerie');
        $subserie = \App\SubSerie::All()->lists('nombreSubSerie','idSubSerie');
        $documento = \App\Documento::All()->lists('nombreDocumento','idDocumento');
        return view('radicado',compact('dependencia','serie','subserie','documento',
            ['radicado' => $radicado]));

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
        // **********************************************************
        // 
        // SUBO UNA NUEVA VERSION DEL RADICADO TIPO GESTION DOCUMENTAL
        // 
        // ***********************************************************
        if ($request['tipo'] == 'storeVersion') 
        {
            //Hago una consulta para saber los directorios
    
         $carpetaReal = DB::Select('
                SELECT 
                    directorioDependencia,
                    directorioSerie,
                    directorioSubSerie,
                    directorioDocumento
                FROM
                    retenciondocumental r
                        LEFT JOIN
                    dependencia d ON r.Dependencia_idDependencia = d.idDependencia
                        LEFT JOIN
                    serie s ON r.Serie_idSerie = s.idSerie
                        LEFT JOIN
                    subserie ss ON r.SubSerie_idSubserie = ss.idSubSerie
                        LEFT JOIN
                    documento doc ON r.Documento_idDocumento = doc.idDocumento
                WHERE
                    r.Documento_idDocumento = '.$request['V_idDocumento'].'
                        AND r.Dependencia_idDependencia = '.$request['V_Dependencia_idDependencia'].'
                        AND r.Serie_idSerie = '.$request['V_Serie_idSerie'].'
                        AND r.SubSerie_idSubSerie = '.$request['V_SubSerie_idSubSerie']);

        //Convierto array en string
        $carpeta = get_object_vars($carpetaReal[0]);
        $destinationPath = '';
         if ($request['archivoNuevaVersion'] != '') 
        {
            $origen = public_path() . '/repositorio/temporal/'.$request['archivoNuevaVersion'];
            $ext = substr($request['archivoRadicado'], -4);

            $request['archivoRadicado'] = $carpeta['abreviaturaDependencia'].'_S'.$carpeta['codigoSerie'].'_SS'.$carpeta['codigoSubSerie'].'_'.$carpeta['nombreDocumento'].'_R'.$radicado->idRadicado.$ext;

            $destinationPath = public_path() . '/repositorio/'.$carpeta['directorioDependencia'].'/'.$carpeta['directorioSerie']."/".$carpeta['directorioSubSerie']."/".$carpeta['directorioDocumento']."/".$request['archivoRadicado'];

            if (file_exists($origen))
            {
                copy($origen, $destinationPath);
                unlink($origen);
            }
            else
            {
                echo "No ha cargado ningun archivo";
            }
        }
        
        //Guardo en documentoversion sus respectivos campos         
        \App\RadicadoVersion::create([
        'Radicado_idRadicado' => $request['V_Radicado_idRadicado'],
        'fechaRadicadoVersion' => $request['V_fechaRadicadoVersion'],
        'numeroRadicadoVersion' => $request['numeroVersionGuardar'],
        'tipoRadicadoVersion' => $request['tipoVersion'],
        'archivoRadicadoVersion' => $destinationPath
        ]);

        //Consulto a radicado version
        $radicadoVersion = \App\RadicadoVersion::All()->last();

        //Guarda en documentopropiedad sus respectivos campos
        //Realizo una consulta a la base de datos trayendo el idDocumento
        $metadatos = DB::table('documentopropiedad')
        ->leftjoin('metadato','documentopropiedad.Metadato_idMetadato', "=", 'metadato.idMetadato')
        ->select(DB::raw('documentopropiedad.*,metadato.*'))
        ->where('Documento_idDocumento', "=", $request['V_idDocumento'])
        // ->where('versionDocumentoPropiedad', "=", 1)
        ->get();

        //Hago un ciclo para que me guarde en registro independientes
        for($i = 0; $i < count($metadatos); $i++)
        {
            //Convertir array a string
            $nombremetadato = get_object_vars($metadatos[$i]);
            // Pregunto si es tipo editor para guardarlo en un campo diferente 
            $campo = ($nombremetadato["tipoMetadato"] == 'Editor') ? 'editorRadicadoDocumentoPropiedad' : 'valorRadicadoDocumentoPropiedad';
            $valor = $request[$nombremetadato["idDocumentoPropiedad"]];
            $valorCheck= $request[$nombremetadato["idDocumentoPropiedad"]];

            //Si un campo de documento propiedad es elecion multiple se separara por comas para gudarlos en un mismo registro
            if ($nombremetadato["tipoMetadato"] == 'EleccionMultiple')
                {
                    $valor = '';
                    foreach ($valorCheck as $key => $value)
                    {
                        $valor .= $value.',';
                    }
                    $valor = substr($valor, 0, strlen($valor)-1);
                }
            
            \App\RadicadoDocumentoPropiedad::create([
            'Radicado_idRadicado' => $request['V_Radicado_idRadicado'],
            'DocumentoPropiedad_idDocumentoPropiedad' => $nombremetadato["idDocumentoPropiedad"],
            $campo => $valor,
            'RadicadoVersion_idRadicadoVersion' => $radicadoVersion->idRadicadoVersion
            ]);
        }

        if($request->ajax()) 
        {
            return response()->json(['Ha actualizado el documento a la version '.$request['numeroVersionGuardar']]);
        }
     return redirect('/radicado');    
        }

        // *******************************************
        // 
        // ACTUALIZO EL RADICADO TIPO GESTION DOCUMENTAL
        // 
        // ******************************************

        else if($request['tipo'] == 'updateVersion')
        {
            $rutaOrigen = DB::table('radicado')
            ->leftjoin('serie','radicado.Serie_idSerie', "=", 'serie.idSerie')
            ->leftjoin('subserie','radicado.SubSerie_idSubserie', "=", 'serie.idSerie')
            ->leftjoin('documento','radicado.Documento_idDocumento', "=", 'documento.idDocumento')
            ->leftjoin('dependencia','radicado.Dependencia_idDependencia', "=", 'dependencia.idDependencia')
            ->leftJoin('radicadoversion', 'radicadoversion.Radicado_idRadicado', "=", 'radicado.idRadicado')
            ->select(DB::raw('directorioSerie, directorioSubSerie, directorioDocumento, directorioDependencia,archivoRadicadoVersion'))
            ->where('idRadicado', "=", $request['Radicado_idRadicado'])
            ->get();

            $origen = get_object_vars($rutaOrigen[0]);

            $carpetaReal = DB::Select('
                SELECT 
                    directorioDependencia,
                    directorioSerie,
                    directorioSubSerie,
                    directorioDocumento
                FROM
                    retenciondocumental r
                        LEFT JOIN
                    dependencia d ON r.Dependencia_idDependencia = d.idDependencia
                        LEFT JOIN
                    serie s ON r.Serie_idSerie = s.idSerie
                        LEFT JOIN
                    subserie ss ON r.SubSerie_idSubserie = ss.idSubSerie
                        LEFT JOIN
                    documento doc ON r.Documento_idDocumento = doc.idDocumento
                WHERE
                    r.Documento_idDocumento = '.$request['idDocumento'].'
                        AND r.Dependencia_idDependencia = '.$request['Dependencia_idDependencia'].'
                        AND r.Serie_idSerie = '.$request['Serie_idSerie'].'
                        AND r.SubSerie_idSubSerie = '.$request['SubSerie_idSubSerie']);

            $carpeta = get_object_vars($carpetaReal[0]);
            
            $origen = $origen['archivoRadicadoVersion'];
            $archivo = basename($origen);
            
            // $destinationPath = public_path() . '/repositorio/'.$carpeta['directorioSerie']."/".$carpeta['directorioSubSerie']."/".$carpeta['directorioDocumento']."/".$archivo; 
            $destinationPath = public_path() . '/repositorio/'.$carpeta['directorioDependencia'].'/'.$carpeta['directorioSerie']."/".$carpeta['directorioSubSerie']."/".$carpeta['directorioDocumento']."/".$archivo;         

            if ($request['idDocumento'] != '' and $origen != $destinationPath) 
            {
                if (file_exists($origen))
                {
                    copy($origen, $destinationPath);
                    unlink($origen);
                }
                else
                {
                    echo "No existe el archivo";
                }
            }

            // Se guardan datos especificos del encabezado y no el array completo
            $radicado = \App\Radicado::find($request['Radicado_idRadicado']);
            $radicado->fill($request->all());
            $radicado->codigoRadicado = $request['codigoRadicado'];
            $radicado->Dependencia_idDependencia = $request['Dependencia_idDependencia'];
            $radicado->Serie_idSerie = $request['Serie_idSerie'];
            $radicado->Documento_idDocumento = $request['idDocumento'];
            $radicado->SubSerie_idSubSerie = $request['SubSerie_idSubSerie'];
            $radicado->ubicacionEstanteRadicado = $request['ubicacionEstanteRadicado'];
            $radicado->Compania_idCompania = \Session::get("idCompania");
            // $radicado->archivoRadicado = $destinationPath;
            $radicado->save();

            \App\RadicadoEtiqueta::where('Radicado_idRadicado',$request['Radicado_idRadicado'])->delete();
            $etiquetaRadicado = explode(",",$_POST["etiquetaRadicado"]);

            for($i = 0; $i < count($etiquetaRadicado); $i++)
            {
                \App\RadicadoEtiqueta::create([
                'Radicado_idRadicado' => $request['Radicado_idRadicado'],
                'Etiqueta_idEtiqueta' => ($etiquetaRadicado[$i] != '') ? $etiquetaRadicado[$i] : null
                ]);
            }

            $indice = array(
                'idRadicadoVersion' => $request['RadicadoVersion_idRadicadoVersion']);

            $data= array(
                'Radicado_idRadicado' => $radicado->idRadicado,
                'fechaRadicadoVersion' => $request['fechaRadicadoVersion'],
                'numeroRadicadoVersion' => $request['numeroRadicadoVersionConsulta'],
                'tipoRadicadoVersion' => $request['tipoRadicadoVersioConsulta'],
                'archivoRadicadoVersion' => $destinationPath);
            
            $preguntas = \App\RadicadoVersion::updateOrCreate($indice, $data);
            
            //Realizo una consulta a la base de datos trayendo el idDocumento
            $metadatos = DB::table('documentopropiedad')
            ->leftjoin('metadato','documentopropiedad.Metadato_idMetadato', "=", 'metadato.idMetadato')
            ->select(DB::raw('documentopropiedad.*,metadato.*'))
            ->where('Documento_idDocumento', "=", $request['idDocumento'])
            ->get();

            for($i = 0; $i < count($metadatos); $i++)
            {
                $nombremetadato = get_object_vars($metadatos[$i]);

                $campo = ($nombremetadato["tipoMetadato"] == 'Editor') ? 'editorRadicadoDocumentoPropiedad' : 'valorRadicadoDocumentoPropiedad';
                $valor = $request[$nombremetadato["idDocumentoPropiedad"]];
                $valorCheck= $request[$nombremetadato["idDocumentoPropiedad"]];

                if ($nombremetadato["tipoMetadato"] == 'EleccionMultiple' || $nombremetadato["tipoMetadato"] == 'EleccionUnica')
                {
                    $valor = '';
                    foreach ($valorCheck as $key => $value)
                    {
                        $valor .= $value.',';
                    }
                    $valor = substr($valor, 0, strlen($valor)-1);
                }
                $indice = array(
                 'idRadicadoDocumentoPropiedad' => $request['idRadicadoDocumentoPropiedad'][$i]);

                 $data = array(
                 'Radicado_idRadicado' => $request['Radicado_idRadicado'],
                 'DocumentoPropiedad_idDocumentoPropiedad' => $nombremetadato["idDocumentoPropiedad"],
                  $campo => $valor,
                  'RadicadoVersion_idRadicadoVersion' => $request['RadicadoVersion_idRadicadoVersion']);

                $preguntas = \App\RadicadoDocumentoPropiedad::updateOrCreate($indice, $data);
            }

            if($request->ajax())
            {
                return response()->json(['Actualizado correctamente']);
            }
            
            return redirect('/radicado');
        }

        // *******************************************
        // 
        // INSERTO EL RADICADO TIPO FORMULARIO
        // 
        // ******************************************

        else if ($request['tipoFormulario'] == 'formulario') 
        {
            \App\Radicado::create([
            'codigoRadicado' => '',
            'Documento_idDocumento' => $request['idDocumentoFormulario'],
            'Compania_idCompania' => \Session::get("idCompania")
            ]); 

            $radicado = \App\Radicado::All()->last();
            //Guardo en documentoversion sus respectivos campos         
            $numeroVersion = ($request['versionInicialFormulario'] == '') ? '1.0' : $request['versionInicialFormulario'];
            \App\RadicadoVersion::create([
            'Radicado_idRadicado' => $radicado->idRadicado,
            'fechaRadicadoVersion' => $request['fechaFormulario'],
            'numeroRadicadoVersion' => $numeroVersion,
            'tipoRadicadoVersion' => $request['tipoVersionFormulario'],
            'archivoRadicadoVersion' => ''
            ]);

            //Guarda en documentopropiedad sus respectivos campos
            //Realizo una consulta a la base de datos trayendo el idDocumento
            $metadatos = DB::table('documentopropiedad')
            ->leftjoin('metadato','documentopropiedad.Metadato_idMetadato', "=", 'metadato.idMetadato')
            ->select(DB::raw('documentopropiedad.*,metadato.*'))
            ->where('Documento_idDocumento', "=", $request['idDocumentoFormulario'])
            ->get();

            //Hago un ciclo para que me guarde en registro independientes
            for($i = 0; $i < count($metadatos); $i++)
            {
                //Convertir array a string
                $nombremetadato = get_object_vars($metadatos[$i]);
                // Pregunto si es tipo editor para guardarlo en un campo diferente 
                $campo = ($nombremetadato["tipoMetadato"] == 'Editor') ? 'editorRadicadoDocumentoPropiedad' : 'valorRadicadoDocumentoPropiedad';
                $valor = $request[$nombremetadato["idDocumentoPropiedad"]];
                $valorCheck= $request[$nombremetadato["idDocumentoPropiedad"]];
                //Si un campo de documento propiedad es elecion multiple se separara por comas para gudarlos en un mismo registro
                if ($nombremetadato["tipoMetadato"] == 'EleccionMultiple')
                {
                    $valor = '';
                    foreach ($valorCheck as $key => $value)
                    {
                        $valor .= $value.',';
                    }
                    $valor = substr($valor, 0, strlen($valor)-1);
                }

                // return;
                $radicadoVersion = \App\RadicadoVersion::All()->last();
                \App\RadicadoDocumentoPropiedad::create([
                'Radicado_idRadicado' => $radicado->idRadicado,
                'DocumentoPropiedad_idDocumentoPropiedad' => $nombremetadato["idDocumentoPropiedad"],
                $campo => $valor,
                'RadicadoVersion_idRadicadoVersion' => $radicadoVersion->idRadicadoVersion
                ]);    
            }
            if($request->ajax()) 
                {
                    return response()->json(['Formulario insertado correctamente.']);
                }
                return redirect('/radicado');
        }

        // *******************************************
        // 
        // ACTUALIZO EL RADICADO TIPO FORMULARIO
        // 
        // *******************************************
        // return;
        else if ($request['tipoFormulario'] == 'formularioVersion') 
        {
            $radicado = \App\Radicado::find($request['F_Radicado_idRadicado']);
            $radicado->fill($request->all());
            $radicado->codigoRadicado = '';
            $radicado->Documento_idDocumento = $request['idDocumentoFC'];
            $radicado->Compania_idCompania = \Session::get("idCompania");
            $radicado->save();

            $radicado = \App\Radicado::All()->last();

            $indice = array(
                'idRadicadoVersion' => $request['F_RadicadoVersion_idRadicadoVersion']);

            $data= array(
                'Radicado_idRadicado' => $request['F_Radicado_idRadicado'],
                'fechaRadicadoVersion' => $request['fechaFormularioA'],
                'numeroRadicadoVersion' => $request['versionMaximaFormulario'],
                'tipoRadicadoVersion' => $request['F_tipoRadicadoVersioFormulario'],
                'archivoRadicadoVersion' => '');
            
            $preguntas = \App\RadicadoVersion::updateOrCreate($indice, $data);
            
            $radicadoVersion = \App\RadicadoVersion::All()->last();

            //Realizo una consulta a la base de datos trayendo el idDocumento
            $metadatos = DB::table('documentopropiedad')
            ->leftjoin('metadato','documentopropiedad.Metadato_idMetadato', "=", 'metadato.idMetadato')
            ->select(DB::raw('documentopropiedad.*,metadato.*'))
            ->where('Documento_idDocumento', "=", $request['idDocumentoFC'])
            ->get();

            for($i = 0; $i < count($metadatos); $i++)
            {
                $nombremetadato = get_object_vars($metadatos[$i]);

                $campo = ($nombremetadato["tipoMetadato"] == 'Editor') ? 'editorRadicadoDocumentoPropiedad' : 'valorRadicadoDocumentoPropiedad';
                $valor = $request[$nombremetadato["idDocumentoPropiedad"]];
                $valorCheck= $request[$nombremetadato["idDocumentoPropiedad"]];

                if ($nombremetadato["tipoMetadato"] == 'EleccionMultiple' || $nombremetadato["tipoMetadato"] == 'EleccionUnica')
                {
                    $valor = '';
                    foreach ($valorCheck as $key => $value)
                    {
                        $valor .= $value.',';
                    }
                    $valor = substr($valor, 0, strlen($valor)-1);
                }
                $indice = array(
                 'idRadicadoDocumentoPropiedad' => $request['idRadicadoDocumentoPropiedad'][$i]);

                 $data = array(
                 'Radicado_idRadicado' => $request['F_Radicado_idRadicado'],
                 'DocumentoPropiedad_idDocumentoPropiedad' => $nombremetadato["idDocumentoPropiedad"],
                  $campo => $valor,
                  'RadicadoVersion_idRadicadoVersion' => $radicadoVersion->idRadicadoVersion);

                $preguntas = \App\RadicadoDocumentoPropiedad::updateOrCreate($indice, $data);
            }

            if($request->ajax())
            {
                return response()->json(['Formulario actualizado correctamente.']);
            }
            
            return redirect('/radicado');
            
        }

        // *******************************************
        // 
        // INSERTO LA NUEVA VERSIÓN DEL RADICADO TIPO FORMULARIO
        // 
        // *******************************************
        // return;
        else if ($request['tipoFormulario'] == 'formularioNuevaVersion') 
        {
            //Guardo en documentoversion sus respectivos campos         
            \App\RadicadoVersion::create([
            'Radicado_idRadicado' => $request['FNV_Radicado_idRadicado'],
            'fechaRadicadoVersion' => $request['FNV_Fecha'],
            'numeroRadicadoVersion' => $request['numeroVersionFormulario'],
            'tipoRadicadoVersion' => $request['FNV_tipoVersion'],
            'archivoRadicadoVersion' => ''
            ]);

            //Consulto a radicado version
            $radicadoVersion = \App\RadicadoVersion::All()->last();

            //Guarda en documentopropiedad sus respectivos campos
            //Realizo una consulta a la base de datos trayendo el idDocumento
            $metadatos = DB::table('documentopropiedad')
            ->leftjoin('metadato','documentopropiedad.Metadato_idMetadato', "=", 'metadato.idMetadato')
            ->select(DB::raw('documentopropiedad.*,metadato.*'))
            ->where('Documento_idDocumento', "=", $request['FNV_idDocumento'])
            // ->where('versionDocumentoPropiedad', "=", 1)
            ->get();

            //Hago un ciclo para que me guarde en registro independientes
            for($i = 0; $i < count($metadatos); $i++)
            {
                //Convertir array a string
                $nombremetadato = get_object_vars($metadatos[$i]);
                // Pregunto si es tipo editor para guardarlo en un campo diferente 
                $campo = ($nombremetadato["tipoMetadato"] == 'Editor') ? 'editorRadicadoDocumentoPropiedad' : 'valorRadicadoDocumentoPropiedad';
                $valor = $request[$nombremetadato["idDocumentoPropiedad"]];
                $valorCheck= $request[$nombremetadato["idDocumentoPropiedad"]];

                //Si un campo de documento propiedad es elecion multiple se separara por comas para gudarlos en un mismo registro
                if ($nombremetadato["tipoMetadato"] == 'EleccionMultiple')
                {
                    $valor = '';
                    foreach ($valorCheck as $key => $value)
                    {
                        $valor .= $value.',';
                    }
                    $valor = substr($valor, 0, strlen($valor)-1);
                }
                
                \App\RadicadoDocumentoPropiedad::create([
                'Radicado_idRadicado' => $request['FNV_Radicado_idRadicado'],
                'DocumentoPropiedad_idDocumentoPropiedad' => $nombremetadato["idDocumentoPropiedad"],
                $campo => $valor,
                'RadicadoVersion_idRadicadoVersion' => $radicadoVersion->idRadicadoVersion
                ]);
            }

                if($request->ajax()) 
                {
                    return response()->json(['Ha actualizado el formulario a la version '.$request['numeroVersionFormulario']]);
                }
                return redirect('/radicado');    
        }  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\Radicado::destroy($id);
        $radicado = DB::Select('SELECT archivoRadicadoVersion from radicadoversion where Radicado_idRadicado in ("'.$id.'")');

        for ($i=0; $i < count($radicado); $i++) 
        { 
            $rutaimagen = get_object_vars($radicado[$i]);

            unlink($rutaimagen);
        }
        
        return response()->json(['Documento eliminado']);
    }

    protected function downloadFile($src)
        {
            if(is_file($src))
            {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $content_type = finfo_file($finfo, $src);
                finfo_close($finfo);
                $file_name = basename($src).PHP_EOL;
                $size = filesize($src);
                header("Content-Type: $content_type");
                header("Content-Disposition: attachment; filename=$file_name");
                header("Content-Transfer-Encoding: binary");
                header("Content-Length: $size");
                readfile($src);
                return true;
            } 
            else
            {
                return false;
            }
        }
        
        public function download()
        {
            $archivo = $_GET['archivo'];
            
            if(!$this->downloadFile($archivo))
            {
                return redirect()->back();
            }
        }

        public function cargar()
        {
            ?>
            <input type="file">
            <?php
        }

}