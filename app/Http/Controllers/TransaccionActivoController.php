<?php

namespace App\Http\Controllers;

use Illuminate\Support\CollectionStdClass;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Requests\activoRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;

class TransaccionActivoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('transaccionactivogrid');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $transacciongrupo=\App\TransaccionGrupo::lists('nombreTransaccionGrupo','idTransaccionGrupo')->prepend('Selecciona');
        
        return view('transaccionactivo',['transacciongrupo'=>$transacciongrupo]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
     

    \App\TransaccionActivo::create(
    [
    'codigoTransaccionActivo'=>$request['codigoTransaccionActivo'],
    'nombreTransaccionActivo'=>$request['nombreTransaccionActivo'],
    'formatoTransaccionActivo'=>$request['formatoTransaccionActivo'],
    'tipoNumeracionTransaccionActivo'=>$request['tipoNumeracionTransaccionActivo'],
    'longitudTransaccionActivo'=>$request['longitudTransaccionActivo'],
    'desdeTransaccionActivo'=>$request['desdeTransaccionActivo'],
    'hastaTransaccionActivo'=>$request['hastaTransaccionActivo'], 
    'TransaccionGrupo_idTransaccionGrupo'=>$request['TransaccionGrupo_idTransaccionGrupo'],
    'accionTransaccionActivo'=>$request['accionTransaccionActivo'], 
    'estadoTransaccionActivo'=>$request['estadoTransaccionActivo'],
    'Compania_idCompania'=>Session::get('idCompania')

    ]);

    $transaccionactivoultimo = \App\TransaccionActivo::All()->last();

    echo count($request['CampoTransaccion_idCampoTransaccionE']);
    for ($i=0 ; $i < count($request['CampoTransaccion_idCampoTransaccionE']); $i++)
    {
        \App\TransaccionActivoCampo::create([
        'TransaccionActivo_idTransaccionActivo'=>$transaccionactivoultimo->idTransaccionActivo,
        'CampoTransaccion_idCampoTransaccion'=>$request['CampoTransaccion_idCampoTransaccionE'][$i],
        'gridTransaccionActivoCampo'=>$request['gridTransaccionActivoCampoE'][$i],
        'obligatorioTransaccionActivoCampo' =>$request['obligatorioTransaccionActivoCampoE'][$i],         
         ]); 

    }

   

    echo count($request['idTransaccionConcepto']);
    for ($i=0 ; $i < count($request['idTransaccionConcepto']); $i++)
    {
        \App\TransaccionConcepto::create([
        'TransaccionActivo_idTransaccionActivo'=>$transaccionactivoultimo->idTransaccionActivo,
        'ConceptoActivo_idConceptoActivo'=>$request['idConceptoActivo'][$i]

         ]); 

    }

   
    echo count($request['idTransaccionRol']);
    for ($i=0 ; $i < count($request['idTransaccionRol']); $i++)
    {
        \App\TransaccionRol::create([
        'TransaccionActivo_idTransaccionActivo'=>$transaccionactivoultimo->idTransaccionActivo,
        'Rol_idRol'=>$request['Rol_idRol'][$i],
        'adicionarTransaccionRol'=>$request['adicionarTransaccionRol'][$i],
        'modificarTransaccionRol'=>$request['modificarTransaccionRol'][$i],
        'anularTransaccionRol'=>$request['anularTransaccionRol'][$i],
        'consultarTransaccionRol'=>$request['consultarTransaccionRol'][$i],
        'autorizarTransaccionRol'=>$request['autorizarTransaccionRol'][$i]
            
         ]); 

    }
        
    
         return redirect('\transaccionactivo');

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
         $transacciongrupo=\App\TransaccionGrupo::lists('nombreTransaccionGrupo','idTransaccionGrupo')->prepend('Selecciona');
        $transaccionactivo = \App\TransaccionActivo::find($id);



        $encabezado=DB::Select(
        "select idTransaccionActivoCampo as idTransaccionActivoCampoE,
        CampoTransaccion_idCampoTransaccion as CampoTransaccion_idCampoTransaccionE, 
        campotransaccion.descripcionCampoTransaccion as descripcionCampoTransaccionE, 
        gridTransaccionActivoCampo as gridTransaccionActivoCampoE,
        vistaTransaccionActivoCampo as vistaTransaccionActivoCampoE,
        obligatorioTransaccionActivoCampo as obligatorioTransaccionActivoCampoE
        from transaccionactivocampo
        inner join transaccionactivo
        on transaccionactivocampo.TransaccionActivo_idTransaccionActivo=transaccionactivo.idTransaccionActivo
        inner join campotransaccion
        on transaccionactivocampo.CampoTransaccion_idCampoTransaccion=campotransaccion.idCampoTransaccion  where tipoCampoTransaccion='Encabezado' and TransaccionActivo_idTransaccionActivo=".$id
        );

    for ($i=0 ; $i < count( $encabezado); $i++) 
    {  
        $transaccionEncabezado[] = get_object_vars($encabezado[$i]);
    }

    

    $concepto=DB::Select(
        "select 
       idTransaccionConcepto,idConceptoActivo,codigoConceptoActivo,nombreConceptoActivo
       from transaccionactivo
        inner join transaccionconcepto
        on transaccionconcepto.TransaccionActivo_idTransaccionActivo=transaccionactivo.idTransaccionActivo
         inner join conceptoactivo
        on transaccionconcepto.ConceptoActivo_idConceptoActivo=conceptoactivo.idConceptoActivo
        where TransaccionActivo_idTransaccionActivo=".$id);

    for ($i=0 ; $i < count( $concepto); $i++) 
    {  
        $transaccionConcepto[] = get_object_vars($concepto[$i]);
    }


    
    $rol=DB::Select(
        "select 
     idTransaccionRol,nombreRol,TransaccionActivo_idTransaccionActivo, Rol_idRol,adicionarTransaccionRol,modificarTransaccionRol,consultarTransaccionRol,anularTransaccionRol,autorizarTransaccionRol
       from transaccionactivo
        inner join transaccionrol
        on transaccionrol.TransaccionActivo_idTransaccionActivo=transaccionactivo.idTransaccionActivo
        inner join rol
        on transaccionrol.Rol_idRol=rol.idRol
        where TransaccionActivo_idTransaccionActivo=".$id);

    for ($i=0 ; $i < count( $rol); $i++) 
    {  
        $transaccionRol[] = get_object_vars($rol[$i]);
    }

        return view('transaccionactivo',['transaccionactivo'=>$transaccionactivo],compact('transacciongrupo','transaccionEncabezado','transaccionConcepto','transaccionRol'));
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
        $transaccionactivo=\App\TransaccionActivo::find($id);
        $transaccionactivo->fill($request->all());
        $transaccionactivo->save();

        $idsEncabezadoEliminar = explode(',', $request['encabezadoEliminar']);
        \App\TransaccionActivoCampo::whereIn('idTransaccionActivoCampo',$idsEncabezadoEliminar)->delete();
        for ($i=0 ; $i < count($request['idTransaccionActivoCampoE']); $i++)
        {
           $indice = array(
            'idTransaccionActivoCampo' => $request['idTransaccionActivoCampoE'][$i]);

           $data = array(
            'TransaccionActivo_idTransaccionActivo' => $id, 
            'CampoTransaccion_idCampoTransaccion'=>$request['CampoTransaccion_idCampoTransaccionE'][$i], 
            'obligatorioTransaccionActivoCampo' =>$request['obligatorioTransaccionActivoCampoE'][$i], 
            'gridTransaccionActivoCampo'=>$request['gridTransaccionActivoCampoE'][$i], 
            'vistaTransaccionActivoCampo'=>$request['vistaTransaccionActivoCampoE'][$i], 
            );

           $respuesta = \App\TransaccionActivoCampo::updateorcreate($indice, $data);
        }

        

       $idsConceptoEliminar = explode(',', $request['conceptoEliminar']);
        \App\TransaccionConcepto::whereIn('idTransaccionConcepto',$idsConceptoEliminar)->delete();
        for ($i=0 ; $i < count($request['idTransaccionConcepto']); $i++)
        {
           $indice = array(
            'idTransaccionConcepto' => $request['idTransaccionConcepto'][$i]);

           $data = array(
            'TransaccionActivo_idTransaccionActivo' => $id, 
            'ConceptoActivo_idConceptoActivo'=>$request['idConceptoActivo'][$i],

            );

           $respuesta = \App\TransaccionConcepto::updateorcreate($indice, $data);
        } 

      

        $idsRolEliminar = explode(',', $request['permisosEliminar']);
        \App\TransaccionRol::whereIn('idTransaccionRol',$idsRolEliminar)->delete();
        for ($i=0 ; $i < count($request['idTransaccionRol']); $i++)
        {
           $indice = array(
            'idTransaccionRol' => $request['idTransaccionRol'][$i]);

           $data = array(
            'TransaccionActivo_idTransaccionActivo' => $id, 
            'Rol_idRol'=>$request['Rol_idRol'][$i],
            'adicionarTransaccionRol'=>$request['adicionarTransaccionRol'][$i],
            'modificarTransaccionRol'=>$request['modificarTransaccionRol'][$i],
            'anularTransaccionRol'=>$request['anularTransaccionRol'][$i],
            'consultarTransaccionRol'=>$request['consultarTransaccionRol'][$i],
            'autorizarTransaccionRol'=>$request['autorizarTransaccionRol'][$i]

            );

           $respuesta = \App\TransaccionRol::updateorcreate($indice, $data);
        } 



        return redirect('\transaccionactivo');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\TransaccionActivo::destroy($id);
        return redirect('\transaccionactivo');
    }
}
