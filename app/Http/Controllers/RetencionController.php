<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\RetencionRequest;
use App\Http\Controllers\Controller;
use DB;
use File;
include public_path().'/ajax/consultarPermisos.php';

class RetencionController extends Controller
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
            return view('retenciongrid', compact('datos'));
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
        $nombreDependencia = \App\Dependencia::All()->lists('nombreDependencia');
        $idDependencia = \App\Dependencia::All()->lists('idDependencia');
        $nombreSerie = \App\Serie::All()->lists('nombreSerie');
        $idSerie = \App\Serie::All()->lists('idSerie');
        $nombreSubSerie = \App\SubSerie::All()->lists('nombreSubSerie');
        $idSubSerie = \App\SubSerie::All()->lists('idSubSerie');
        $nombreDocumento = \App\Documento::All()->lists('nombreDocumento');
        $idDocumento = \App\Documento::All()->lists('idDocumento');


        return view('retencion',compact('nombreDependencia','idDependencia','nombreSerie','idSerie','nombreSubSerie','idSubSerie','nombreDocumento','idDocumento'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RetencionRequest $request)
    {
        \App\Retencion::create([
        'anioRetencion' => $request['anioRetencion'],
        'Compania_idCompania' => \Session::get("idCompania")

        ]);

        $retencion = \App\Retencion::All()->last();
        $contadorRetencionDocumental = count($request['Dependencia_idDependencia']);
        for($i = 0; $i < $contadorRetencionDocumental; $i++)
        {
            \App\RetencionDocumental::create([
            'Retencion_idRetencion' => $retencion->idRetencion,
            'Dependencia_idDependencia' => $request['Dependencia_idDependencia'][$i],
            'Serie_idSerie' => $request['Serie_idSerie'][$i],
            'SubSerie_idSubSerie' => $request['SubSerie_idSubSerie'][$i],
            'Documento_idDocumento' => $request['Documento_idDocumento'][$i],
            'retencionGestionRetencionDocumental' => $request['retencionGestionRetencionDocumental'][$i],
            'retencionCentralRetencionDocumental' => $request['retencionCentralRetencionDocumental'][$i],
            'soporteRetencionDocumental' => $request['soporteRetencionDocumental'][$i],
            'disposicionFinalRetencionDocumental' => $request['disposicionFinalRetencionDocumental'][$i],
            'microfilmRetencionDocumental' => $request['microfilmRetencionDocumental'][$i],
            'procedimientoRetencionDocumental' => $request['procedimientoRetencionDocumental'][$i]
            ]);

            $carpetas = DB::Select('SELECT directorioDependencia, directorioSerie, directorioSubSerie, directorioDocumento from retenciondocumental r
                left join dependencia d
                on r.Dependencia_idDependencia = d.idDependencia
                left join serie s
                on r.Serie_idSerie = s.idSerie
                left join subserie ss
                on r.SubSerie_idSubserie = ss.idSubSerie
                left join documento doc
                on r.Documento_idDocumento = doc.idDocumento
                where Retencion_idRetencion = '.$id);

            $dir = get_object_vars($carpetas[$i]);

            $directorioDep = public_path() . '/repositorio/' . $dir['directorioDependencia'];
            if (!File::exists($directorioDep)) 
            {
                $resultado = File::makeDirectory($directorioDep , 0777, true);
            }

            $directorioSer = $directorioDep.'/'.$dir['directorioSerie'];
            if (!File::exists($directorioSer))
            {
                $resultado = File::makeDirectory($directorioSer , 0777, true);
            }

            $directorioSubSer = $directorioSer.'/'.$dir['directorioSubSerie'];
            if (!File::exists($directorioSubSer))
            {
                $resultado = File::makeDirectory($directorioSubSer , 0777, true);
            }

            $directorioDoc = $directorioSubSer.'/'.$dir['directorioDocumento'];
            if (!File::exists($directorioDoc))
            {
                $resultado = File::makeDirectory($directorioDoc , 0777, true);
            }
        }
        return redirect('/retencion');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request){

    if(isset($request['dependenciaClasificacionDocumental']))
    {
        $clasificaciondocumental = DB::table('clasificaciondocumental')
        ->leftjoin('serie', 'clasificaciondocumental.Serie_idSerie', "=", 'serie.idSerie')
        ->leftjoin('subserie', 'subserie.Serie_idSerie', "=", 'serie.idSerie')
        ->leftjoin('documento', 'subserie.Documento_idDocumento', "=", 'documento.idDocumento')
        ->select (DB::raw('idSerie, nombreSerie'))
        ->where ('idClasificacionDocumental', "=", $request['dependenciaClasificacionDocumental'])
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
           ->select(DB::raw('idSubSerie, nombreSubSerie'))
           ->leftjoin('documento', 'subserie.Documento_idDocumento', "=", 'documento.idDocumento')
           ->where ('Serie_idSerie', "=", $request['Serie_idSerie'])
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $retencion = \App\Retencion::find($id);
        $nombreDependencia = \App\Dependencia::All()->lists('nombreDependencia');
        $idDependencia = \App\Dependencia::All()->lists('idDependencia');
        $nombreSerie = \App\Serie::All()->lists('nombreSerie');
        $idSerie = \App\Serie::All()->lists('idSerie');
        $nombreSubSerie = \App\SubSerie::All()->lists('nombreSubSerie');
        $idSubSerie = \App\SubSerie::All()->lists('idSubSerie');
        $nombreDocumento = \App\Documento::All()->lists('nombreDocumento');
        $idDocumento = \App\Documento::All()->lists('idDocumento');


        return view('retencion',compact('nombreDependencia','idDependencia','nombreSerie','idSerie','nombreSubSerie','idSubSerie','nombreDocumento','idDocumento'),['retencion' => $retencion]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RetencionRequest $request, $id)
    {
        $retencion = \App\Retencion::find($id);
        $retencion->fill($request->all());
        $retencion->save();

        $idsEliminar = explode(',', $request['eliminarRetencionDocumental']);
        \App\RetencionDocumental::whereIn('idRetencionDocumental',$idsEliminar)->delete();

        $contadorRetencionDocumental = count($request['Dependencia_idDependencia']);
        for($i = 0; $i < $contadorRetencionDocumental; $i++)
        {
             $indice = array(
                'idRetencionDocumental' => $request['idRetencionDocumental'][$i]);

            $datos= array(
                'Retencion_idRetencion' => $id,
                'Dependencia_idDependencia' => $request['Dependencia_idDependencia'][$i],
                'Serie_idSerie' => $request['Serie_idSerie'][$i],
                'SubSerie_idSubSerie' => $request['SubSerie_idSubSerie'][$i],
                'Documento_idDocumento' => $request['Documento_idDocumento'][$i],
                'retencionGestionRetencionDocumental' => $request['retencionGestionRetencionDocumental'][$i],
                'retencionCentralRetencionDocumental' => $request['retencionCentralRetencionDocumental'][$i],
                'soporteRetencionDocumental' => $request['soporteRetencionDocumental'][$i],
                'disposicionFinalRetencionDocumental' => $request['disposicionFinalRetencionDocumental'][$i],
                'microfilmRetencionDocumental' => $request['microfilmRetencionDocumental'][$i],
                'procedimientoRetencionDocumental' => $request['procedimientoRetencionDocumental'][$i]
                );

            $guardar = \App\RetencionDocumental::updateOrCreate($indice, $datos);

            $carpetas = DB::Select('SELECT directorioDependencia, directorioSerie, directorioSubSerie, directorioDocumento from retenciondocumental r
                left join dependencia d
                on r.Dependencia_idDependencia = d.idDependencia
                left join serie s
                on r.Serie_idSerie = s.idSerie
                left join subserie ss
                on r.SubSerie_idSubserie = ss.idSubSerie
                left join documento doc
                on r.Documento_idDocumento = doc.idDocumento
                where Retencion_idRetencion = '.$id);

            $dir = get_object_vars($carpetas[$i]);

            $directorioDep = public_path() . '/repositorio/' . $dir['directorioDependencia'];
            if (!File::exists($directorioDep)) 
            {
                $resultado = File::makeDirectory($directorioDep , 0777, true);
            }

            $directorioSer = $directorioDep.'/'.$dir['directorioSerie'];
            if (!File::exists($directorioSer))
            {
                $resultado = File::makeDirectory($directorioSer , 0777, true);
            }

            $directorioSubSer = $directorioSer.'/'.$dir['directorioSubSerie'];
            if (!File::exists($directorioSubSer))
            {
                $resultado = File::makeDirectory($directorioSubSer , 0777, true);
            }

            $directorioDoc = $directorioSubSer.'/'.$dir['directorioDocumento'];
            if (!File::exists($directorioDoc))
            {
                $resultado = File::makeDirectory($directorioDoc , 0777, true);
            }
        }

        return redirect('/retencion');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\Retencion::destroy($id);
        return redirect('/retencion');
    }
}
