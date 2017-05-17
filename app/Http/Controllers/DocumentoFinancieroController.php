<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\DocumentoFinancieroRequest;
use App\Http\Controllers\Controller;
use DB;
include public_path().'/ajax/consultarPermisos.php';
include public_path().'/ajax/actualizarCartera.php';

class DocumentoFinancieroController extends Controller
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
            return view('documentofinancierogrid', compact('datos'));
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
        $listafinanciacion = \App\ListaFinanciacion::All()->lists('nombreListaFinanciacion','idListaFinanciacion');
        
        return view('documentofinanciero',compact('listafinanciacion'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DocumentoFinancieroRequest $request)
    {
        if($request['respuesta'] != 'falso')
        {
            \App\DocumentoFinanciero::create([
            'ListaFinanciacion_idListaFinanciacion' => $request['ListaFinanciacion_idListaFinanciacion'],
            'numeroDocumentoFinanciero' => $request['numeroDocumentoFinanciero'],
            'fechaNegociacionDocumentoFinanciero' => $request['fechaNegociacionDocumentoFinanciero'],
            'fechaVencimientoDocumentoFinanciero' => $request['fechaVencimientoDocumentoFinanciero'],
            'nombreEntidadDocumentoFinanciero' => $request['nombreEntidadDocumentoFinanciero'],
            'valorDocumentoFinanciero' => $request['valorDocumentoFinanciero']
            ]);

            $documentofinanciero = \App\DocumentoFinanciero::All()->last();
            for($i = 0; $i < count($request['valorFobDocumentoFinancieroDetalle']); $i++)
            {            
                \App\DocumentoFinancieroDetalle::create([
                    'DocumentoFinanciero_idDocumentoFinanciero' => $documentofinanciero->idDocumentoFinanciero,
                    'Compra_idCompra' => $request['Compra_idCompra'][$i],
                    'numeroCompraDocumentoFinancieroDetalle' => $request['numeroCompraDocumentoFinancieroDetalle'][$i],
                    'Factura_idFactura' => $request['Factura_idFactura'][$i],
                    'numeroFacturaDocumentoFinancieroDetalle' => $request['numeroFacturaDocumentoFinancieroDetalle'][$i],
                    'valorFobDocumentoFinancieroDetalle' => $request['valorFobDocumentoFinancieroDetalle'][$i],
                    'valorPagoDocumentoFinancieroDetalle' => $request['valorPagoDocumentoFinancieroDetalle'][$i],
                ]);

                actualizarCartera('carga','pago',$request['Compra_idCompra'][$i], '', $request['fechaNegociacionDocumentoFinanciero'], $request['valorPagoDocumentoFinancieroDetalle'][$i]);
            }

            for ($i=0; $i < count($request['fechaProrrogaDocumentoFinancieroProrroga']); $i++) 
            { 
                \App\DocumentoFinancieroProrroga::create([
                    'DocumentoFinanciero_idDocumentoFinanciero' => $documentofinanciero->idDocumentoFinanciero,
                    'fechaProrrogaDocumentoFinancieroProrroga' => $request['fechaProrrogaDocumentoFinancieroProrroga'][$i],
                    'observacionDocumentoFinancieroProrroga' => $request['observacionDocumentoFinancieroProrroga'][$i]
                ]);
            }

            $listaf = \App\ListaFinanciacion::find($request['ListaFinanciacion_idListaFinanciacion']);

            if ($listaf->tipoListaFinanciacion != 'RecursoPropio') 
            {
                actualizarCartera('carga','documentofinanciero','', $documentofinanciero->idDocumentoFinanciero, $request['fechaNegociacionDocumentoFinanciero'], $request['totalProgramadoDocumentoFinanciero']);
            }
        }

        return redirect('/documentofinanciero');
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
        $listafinanciacion = \App\ListaFinanciacion::All()->lists('nombreListaFinanciacion','idListaFinanciacion');
        $documentofinanciero = \App\DocumentoFinanciero::find($id);
        return view('documentofinanciero',compact('listafinanciacion'), ['documentofinanciero' => $documentofinanciero]);
    }

    /** 
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DocumentoFinancieroRequest $request, $id)
    {
        // Antes de guardar los datos modificados, descargamos los datos originales de la cartera
        if($request['respuesta'] != 'falso')
        {
            $documentoF = DB::Select('SELECT * from documentofinancierodetalle where DocumentoFinanciero_idDocumentoFinanciero = '.$id);

            // // recorremos el detalle depago original descargandolos de la cartera
            $total = 0;
            for ($i=0; $i < count($documentoF); $i++) 
            { 
                // convierto array a string
                $documento = get_object_vars($documentoF[$i]);

                actualizarCartera('descarga','pago',$documento['Compra_idCompra'], '', $request['fechaNegociacionDocumentoFinanciero'], $documento['valorPagoDocumentoFinancieroDetalle']);

                $total += $documento['valorPagoDocumentoFinancieroDetalle'];
            }

            $listaf = \App\ListaFinanciacion::find($request['ListaFinanciacion_idListaFinanciacion']);

            if ($listaf->tipoListaFinanciacion != 'RecursoPropio') 
            {
                actualizarCartera('descarga','documentofinanciero', '', $id, $request['fechaNegociacionDocumentoFinanciero'], $total);
            }

            $documentofinanciero = \App\DocumentoFinanciero::find($id);
            $documentofinanciero->fill($request->all());
            $documentofinanciero->save();

            if ($listaf->tipoListaFinanciacion != 'RecursoPropio') 
            {
                actualizarCartera('carga','documentofinanciero','', $id, $request['fechaNegociacionDocumentoFinanciero'], $request['totalProgramadoDocumentoFinanciero']);
            }

            $idsEliminar = explode(',', $request['eliminarDocumentoFinanciero']);
            \App\DocumentoFinancieroDetalle::whereIn('idDocumentoFinancieroDetalle',$idsEliminar)->delete();
            for($i = 0; $i < count($request['numeroCompraDocumentoFinancieroDetalle']); $i++)
            {
                $indice = array(
                    'idDocumentoFinancieroDetalle' => $request['idDocumentoFinancieroDetalle'][$i]);

                $datos= array(
                    'DocumentoFinanciero_idDocumentoFinanciero' => $id,
                    'Compra_idCompra' => $request['Compra_idCompra'][$i],
                    'numeroCompraDocumentoFinancieroDetalle' => $request['numeroCompraDocumentoFinancieroDetalle'][$i],
                    'Factura_idFactura' => $request['Factura_idFactura'][$i],
                    'numeroFacturaDocumentoFinancieroDetalle' => $request['numeroFacturaDocumentoFinancieroDetalle'][$i],
                    'valorFobDocumentoFinancieroDetalle' => $request['valorFobDocumentoFinancieroDetalle'][$i],
                    'valorPagoDocumentoFinancieroDetalle' => $request['valorPagoDocumentoFinancieroDetalle'][$i]
                    );

                $guardar = \App\DocumentoFinancieroDetalle::updateOrCreate($indice, $datos);

                actualizarCartera('carga','pago',$request['Compra_idCompra'][$i], '', $request['fechaNegociacionDocumentoFinanciero'], $request['valorPagoDocumentoFinancieroDetalle'][$i]);
            }

            $idsEliminarProrroga = explode(',', $request['eliminarDocumentoFinancieroProrroga']);
            \App\DocumentoFinancieroProrroga::whereIn('idDocumentoFinancieroProrroga',$idsEliminarProrroga)->delete();
            for($i = 0; $i < count($request['fechaProrrogaDocumentoFinancieroProrroga']); $i++)
            {
                $indice = array(
                    'idDocumentoFinancieroProrroga' => $request['idDocumentoFinancieroProrroga'][$i]);

                $datos= array(
                    'DocumentoFinanciero_idDocumentoFinanciero' => $id,
                    'fechaProrrogaDocumentoFinancieroProrroga' => $request['fechaProrrogaDocumentoFinancieroProrroga'][$i],
                    'observacionDocumentoFinancieroProrroga' => $request['observacionDocumentoFinancieroProrroga'][$i]
                    );

                $guardar = \App\DocumentoFinancieroProrroga::updateOrCreate($indice, $datos);
            }
        }

        return redirect('/documentofinanciero');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        if ($listaf->tipoListaFinanciacion != 'RecursoPropio') 
        {
            actualizarCartera('descarga','documentofinanciero','', $id, $request['fechaNegociacionDocumentoFinanciero'], $request['totalProgramadoDocumentoFinanciero']);
        }

        \App\DocumentoFinanciero::destroy($id);
        return redirect('/documentofinanciero');
    }
}
