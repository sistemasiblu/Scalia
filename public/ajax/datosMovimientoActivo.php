<?php
    $modificar = $_GET['modificar'];
    $eliminar = $_GET['eliminar'];
    $consultar = $_GET['consultar'];
    $aprobar = $_GET['aprobar'];



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

    if ($consultar == 1) 
        $visibleC = 'inline-block;';
    else
        $visibleC = 'none;';

    if ($aprobar == 1) 
        $visibleA = 'inline-block;';
    else
        $visibleA = 'none;';



    $id = isset($_GET["idTransaccionActivo"])
                ? $_GET["idTransaccionActivo"] 
                : 0;

     //return;           

    $TipoEstado = isset($_GET["TipoEstado"])
                ? $_GET["TipoEstado"] 
                : '';

    $campos = DB::select(
    'SELECT codigoTransaccionActivo, nombreTransaccionActivo, nombreCampoTransaccion,descripcionCampoTransaccion, 
            gridTransaccionActivoCampo, relacionTablaCampoTransaccion, relacionNombreCampoTransaccion, relacionAliasCampoTransaccion
    FROM transaccionactivo
    left join transaccionactivocampo
    on transaccionactivo.idTransaccionActivo = transaccionactivocampo.TransaccionActivo_idTransaccionActivo
    left join campotransaccion
    on transaccionactivocampo.CampoTransaccion_idCampoTransaccion = campotransaccion.idCampoTransaccion
    where   transaccionactivo.idTransaccionActivo = '.$id.' and
            relacionTablaCampoTransaccion != "" and
            gridTransaccionActivoCampo = 1');


/*$camposGrid = 'idMovimientoActivo, numeroMovimientoActivo, fechaElaboracionMovimientoActivo, nombre1Tercero';
$camposBase = 'idMovimientoActivo,numeroMovimientoActivo,fechaElaboracionMovimientoActivo, nombre1Tercero';*/


$camposGrid = 'idMovimientoActivo, numeroMovimientoActivo, fechaElaboracionMovimientoActivo, fechaInicioMovimientoActivo, fechaFinMovimientoActivo, Tercero_idTercero, estadoMovimientoActivo, Users_idCrea, Users_idCambioEstado';
$camposBase = 'idMovimientoActivo,numeroMovimientoActivo,fechaElaboracionMovimientoActivo, Tercero_idTercero';
for($i = 0; $i < count($campos); $i++)
{
    $datos = get_object_vars($campos[$i]); 
    
    $camposGrid .= ', '. $datos["relacionTablaCampoTransaccion"].'.'.$datos["relacionNombreCampoTransaccion"]  .
                     ($datos["relacionAliasCampoTransaccion"] == null 
                        ? ''
                        : ' As '. $datos["relacionAliasCampoTransaccion"]);

    $camposBase .= ','.($datos["relacionAliasCampoTransaccion"] == null 
                        ? $datos["relacionNombreCampoTransaccion"]
                        : $datos["relacionAliasCampoTransaccion"]);

}
    $compania=\Session::get('nombreCompania');
    $movimientoactivo = DB::select(
        'Select
          '.$camposGrid.'
        From
          movimientoactivo
          left join transaccionactivo
          On movimientoactivo.TransaccionActivo_idTransaccionActivo = transaccionactivo.idTransaccionActivo
          Left Join '.$compania.'.Tercero as tercero
            On movimientoactivo.Tercero_idTercero = tercero.idTercero 
                  Where   idTransaccionActivo = '.$id. 
                ($TipoEstado != '' ? ' and movimientoactivo.estadoMovimientoActivo = "'.$TipoEstado.'"' : ''));


 

    $row = array();

    for($i = 0; $i < count($movimientoactivo); $i++)
    {  
        $datoValor = get_object_vars($movimientoactivo[$i]); 
        $row[$i][] = '<a href="movimientoactivo/'.$datoValor["idMovimientoActivo"].'/edit?idTransaccionActivo='.$id.'&aprobador='.$aprobar.'">'.
                            '<span class="glyphicon glyphicon-pencil" style = "display:'.$visibleM.'"></span>'.
                        '</a>&nbsp;'.
                        '<a href="movimientoactivo/'.$datoValor["idMovimientoActivo"].'/edit?idTransaccionActivo='.$id.'&aprobador='.$aprobar.'&accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash" style = "display:'.$visibleE.'"></span>'.
                        '</a>&nbsp;'.
                        '<a  onclick="abrirAprobacionActivo('.$datoValor["idMovimientoActivo"].');">'.
                            '<span class="glyphicon glyphicon-check" style = "cursor:pointer;display:'.$visibleA.'" ></span>'.
                        '</a>&nbsp;'.
                        '<a  onclick="imprimirFormato('.$datoValor["idMovimientoActivo"].','.$id.');">'.
                            '<span class="glyphicon glyphicon-print" style = "cursor:pointer;display:'.$visibleC.'" ></span>'.
                        '</a>';
                        

        $campos = explode(',', $camposBase);
        for($j = 0; $j < count($campos); $j++)
        {
          // if(trim($campos[$j]) == 'asuntoMovimientoCRM')
          //     $row[$i][] = '<p title="'.$datoValor['detallesMovimientoCRM'].'">'.$datoValor[trim($campos[$j])].'</p>';
          // else
              $row[$i][] = $datoValor[trim($campos[$j])];
          
        }

    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>