<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
class ClasificacionDocumentalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clasificaciondocumental = \App\ClasificacionDocumentalEnc::All()->last();
        $nombreSerie = \App\Serie::All()->lists('nombreSerie');
        $idSerie = \App\Serie::All()->lists('idSerie');
        $nombreSubSerie = \App\SubSerie::All()->lists('nombreSubSerie');
        $idSubSerie = \App\SubSerie::All()->lists('idSubSerie');
        $anioRetencion = \App\Retencion::All()->lists('anioRetencion');
        $idRetencion = \App\Retencion::All()->lists('idRetencion');

        return view('clasificaciondocumental', 
            compact('nombreSerie','idSerie',
                    'nombreSubSerie','idSubSerie',
                    'anioRetencion','idRetencion', 'clasificaciondocumental'));
            
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $clasificaciondocumental = \App\ClasificacionDocumentalEnc::All()->last();
        if(count($clasificaciondocumental) == 0)
        {
            \App\ClasificacionDocumentalEnc::create([
            'idClasificacionDocumentalEnc' => $request['idClasificacionDocumentalEnc'],
            ]);

            $clasificaciondocumental = \App\ClasificacionDocumentalEnc::All()->last();
        }


        $contadorClasificacionDocumental = count($request['dependenciaClasificacionDocumental']);               
        for($i = 0; $i < $contadorClasificacionDocumental; $i++)
        {
            \App\ClasificacionDocumental::create([
            'dependenciaClasificacionDocumental' => $request['dependenciaClasificacionDocumental'][$i],
            'subdependenciaClasificacionDocumental' => $request['subdependenciaClasificacionDocumental'][$i],
            'Serie_idSerie' => $request['Serie_idSerie'][$i],
            'SubSerie_idSubSerie' => $request['SubSerie_idSubSerie'][$i],
            'Retencion_idRetencion' => $request['Retencion_idRetencion'][$i],
            'estadoClasificacionDocumental' => $request['estadoClasificacionDocumental'][$i],
            'ClasificacionDocumentalEnc_idClasificacionDocumentalEnc' => 1
            ]);
        }
        return redirect('/clasificaciondocumental');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $clasificaciondocumental = \App\ClasificacionDocumentalEnc::find($id);
        $clasificaciondocumental->fill($request->all());
        $clasificaciondocumental->save();

        \App\ClasificacionDocumental::where('ClasificacionDocumentalEnc_idClasificacionDocumentalEnc',$id)->delete();
        $contadorClasificacionDocumental = count($request['dependenciaClasificacionDocumental']);
        for($i = 0; $i < $contadorClasificacionDocumental; $i++)
        {
            \App\ClasificacionDocumental::create([
            'dependenciaClasificacionDocumental' => $request['dependenciaClasificacionDocumental'][$i],
            'subdependenciaClasificacionDocumental' => $request['subdependenciaClasificacionDocumental'][$i],
            'Serie_idSerie' => $request['Serie_idSerie'][$i],
            'SubSerie_idSubSerie' => $request['SubSerie_idSubSerie'][$i],
            'Retencion_idRetencion' => $request['Retencion_idRetencion'][$i],
            'estadoClasificacionDocumental' => $request['estadoClasificacionDocumental'][$i],
            'ClasificacionDocumentalEnc_idClasificacionDocumentalEnc' => 1
            ]);
        }
        
        return redirect('/scalia');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\ClasificacionDocumentalEnc::destroy($id);
        return redirect('/clasificaciondocumental');
    }
}
