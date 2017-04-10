@extends('layouts.formato')

<title>Movimiento</title>
@section('contenido')

{!!Form::model($datosmovimiento)!!}
<?php 
function base64($archivo)
{
  
    $logo = '&nbsp;';
    $fp = fopen($archivo,"r", 0);
    if($archivo != '' and $fp)
    {
       $imagen = fread($fp,filesize($archivo));
       fclose($fp);
       // devuelve datos cifrados en base64
       //  formatear $imagen usando la sem ntica del RFC 2045

       $base64 = chunk_split(base64_encode($imagen));
       $logo =  '<img src="data:image/png;base64,' . $base64 .'" alt="Texto alternativo" width="130px"/>';
    }
    return $logo;
}    
    $img = base64('imagenes/Logo_iblu.png');
  

    $camposencabezado = array();
  	// por facilidad de manejo convierto el stdclass a tipo array con un cast (array)
       for ($i = 0, $c = count($datosmovimiento); $i < $c; ++$i) 
       {
          $camposencabezado[$i] = (array) $datosmovimiento[$i];
       }
?>
<div>
		<!-- IMPRIMO EL ENCABEZADO DEL INFORME DE PEDIDO CLIENTE -->
  <div class="col-md-12" style="border:1px; background-color:#CEECF5;">
    <div class="col-md-1" style="top:15px"> <?php echo $img ?> </div>
    <div class="col-md-11"><center><h1>Pedido Cliente</h1></center></div>
  </div>

  </br> </br> </br>

		<!-- IMPRIMO LOS DATOS GENERALES DEL INFORME DE PEDIDO CLIENTEDENTRO DE UN PANEL -->
	  	<?php 
      $i = 0;
      $datos = count($datosmovimiento);
      
      while ($i < $datos) 
      {
        $totTalla = 0;
        $valorTotalTalla = 0;
        $valorTotal = 0;
        $granTotal = 0;
        $totalUnidades = 0;
        $totalColumna = array();
        $numeroMovimientoAnt = $camposencabezado[$i]['numeroMovimiento'];
        echo '
          <div class="list-group" style="border:1px;">
            <div class="panel panel-primary">
              <div class="panel-heading" style="height:45px;"><h4>Datos generales pedido cliente: '.$camposencabezado[$i]['numeroMovimiento'].'</h4></div>
              <div class="panel-body">
                <div>
                  <div style="width:200px; display:inline-block;"><b>PEDIDO CLIENTE No:</b> </div>
                  <div style="width:200px; display:inline-block; color:red;"><b>'.$camposencabezado[$i]['numeroMovimiento'].'</b></div>
                </div>

                <div>
                  <div style="width:200px; display:inline-block;"><b>Fecha de Elaboración:</b> </div>
                  <div style="width:400px; display:inline-block;">'.$camposencabezado[$i]['fechaElaboracionMovimiento'].'</div>

                  <div style="width:100px; display:inline-block;"><b>Fecha Inicio:</b> </div>
                  <div style="width:200px; display:inline-block;">'.$camposencabezado[$i]['fechaMinimaMovimiento'].'</div>

                  <div style="width:100px; display:inline-block;"><b>Fecha Final:</b> </div>
                  <div style="width:200px; display:inline-block;">'.$camposencabezado[$i]['fechaMaximaMovimiento'].'</div>
                </div>

                </br>

                <div>
                  <div style="width:200px; display:inline-block;"><b>Tercero Cliente / Proveedor:</b> </div>
                  <div style="width:400px; display:inline-block;">'.$camposencabezado[$i]['nombre1Tercero'].'</div>

                  <div style="width:200px; display:inline-block;"><b>Nit o C.C:</b> </div>
                  <div style="width:400px; display:inline-block;">'.$camposencabezado[$i]['documentoTercero'].'</div>
                </div>

                <div>
                  <div style="width:200px; display:inline-block;"><b>Dirección:</b> </div>
                  <div style="width:400px; display:inline-block;">'.$camposencabezado[$i]['direccionTercero'].'</div>

                  <div style="width:200px; display:inline-block;"><b>Teléfono:</b> </div>
                  <div style="width:400px; display:inline-block;">'.$camposencabezado[$i]['telefono1Tercero'].'</div>
                </div>

                <div>
                  <div style="width:200px; display:inline-block;"><b>Ciudad:</b> </div>
                  <div style="width:400px; display:inline-block;">'.$camposencabezado[$i]['nombreCiudad'].'</div>

                  <div style="width:200px; display:inline-block;"><b>Doc. Referencia:</b> </div>
                  <div style="width:400px; display:inline-block;">'.$camposencabezado[$i]['numeroReferenciaExternoMovimiento'].'</div>
                </div>

                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>Referencia</th>
                      <th>Descripción</th>
                      <th>Color</th>';
                      
                      for ($j=0; $j < count($tallas); $j++) 
                      { 
                        $Ntallas = get_object_vars($tallas[$j]);
                        echo '<th >'.$Ntallas["codigoAlternoTalla"].'</td>';
                        $totalColumna[] = 0;
                      }

                echo '
                      <th>Total unidades</th>
                      <th>Valor total</th>
                    </tr>
                  </thead>
                  <tbody>';

        while ($i < $datos and $numeroMovimientoAnt == $camposencabezado[$i]['numeroMovimiento'])
        {
          
          $colorAnt = $camposencabezado[$i]['nombre1Color'];
          while ( $i < $datos and $numeroMovimientoAnt == $camposencabezado[$i]['numeroMovimiento'] and $colorAnt == $camposencabezado[$i]['nombre1Color']) 
          {
            $codigoAnt = $camposencabezado[$i]['codigoAlternoProducto'];
            echo '<tr>
                    <td>'.$camposencabezado[$i]['codigoAlternoProducto'].'</td>
                    <td>'.$camposencabezado[$i]['nombreLargoProducto'].'</td>
                    <td>'.$camposencabezado[$i]['nombre1Color'].'</td>';

            while ($i < $datos and $numeroMovimientoAnt == $camposencabezado[$i]['numeroMovimiento'] and $colorAnt == $camposencabezado[$i]['nombre1Color'] and  $codigoAnt == $camposencabezado[$i]['codigoAlternoProducto']) 
            {
              $totTalla = 0;
              $valorTotalTalla = 0;

              for ($j=0; $j < count($tallas); $j++) 
              { 
                $Ntallas = get_object_vars($tallas[$j]);
                echo '<td style="text-align:right;">'.number_format($camposencabezado[$i]['T_'.($Ntallas["idTalla"] == '' ? 0: $Ntallas["idTalla"])],0,".",",").'</td>';
                $totTalla += $camposencabezado[$i]['T_'.($Ntallas["idTalla"] == '' ? 0: $Ntallas["idTalla"])];
                $totalColumna[$j] += $camposencabezado[$i]['T_'.($Ntallas["idTalla"] == '' ? 0: $Ntallas["idTalla"])];
                $valorTotalTalla += ($camposencabezado[$i]['T_'.($Ntallas["idTalla"] == '' ? 0: $Ntallas["idTalla"])]) * $camposencabezado[$i]['valorBrutoMovimientoDetalle'];
                $valorTotal += ($camposencabezado[$i]['T_'.($Ntallas["idTalla"] == '' ? 0: $Ntallas["idTalla"])]) * $camposencabezado[$i]['valorBrutoMovimientoDetalle'];

                $granTotal += ($camposencabezado[$i]['T_'.($Ntallas["idTalla"] == '' ? 0: $Ntallas["idTalla"])]) * $camposencabezado[$i]['valorBrutoMovimientoDetalle'];
                $totalUnidades += $camposencabezado[$i]['T_'.($Ntallas["idTalla"] == '' ? 0: $Ntallas["idTalla"])];
              }
              echo '<td style="font-weight:bold; text-align:right;">'.number_format($totTalla,0,".",",").'</td>';
              echo '<td style="font-weight:bold; text-align:right;">'.number_format($valorTotalTalla,0,".",",").'</td>';
              $i++;
            }
          }
        }
        echo '</tr>
              <tr>
                <th>TOTALES: </th>
                <td>&nbsp;</td>
                <td>&nbsp;</td>';
                for ($j=0; $j < count($tallas); $j++) 
                { 
                  echo'<td style="color:red; font-weight:bold; text-align:right;">'.number_format($totalColumna[$j],0,".",",").'</td>';
                }
          echo '<td style="font-weight:bold; text-align:right; font-size: 14px; color:red;">'.number_format($totalUnidades,0,".",",").'</td>
                <td style="font-weight:bold; text-align:right; font-size: 14px; color:red;">'.number_format($granTotal,0,".",",").'</td>
              </tr>    
                </tbody>
              </table> 
              </br>   
                <div>
                  <div style="width:200px; display:inline-block;"><b>TOTAL UNIDADES:</b> </div>
                  <div style="width:400px; display:inline-block; font-weight:bold; font-size: 18px; color:red;">'.number_format($totalUnidades,0,".",",").'</div>

                  <div style="width:200px; display:inline-block;"><b>VALOR TOTAL:</b> </div>
                  <div style="width:400px; display:inline-block; font-weight:bold; font-size: 18px; color:red;">'.number_format($valorTotal,0,".",",").'</div>
                </div>
                </br>
                <div>
                  <div style="width:200px; display:inline-block;"><b>Observación:</b> </div>
                  <div style="width:400px; display:inline-block;">'.$camposencabezado[0]['observacionMovimiento'].'</div>
                </div>
              </div>
            </div>
          </div>';
      }
    ?>

</div>

  		
{!!Form::close()!!}
@stop