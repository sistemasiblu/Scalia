<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\ControlIngresoRequest;
use App\Http\Controllers\Controller;
use DB;
include public_path().'/ajax/consultarPermisos.php';

class ControlIngresoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $tipodocumento = DB::Select(
            "SELECT idIdentificacion as id, nombreIdentificacion as nombre
            FROM Iblu.TipoIdentificacion");    
        $tipodocumento = $this->convertirArray($tipodocumento);

        $responsable = DB::Select(
            "SELECT nombre1Tercero as nombre, idTercero as id
            FROM Iblu.Tercero
            WHERE tipoTercero like '%05%' 
            AND estadoTercero = 'ACTIVO'
            ORDER BY nombre ASC");    
        $responsable = $this->convertirArray($responsable);

        $nombreDispositivo = \App\Dispositivo::All()->lists('nombreDispositivo');
        $idDispositivo = \App\Dispositivo::All()->lists('idDispositivo');
        $nombreMarca = \App\Marca::All()->lists('nombreMarca');
        $idMarca = \App\Marca::All()->lists('idMarca');

        return view('controlingreso',compact('nombreDispositivo','idDispositivo','nombreMarca','idMarca','responsable','tipodocumento'));

    }

    function convertirArray($dato)
    {
        $nuevo = array();
        $nuevo[0] = 'Seleccione';
        for($i = 0; $i < count($dato); $i++) 
        {
          $nuevo[get_object_vars($dato[$i])["id"]] = get_object_vars($dato[$i])["nombre"] ;
        }
        return $nuevo;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $nombreDispositivo = \App\Dispositivo::All()->lists('nombreDispositivo');
        $idDispositivo = \App\Dispositivo::All()->lists('idDispositivo');
        $nombreMarca = \App\Marca::All()->lists('nombreMarca');
        $idMarca = \App\Marca::All()->lists('idMarca');

        return view('controlingreso',compact('nombreDispositivo','idDispositivo','nombreMarca','idMarca'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ControlIngresoRequest $request)
    {
        $index = array(
            'idControlIngreso' => $request['idControlIngreso']);

        $data= array(
            'TipoIdentificacion_idTipoIdentificacion' => $request['TipoIdentificacion_idTipoIdentificacion'],
            'numeroDocumentoVisitanteControlIngreso' => $request['numeroDocumentoVisitanteControlIngreso'],
            'nombreVisitanteControlIngreso' => $request['nombreVisitanteControlIngreso'],
            'apellidoVisitanteControlIngreso' => $request['apellidoVisitanteControlIngreso'],
            'Tercero_idResponsable' => $request['Tercero_idResponsable'],
            'dependenciaControlIngreso' => $request['dependenciaControlIngreso'],
            'fechaIngresoControlIngreso' => $request['fechaIngresoControlIngreso'],
            'fechaSalidaControlIngreso' => $request['fechaSalidaControlIngreso'],
            'observacionControlIngreso' => $request['observacionControlIngreso']);
        
        $guardar = \App\ControlIngreso::updateOrCreate($index, $data);
        
        if ($request['idControlIngreso'] != '' or $request['idControlIngreso'] != 0) 
            $controlingreso = \App\ControlIngreso::find($request['idControlIngreso']);    
        else
            $controlingreso = \App\ControlIngreso::All()->last();

        for ($i=0; $i <count($request['observacionControlIngresoDetalle']); $i++) 
        { 
            $indice = array(
                'idControlIngresoDetalle' => $request['idControlIngresoDetalle'][$i]);

            $datos= array(
                'ControlIngreso_idControlIngreso' => $controlingreso->idControlIngreso,
                'Dispositivo_idDispositivo' => $request['Dispositivo_idDispositivo'][$i],
                'Marca_idMarca' => $request['Marca_idMarca'][$i],
                'referenciaDispositivoControlIngresoDetalle' => $request['referenciaDispositivoControlIngresoDetalle'][$i],
                'observacionControlIngresoDetalle' => $request['observacionControlIngresoDetalle'][$i],
                'retiraDispositivoControlIngresoDetalle' => ($request['retiraDispositivoControlIngresoDetalle'][$i] == '' ? 0 : $request['retiraDispositivoControlIngresoDetalle'][$i]));

            $guardar = \App\ControlIngresoDetalle::updateOrCreate($indice, $datos);
        }

        return redirect('controlingreso');
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
    // public function edit($id)
    // {
    //     $controlingreso = \App\ControlIngreso::find($id);
    //     $nombreDispositivo = \App\Dispositivo::All()->lists('nombreDispositivo');
    //     $idDispositivo = \App\Dispositivo::All()->lists('idDispositivo');
    //     $nombreMarca = \App\Marca::All()->lists('nombreMarca');
    //     $idMarca = \App\Marca::All()->lists('idMarca');

    //     return view('controlingreso',compact('nombreDispositivo','idDispositivo','nombreMarca','idMarca'),['controlingreso' => $controlingreso]);
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function update(ControlIngresoRequest $request, $id)
    // {
    //     $controlingreso = \App\ControlIngreso::find($id);
    //     $controlingreso->fill($request->all());
    //     $controlingreso->save();

    //     $idsEliminar = explode(',', $request['eliminarControlIngreso']);
    //     \App\ControlIngresoDetalle::whereIn('idControlIngresoDetalle',$idsEliminar)->delete();

    //     for ($i=0; $i <count($request['Dispositivo_idDispositivo']); $i++) 
    //     { 
    //          $indice = array(
    //             'idControlIngresoDetalle' => $request['idControlIngresoDetalle'][$i]);

    //         $datos= array(
    //             'ControlIngreso_idControlIngreso' => $id,
    //             'Dispositivo_idDispositivo' => $request['Dispositivo_idDispositivo'][$i],
    //             'Marca_idMarca' => $request['Marca_idMarca'][$i],
    //             'referenciaDispositivoControlIngresoDetalle' => $request['referenciaDispositivoControlIngresoDetalle'][$i],
    //             'observacionControlIngresoDetalle' => $request['observacionControlIngresoDetalle'][$i]);
            
    //         $guardar = \App\ControlIngresoDetalle::updateOrCreate($indice, $datos);
    //     }

    //     return redirect('controlingreso');
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\ControlIngreso::destroy($id);
        return redirect('controlingreso');
    }
}
