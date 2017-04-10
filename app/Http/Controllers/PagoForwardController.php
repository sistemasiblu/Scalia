<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PagoForwardRequest;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Config;
include public_path().'/ajax/consultarPermisos.php';
include public_path().'/ajax/actualizarCartera.php';

class PagoForwardController extends Controller
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
            return view('pagoforwardgrid', compact('datos'));
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
        $forward = \App\Forward::where('estadoForward','!=','Cerrado')->lists('numeroForward','idForward');
        return view('pagoforward',compact('forward'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PagoForwardRequest $request)
    {
        if($request['respuesta'] != 'falso')
        {
            \App\PagoForward::create([
            'fechaPagoForward' => $request['fechaPagoForward'],
            'Forward_idForward' => $request['Forward_idForward'],
            ]);

            $pagoforward = \App\PagoForward::All()->last();
            for ($i=0; $i <count($request['valorPagadoPagoForwardDetalle']); $i++) 
            {                 
                \App\PagoForwardDetalle::create([
                'PagoForward_idPagoForward' => $pagoforward->idPagoForward,
                'Temporada_idTemporada' => ($request['Temporada_idTemporada'][$i] == '' or $request['Temporada_idTemporada'][$i] == 0) ? null : $request['Temporada_idTemporada'][$i],
                'nombreTemporadaPagoForwardDetalle' => $request['nombreTemporadaPagoForwardDetalle'][$i],
                'Compra_idCompra' => ($request['Compra_idCompra'][$i] == '' or $request['Compra_idCompra'][$i] == 0) ? null : $request['Compra_idCompra'][$i],
                'numeroCompraPagoForwardDetalle' => $request['numeroCompraPagoForwardDetalle'][$i],
                'DocumentoFinanciero_idDocumentoFinanciero' => ($request['DocumentoFinanciero_idDocumentoFinanciero'][$i] == '' or $request['DocumentoFinanciero_idDocumentoFinanciero'][$i] == 0) ? null : $request['DocumentoFinanciero_idDocumentoFinanciero'][$i],
                'numeroDocumentoFinancieroPagoForwardDetalle' => $request['numeroDocumentoFinancieroPagoForwardDetalle'][$i],
                'facturaPagoForwardDetalle' => $request['facturaPagoForwardDetalle'][$i],
                'fechaFacturaPagoForwardDetalle' => $request['fechaFacturaPagoForwardDetalle'][$i],
                'valorFacturaPagoForwardDetalle' => $request['valorFacturaPagoForwardDetalle'][$i],
                'valorPagadoPagoForwardDetalle' => $request['valorPagadoPagoForwardDetalle'][$i],
                ]);

                // Despues de guardar cargamos los nuevos datos de la cartera
                actualizarCartera('carga','pago',$request['Compra_idCompra'][$i], $request['DocumentoFinanciero_idDocumentoFinanciero'][$i],$request['fechaPagoForward'], $request['valorPagadoPagoForwardDetalle'][$i]);
            }

            return redirect('/pagoforward');
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
        $pagofwd = DB::Select('
        SELECT 
            pf.fechaPagoForward as fechaPagoForward,
            f.numeroForward as numeroForward,
            f.descripcionForward as descripcionForward,
            f.fechaNegociacionForward as fechaNegociacionForward,
            f.fechaVencimientoForward as fechaVencimientoForward,
            f.modalidadForward as modalidadForward,
            f.valorDolarForward as valorDolarForward,
            f.tasaForward as tasaForward,
            f.tasaInicialForward as tasaInicialForward,
            f.valorPesosForward as valorPesosForward,
            f.bancoForward as bancoForward,
            f.rangeForward as rangeForward,
            f.devaluacionForward as devaluacionForward,
            f.spotForward as spotForward,
            f.estadoForward as estadoForward,
            fp.numeroForward as padreForward
        FROM
            pagoforward pf
                LEFT JOIN
            forward f ON pf.Forward_idForward = f.idForward
                LEFT JOIN
            forward fp ON f.ForwardPadre_idForwardPadre = fp.idForward
        WHERE
            idPagoForward = '.$id);

        $pagofwddetalle = DB::Select('
        SELECT 
            nombreTemporadaPagoForwardDetalle,
            numeroCompraPagoForwardDetalle,
            facturaPagoForwardDetalle,
            fechaFacturaPagoForwardDetalle
            valorFacturaPagoForwardDetalle,
            valorPagadoPagoForwardDetalle
        FROM
            pagoforwarddetalle pfd
                LEFT JOIN
            pagoforward pf ON pf.idPagoForward = pfd.PagoForward_idPagoForward
        WHERE
            PagoForward_idPagoForward = '.$id);

        return view('formatos.impresionPagoForward',compact('pagofwd','pagofwddetalle'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $pagoforward = \App\PagoForward::find($id);
        $forward = \App\Forward::All()->lists('numeroForward', 'idForward');
        return view('pagoforward',compact('forward'), ['pagoforward' => $pagoforward]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PagoForwardRequest $request, $id)
    {
        if($request['respuesta'] != 'falso')
        {
            $pagoforward = \App\PagoForward::find($id);
            $pagoforward->fill($request->all());
            $pagoforward->save();

            // Antes de guardar los datos modificados, descargamos los datos originales de la cartera

            // consultamos el detalle de pagos del forward
            $pagoF = DB::Select('SELECT * from pagoforwarddetalle where PagoForward_idPagoForward = '.$id);
            
            // recorremos el detalle depago original descargandolos de la cartera
            for ($i=0; $i < count($pagoF); $i++) 
            { 
                // convierto array a string
                $pagoForward = get_object_vars($pagoF[$i]);

                actualizarCartera('descarga','pago',$pagoForward['Compra_idCompra'], $pagoForward['DocumentoFinanciero_idDocumentoFinanciero'], $request['fechaPagoForward'], $pagoForward['valorPagadoPagoForwardDetalle']);

            }
                
            $idsEliminar = explode(',', $request['eliminarPagoForwardDetalle']);
            \App\PagoForwardDetalle::whereIn('idPagoForwardDetalle',$idsEliminar)->delete();
            for($i = 0; $i < count($request['valorFacturaPagoForwardDetalle']); $i++)
            {
                $indice = array(
                    'idPagoForwardDetalle' => $request['idPagoForwardDetalle'][$i]);

                $datos= array(
                'PagoForward_idPagoForward' => $pagoforward->idPagoForward,
                'Temporada_idTemporada' => ($request['Temporada_idTemporada'][$i] == '' or $request['Temporada_idTemporada'][$i] == 0) ? null : $request['Temporada_idTemporada'][$i],
                'nombreTemporadaPagoForwardDetalle' => $request['nombreTemporadaPagoForwardDetalle'][$i],
                'Compra_idCompra' => ($request['Compra_idCompra'][$i] == '' or $request['Compra_idCompra'][$i] == 0) ? null : $request['Compra_idCompra'][$i],
                'numeroCompraPagoForwardDetalle' => $request['numeroCompraPagoForwardDetalle'][$i],
                'DocumentoFinanciero_idDocumentoFinanciero' => ($request['DocumentoFinanciero_idDocumentoFinanciero'][$i] == '' or $request['DocumentoFinanciero_idDocumentoFinanciero'][$i] == 0) ? null : $request['DocumentoFinanciero_idDocumentoFinanciero'][$i],
                'numeroDocumentoFinancieroPagoForwardDetalle' => $request['numeroDocumentoFinancieroPagoForwardDetalle'][$i],
                'facturaPagoForwardDetalle' => $request['facturaPagoForwardDetalle'][$i],
                'fechaFacturaPagoForwardDetalle' => $request['fechaFacturaPagoForwardDetalle'][$i],
                'valorFacturaPagoForwardDetalle' => $request['valorFacturaPagoForwardDetalle'][$i],
                'valorPagadoPagoForwardDetalle' => $request['valorPagadoPagoForwardDetalle'][$i]);

                $guardar = \App\PagoForwardDetalle::updateOrCreate($indice, $datos);

                // Despues de guardar los datos modificados, cargamos los nuevos datos de la cartera
                actualizarCartera('carga','pago',$request['Compra_idCompra'][$i], $request['DocumentoFinanciero_idDocumentoFinanciero'][$i], $request['fechaPagoForward'], $request['valorPagadoPagoForwardDetalle'][$i]);
            }

            return redirect('/pagoforward');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        // descargamos la cartera
        $pagoforward = \App\PagoForward::find($id);

        // consultamos el detalle de pagos del forward
        $pagoF = DB::Select('SELECT * from pagoforwarddetalle where PagoForward_idPagoForward = '.$id);

        // recorremos el detalle depago original descargandolos de la cartera
        for ($i=0; $i < count($pagoF); $i++) 
        { 
            // convierto array a string
            $pagoForward = get_object_vars($pagoF[$i]);

            actualizarCartera('descarga','pago',$pagoForward['Compra_idCompra'], $pagoForward['DocumentoFinanciero_idDocumentoFinanciero'], $request['fechaPagoForward'], $pagoForward['valorPagadoPagoForwardDetalle']);
        }

        \App\PagoForward::destroy($id);
        return redirect('/pagoforward');
    }

}