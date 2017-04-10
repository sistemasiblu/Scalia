<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\CierreCompraRequest;
use App\Http\Controllers\Controller;
use DB;
include public_path().'/ajax/consultarPermisos.php';
include public_path().'/ajax/actualizarCartera.php';

class CierreCompraController extends Controller
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
            return view('cierrecompragrid', compact('datos'));
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
        return view('cierrecompra');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CierreCompraRequest $request)
    {
        if($request['respuesta'] != 'falso')
        {
            \App\CierreCompra::create([
                'numeroCierreCompra' => $request['numeroCierreCompra'],
                'fechaCierreCompra' => $request['fechaCierreCompra'],
                'descripcionCierreCompra' => $request['descripcionCierreCompra'],
                'Tercero_idProveedor' => ($request['Tercero_idProveedor'] == '' || $request['Tercero_idProveedor'] == 0 ? NULL : $request['Tercero_idProveedor']),
                'Users_id' => \Session::get('idUsuario')
            ]);

            $cierrecompra = \App\CierreCompra::All()->last();

            //---------------------------------
            // guardamos las tablas de detalle
            //---------------------------------
            $this->grabarDetalle($cierrecompra->idCierreCompra, $request);

            return redirect('/cierrecompra');
        }
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
        $cierrecompra = \App\CierreCompra::find($id);

        $tercero = DB::table('cierrecompra')
                        ->select(DB::raw('nombre1Tercero as nombreProveedorCierreCompra'))
                        ->leftJoin('Iblu.Tercero','cierrecompra.Tercero_idProveedor','=','Iblu.Tercero.idTercero')
                        ->where('idCierreCompra','=',$id)
                        ->get();

        $nombreTercero = get_object_vars($tercero[0]);


        $cierrecomprasaldo = DB::table('cierrecomprasaldo')
                        ->select(DB::raw('idCierreCompraSaldo, Compra_idCompra, numeroCompra as numeroCompraCierreCompraSaldo, nombreTemporadaCompra as nombreTemporadaCierreCompraSaldo, valorCierreCompraSaldo, Forward_idForward, numeroForward as numeroForwardCierreCompraSaldo, CierreCompra_idCierreCompra'))
                        ->leftJoin('compra','cierrecomprasaldo.Compra_idCompra','=','compra.idCompra')
                        ->leftJoin('forward','cierrecomprasaldo.Forward_idForward','=','forward.idForward')
                        ->where('CierreCompra_idCierreCompra','=',$id)
                        ->get();

        $cierrecompracartera = DB::table('cierrecompracartera')
                        ->select(DB::raw('idCierreCompraCartera, nombreDocumento as nombreDocumentoCierreCompraCartera, numeroMovimiento as numeroMovimientoCierreCompraCartera, numeroReferenciaExternoMovimiento as facturaCierreCompraCartera, numeroCompra as numeroCompraCierreCompraCartera,REPLACE(saldoCartera, "-", "") AS valorCierreCompraCartera, idDocumento as Documento_idDocumento, idMovimiento as Movimiento_idMovimiento, CierreCompra_idCierreCompra'))
                        ->leftJoin('Iblu.Movimiento','cierrecompracartera.Movimiento_idMovimiento', '=', 'Iblu.Movimiento.idMovimiento')
                        ->leftJoin('Iblu.Documento','cierrecompracartera.Documento_idDocumento','=','Iblu.Documento.idDocumento')
                        ->leftJoin('Iblu.Cartera','Iblu.Movimiento.idMovimiento','=','Iblu.Cartera.Movimiento_idMovimiento')
                        ->leftJoin('embarquedetalle', 'Movimiento.numeroReferenciaExternoMovimiento', '=', 'embarquedetalle.facturaEmbarqueDetalle')
                        ->leftJoin('compra', 'embarquedetalle.Compra_idCompra', '=', 'compra.idCompra')
                        ->where('CierreCompra_idCierreCompra','=',$id)
                        ->groupBy('idCierreCompraCartera')
                        ->get();

        return view('cierrecompra',compact('cierrecompra','cierrecomprasaldo','cierrecompracartera','nombreTercero'),['cierrecompra'=>$cierrecompra]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CierreCompraRequest $request, $id)
    {
        if($request['respuesta'] != 'falso')
        {
            $cierrecompra = \App\CierreCompra::find($id);
            $cierrecompra ->fill($request->all());
            $cierrecompra->Tercero_idProveedor = ($request['Tercero_idProveedor'] == '' || $request['Tercero_idProveedor'] == 0 ? NULL : $request['Tercero_idProveedor']);
            $cierrecompra->Users_id = \Session::get('idUsuario');
            $cierrecompra->save();

            $pagoC = DB::Select('SELECT Compra_idCompra, valorCierreCompraSaldo from cierrecomprasaldo where CierreCompra_idCierreCompra = '.$id);
                
            // recorremos el detalle de pago original descargandolos de la cartera
            for ($i=0; $i < count($pagoC); $i++) 
            { 
                // convierto array a string
                $pagoCompra = get_object_vars($pagoC[$i]);

                actualizarCartera('descarga','pago',$pagoCompra['Compra_idCompra'], '', $request['fechaCierreCompra'], $pagoCompra['valorCierreCompraSaldo']);
            }

            //---------------------------------
            // guardamos las tablas de detalle
            //---------------------------------
            $this->grabarDetalle($id, $request);

            return redirect('/cierrecompra');
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
        \App\CierreCompra::destroy($id);
        return redirect('/cierrecompra');
    }

    public function grabarDetalle($id, $request)
    {
        $idsEliminar = explode(',', $request['eliminarAbonoCartera']);
        \App\CierreCompraCartera::whereIn('idCierreCompraCartera',$idsEliminar)->delete();

        $contador = count($request['idCierreCompraCartera']);

        for($i = 0; $i < $contador; $i++)
        {

            $indice = array(
             'idCierreCompraCartera' => $request['idCierreCompraCartera'][$i]);

            $data = array(
            'CierreCompra_idCierreCompra' => $id,
            'Documento_idDocumento' => $request['Documento_idDocumento'][$i],
            'Movimiento_idMovimiento' => $request['Movimiento_idMovimiento'][$i],
            'valorCierreCompraCartera' => $request['valorCierreCompraCartera'][$i]);

             $preguntas = \App\CierreCompraCartera::updateOrCreate($indice, $data);

        }

        $idsEliminar = explode(',', $request['eliminarSaldoCartera']);
        \App\CierreCompraSaldo::whereIn('idCierreCompraSaldo',$idsEliminar)->delete();

        $contador = count($request['idCierreCompraSaldo']);

        for($i = 0; $i < $contador; $i++)
        {

            $indice = array(
             'idCierreCompraSaldo' => $request['idCierreCompraSaldo'][$i]);

            $data = array(
            'CierreCompra_idCierreCompra' => $id,
            'Compra_idCompra' => $request['Compra_idCompra'][$i],
            'Forward_idForward' => ($request['Forward_idForward'][$i] == '' ? NULL : $request['Forward_idForward'][$i]),
            'valorCierreCompraSaldo' => $request['valorCierreCompraSaldo'][$i]);

             $preguntas = \App\CierreCompraSaldo::updateOrCreate($indice, $data);

             // Despues de guardar cargamos los nuevos datos de la cartera
                actualizarCartera('carga','pago',$request['Compra_idCompra'][$i], '',$request['fechaCierreCompra'], $request['valorCierreCompraSaldo'][$i]);

                // Por ultimo liberamos el saldo del forward que no se utilizó en el pago de la compra
                if ($request['Forward_idForward'][$i] != NULL) 
                {
                    DB::Update('UPDATE forwarddetalle 
                            SET 
                                valorRealForwardDetalle = valorRealForwardDetalle -'.$request["valorCierreCompraSaldo"][$i].'
                            WHERE
                                Compra_idCompra = '.$request['Compra_idCompra'][$i].' 
                            AND Forward_idForward = '.$request['Forward_idForward'][$i]);
                }
        }
    }
}
