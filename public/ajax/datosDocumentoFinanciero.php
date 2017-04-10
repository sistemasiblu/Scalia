<?php
    
    $modificar = $_GET['modificar'];
    $eliminar = $_GET['eliminar'];

    $visibleM = '';
    $visibleE = '';

    if ($modificar == 1) 
        $visibleM = 'inline-block;';
    else
        $visibleM = 'none;';

    if ($eliminar == 1) 
        $visibleE = 'inline-block;';
    else
        $visibleE = 'none;';

    $documentofinanciero = DB::table('documentofinanciero')
            ->leftJoin('listafinanciacion', 'documentofinanciero.ListaFinanciacion_idListaFinanciacion', '=', 'idListaFinanciacion')
            ->leftJoin('documentofinancieroprorroga', 'documentofinanciero.idDocumentoFinanciero', '=', 'documentofinancieroprorroga.DocumentoFinanciero_idDocumentoFinanciero')
            ->select(DB::raw('idDocumentoFinanciero, nombreListaFinanciacion, numeroDocumentoFinanciero, fechaNegociacionDocumentoFinanciero, fechaVencimientoDocumentoFinanciero, nombreEntidadDocumentoFinanciero, valorDocumentoFinanciero, max(fechaProrrogaDocumentoFinancieroProrroga) as fechaProrrogaDocumentoFinancieroProrroga'))
            ->groupBy('idDocumentoFinanciero')
            ->get();

   // print_r($documentofinanciero);
 // exit;
    $row = array();

    foreach ($documentofinanciero as $key => $value) 
    {  
        $row[$key][] = '<a href="documentofinanciero/'.$value->idDocumentoFinanciero.'/edit">'.
                            '<span class="glyphicon glyphicon-pencil" style="display: '.$visibleM.'"></span>'.
                        '</a>&nbsp;'.
                        '<a href="documentofinanciero/'.$value->idDocumentoFinanciero.'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash" style="display: '.$visibleE.'"></span>'.
                        '</a>';
        $row[$key][] = $value->idDocumentoFinanciero;
        $row[$key][] = $value->nombreListaFinanciacion;
        $row[$key][] = $value->numeroDocumentoFinanciero;   
        $row[$key][] = $value->fechaNegociacionDocumentoFinanciero;
        $row[$key][] = $value->fechaVencimientoDocumentoFinanciero;
        $row[$key][] = $value->nombreEntidadDocumentoFinanciero;
        $row[$key][] = $value->valorDocumentoFinanciero;
        $row[$key][] = $value->fechaProrrogaDocumentoFinancieroProrroga;
    }
     // print_r($row);
    // exit;
    $output['aaData'] = $row;
    echo json_encode($output);
?>