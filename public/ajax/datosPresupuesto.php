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

$presupuesto = DB::Select('SELECT * from presupuesto p left join documentocrm dcrm on p.DocumentoCRM_idDocumentoCRM = dcrm.idDocumentoCRM');

$row = array();

    foreach ($presupuesto as $key => $value) 
    {  
        $row[$key][] = '<a href="presupuesto/'.$value->idPresupuesto.'/edit">'.
                            '<span class="glyphicon glyphicon-pencil" style = "display:'.$visibleM.'"></span>'.
                        '</a>&nbsp;'.
                        '<a href="presupuesto/'.$value->idPresupuesto.'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash" style = "display:'.$visibleE.'"></span>'.
                        '</a>';

        $row[$key][] = $value->idPresupuesto;
        $row[$key][] = $value->fechaInicialPresupuesto;
        $row[$key][] = $value->fechaFinalPresupuesto; 
        $row[$key][] = $value->nombreDocumentoCRM;    
    }

$output['aaData'] = $row;
echo json_encode($output);

?>