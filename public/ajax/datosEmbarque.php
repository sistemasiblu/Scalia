<?php
    
    $idDocumento = $_GET['idDocumento'];

    $modificar = $_GET['modificar'];
    $eliminar = $_GET['eliminar'];
    $imprimir = $_GET['imprimir'];

    $visibleM = '';
    $visibleE = '';
    $visibleI = '';
    if ($modificar == 1) 
        $visibleM = 'inline-block;';
    else
        $visibleM = 'none;';

    if ($eliminar == 1) 
        $visibleE = 'inline-block;';
    else
        $visibleE = 'none;';

    if ($imprimir == 1) 
        $visibleI = 'inline-block;';
    else
        $visibleI = 'none;';
    

    $embarque = \App\Embarque::Where('DocumentoImportacion_idDocumentoImportacion',"=",$idDocumento)->get();
    // print_r($Embarque);
    // exit;
    $row = array();

    foreach ($embarque as $key => $value) 
    {  
        $row[$key][] = '<a href="embarque/'.$value['idEmbarque'].'/edit?idDocumento='.$idDocumento.'&accion=modificar">'.
                            '<span class="glyphicon glyphicon-pencil" style="display: '.$visibleM.'"></span>'.
                        '</a>&nbsp;'.
                        '<a href="embarque/'.$value['idEmbarque'].'/edit?accion=eliminar&idDocumento='.$idDocumento.'">'.
                            '<span class="glyphicon glyphicon-trash" style="display: '.$visibleE.'"></span>'.
                        '</a>&nbsp;'.
                        '<a href="#" onclick="imprimirFormato('.$value["idEmbarque"].','.$idDocumento.',\'embarque\')">'.
                            '<span class="glyphicon glyphicon-print" style="display: '.$visibleI.'"></span>'.
                        '</a>';
        $row[$key][] = $value['idEmbarque'];
        $row[$key][] = $value['numeroEmbarque'];
        $row[$key][] = $value['fechaElaboracionEmbarque'];
        $row[$key][] = $value['tipoTransporteEmbarque'];   
        $row[$key][] = $value['agenteCargaEmbarque'];   
        $row[$key][] = $value['navieraEmbarque'];   
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>