@extends('layouts.formato')

<title>Consulta de produccion</title>
@section('contenido')

{!!Form::model($datosproduccion)!!}
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
       for ($i = 0, $c = count($datosproduccion); $i < $c; ++$i) 
       {
          $camposencabezado[$i] = (array) $datosproduccion[$i];
       }

       $centrocantidad = array();
  	// por facilidad de manejo convierto el stdclass a tipo array con un cast (array)
       for ($i = 0, $c = count($centrocantidadop); $i < $c; ++$i) 
       {
          $centrocantidad[$i] = (array) $centrocantidadop[$i];
       }
?>
<div>
	<!-- IMPRIMO EL ENCABEZADO DEL INFORME DE PRODUCCION -->
	  <div class="col-md-12" style="border:1px; background-color:#CEECF5;">
	    <div class="col-md-1" style="top:15px"> <?php echo $img ?> </div>
	    <div class="col-md-11"><center><h1>ORDEN DE PRODUCCIÓN</h1></center></div>
	  </div>

	  </br> </br> </br>

	<!-- IMPRIMO LOS DATOS GENERALES DEL INFORME DE PRODUCCION DENTRO DE UN PANEL -->
	  <div class="list-group" style="border:1px;">
		<div class="panel panel-primary">
		  <div class="panel-heading" style="height:45px;"><h4>Datos generales</h4></div>
		  <div class="panel-body">
		  	<?php 
		  	echo '
		  	<div>
		  		<div style="width:200px; display:inline-block;"><b>ORDEN DE PRODUCCIÓN No:</b> </div>
		  		<div style="width:400px; display:inline-block; color:red;"><b>'.$camposencabezado[0]['numeroOrdenProduccion'].'</b></div>
		  	</div>

		  	<div>
		  		<div style="width:200px; display:inline-block;"><b>DOCUMENTO BASE No:</b> </div>
		  		<div style="width:400px; display:inline-block;"><b>'.$camposencabezado[0]['documentoReferenciaOrdenProduccion'].'</b></div>
		  	</div>

		  	<div>
		  		<div style="width:200px; display:inline-block;"><b>Fecha de Elaboración:</b> </div>
		  		<div style="width:400px; display:inline-block;">'.$camposencabezado[0]['fechaElaboracionOrdenProduccion'].'</div>

		  		<div style="width:200px; display:inline-block;"><b>Fecha Estimada de Entrega:</b> </div>
		  		<div style="width:400px; display:inline-block;">'.$camposencabezado[0]['fechaEstimadaEntregaOrdenProduccion'].'</div>
		  	</div>

		  	</br> 

		  	<div>
		  		<div style="width:200px; display:inline-block;"><b>Tercero Cliente / Proveedor:</b> </div>
		  		<div style="width:400px; display:inline-block;">'.$camposencabezado[0]['nombre1Tercero'].'</div>

		  		<div style="width:200px; display:inline-block;"><b>Nit o C.C:</b> </div>
		  		<div style="width:400px; display:inline-block;">'.$camposencabezado[0]['documentoTercero'].'</div>
		  	</div>

		  	<div>
		  		<div style="width:200px; display:inline-block;"><b>Dirección:</b> </div>
		  		<div style="width:400px; display:inline-block;">'.$camposencabezado[0]['direccionTercero'].'</div>

		  		<div style="width:200px; display:inline-block;"><b>Teléfono:</b> </div>
		  		<div style="width:400px; display:inline-block;">'.$camposencabezado[0]['telefono1Tercero'].'</div>
		  	</div>

		  	<div>
		  		<div style="width:200px; display:inline-block;"><b>Colección:</b> </div>
		  		<div style="width:400px; display:inline-block;">'.$camposencabezado[0]['nombreTemporada'].'</div>

		  		<div style="width:200px; display:inline-block;"><b>Liquidación de corte:</b> </div>
		  		<div style="width:400px; display:inline-block;">'.$camposencabezado[0]['numeroLiquidacionCorteOrdenProduccion'].'</div>
		  	</div>

		  	<div>
		  		<div style="width:200px; display:inline-block;"><b>Responsable:</b> </div>
		  		<div style="width:400px; display:inline-block;"><b>'.$camposencabezado[0]['responsableOrdenProduccion'].'</b></div>		  	
		  	</div>';
		  	
			?>
		  </div>
		</div>
  	</div>

  	<!-- IMPRIMO LA INFORMACION DEL PRODUCTO DE LA ORDEN DE PRODUCCION DENTRO DE UN PANEL -->
  	<div class="list-group" style="border:1px;">
		<div class="panel panel-primary">
		  <div class="panel-heading" style="height:45px;"><h4>Información del Producto</h4></div>
		  <div class="panel-body">
			<table style="border-top: 1px solid #000;" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="font-weight:bold;" width="193">Referencia Interna</td>
                    <td style="font-size: 14px; font-weight:bold; color: RED;" width="207"><?php echo ($camposencabezado[0]["referenciaBaseFichaTecnica"] == NULL ? $camposencabezado[0]["codigoAlternoProducto"] : $camposencabezado[0]["referenciaBaseFichaTecnica"]) ; ?></td>
                    <td style="font-weight:bold;" width="107">Descripcion</td>
                    <td  colspan="5"><?php echo $camposencabezado[0]["nombreLargoProducto"]; ?></td>
                  </tr>
                  <tr>
                    <td style="font-weight:bold;" ><p>Composicion</p>
                    </td>
                    <td  ><?php echo $camposencabezado[0]["nombreComposicion"]; ?></td>
                    <td style="font-weight:bold;" >Marca</td>
                    <td  width="192"><?php echo $camposencabezado[0]["nombreMarca"]; ?></td>
                    <td style="font-weight:bold;" width="78">Molde</td>
                    <td  width="217"><?php echo $camposencabezado[0]["numeroMoldeFichaTecnica"]; ?></td>
                  </tr>

            <?php       
            echo '<table style="border-bottom: 1px solid #000;" width="100%" cellpadding="0" cellspacing="0">
	                      <tr>
	                        <td style="font-weight:bold;" width="200">COLOR/TALLA</td>';
				$totTalla = array();
	            for($i = 0; $i < count($tallas); $i++)
	            {
	            	$Ntallas = get_object_vars($tallas[$i]);
	                echo '<td style="font-weight:bold;" width="100" colspan="2">'.$Ntallas["codigoAlternoTalla"].'</td>';
	                $totTalla[$i] = 0;
	            }
	            echo '<td style="font-weight:bold;">TOTAL</td></tr>';                        
		  
		  	$s = 0;
		  	
		  	$granTotal = 0;
      		$datos = count($datosproduccion);

      		while ($s < $datos)
			{
            	$colorAnt = $camposencabezado[$s]["nombre1Color"];
            	$totColor = 0;
	      		while ($s < $datos and $colorAnt == $camposencabezado[$s]["nombre1Color"]) 
	      		{
	      			echo '<td style="font-size: 14px; font-weight:bold; "  height="25" width="200">'.$camposencabezado[$s]["nombre1Color"].'</td>';
	      			
	                for($j = 0; $j < count($tallas); $j++)
	                {
	                	$Ntallas = get_object_vars($tallas[$j]);
	                    echo '<td style="font-size: 14px; font-weight:bold; color: RED;">'.$camposencabezado[$s]['T_'.($Ntallas["idTalla"] == '' ? 0: $Ntallas["idTalla"])].'</td><td>&nbsp;</td>';
	                    $totColor += $camposencabezado[$s]['T_'.($Ntallas["idTalla"] == '' ? 0: $Ntallas["idTalla"])];
	                    $totTalla[$j] += $camposencabezado[$s]['T_'.($Ntallas["idTalla"] == '' ? 0: $Ntallas["idTalla"])];
	                    $granTotal += $camposencabezado[$s]['T_'.($Ntallas["idTalla"] == '' ? 0: $Ntallas["idTalla"])];
	                }

	                echo '<td style="font-size: 14px; font-weight:bold; color: RED;" width="100">'.$totColor.'</td></tr>';
	               $s++;
	      		}
	      		
	      		
	      	}
	      	echo '<td style="font-size: 14px; font-weight:bold; "  height="25" width="200">TOTALES POR TALLA </td>';

            
            for($i = 0; $i < count($tallas); $i++)
            {
                
                echo '<td style="font-size: 14px; font-weight:bold;">'. $totTalla[$i].'</td><td  >&nbsp;</td>';
            }
            echo '<td style="font-size: 14px; font-weight:bold;">'.$granTotal.'</td></tr>';
      		echo'</table>';
	      	echo '</table>';
		  ?>
		  </div>
		</div>
  	</div>

  	<!-- IMPRIMO CENTRO DE PRODUCCIÓN Y CANTIDADES DE LA ORDEN DE PRODUCCION DENTRO DE UN PANEL -->
  	<div class="list-group" style="border:1px;">
		<div class="panel panel-primary">
		  <div class="panel-heading" style="height:45px;"><h4>Estado OP</h4></div>
		  <div class="panel-body">
		  <?php 
		  echo '
		  	<div>
		  		<div style="width:200px; display:inline-block;"><b>Centro de producción:</b> </div>
		  		<div style="width:400px; display:inline-block; color:red;"><b>'.$centrocantidad[0]['nombreCentroProduccion'].'</b></div>
		  	</div>

		  	<div>
		  		<div style="width:200px; display:inline-block;"><b>Cantidad remisionada:</b> </div>
		  		<div style="width:400px; display:inline-block;">'.$centrocantidad[0]['cantidadRemision'].'</div>

		  		<div style="width:200px; display:inline-block;"><b>Cantidad recibida:</b> </div>
		  		<div style="width:400px; display:inline-block;">'.$centrocantidad[0]['cantidadRecibo'].'</div>
		  	</div>';

		  ?>
		  </div>
		</div>
  	</div>

  	<!-- IMPRIMO LA EXPLOSIÓN DE MATERIALES DE LA ORDEN DE PRODUCCION DENTRO DE UN PANEL -->
  	<div class="list-group" style="border:1px;">
		<div class="panel panel-primary">
		  <div class="panel-heading" style="height:45px;"><h4>Explosión de Materiales</h4></div>
		  <div class="panel-body">
		  <?php 
		  	$datos = array();
		  	// por facilidad de manejo convierto el stdclass a tipo array con un cast (array)
		       for ($i = 0, $c = count($explosionmateriales); $i < $c; ++$i) 
		       {
		          $datos[$i] = (array) $explosionmateriales[$i];
		       }

		  		echo '<table  class="table table-striped table-bordered table-hover">';
		  		$i = 0;
		  		$total = count($explosionmateriales);

		  		while ($i < $total) 
		  		{
		  			$centro = $datos[$i]['nombreCentroProduccion'];

		  			echo '
		  			<thead class="thead-inverse">
					  	<tr class="table-info">
							<th colspan="20" style=" background-color:#255986; color:white;">'.$datos[$i]['nombreCentroProduccion'].'</th>
						</tr>
						<tr class="table-info">
							<th>Referencia</th>
							<th>Descripción</th>
							<th>Consumo/ Unit</th>
							<th>Cantidad</th>
						</tr>
					</thead>';

					while ($i < $total and $centro == $datos[$i]["nombreCentroProduccion"])
					{
						echo 
						'<tbody>
							<td>'.$datos[$i]["referenciaProducto"].'</td>
							<td>'.$datos[$i]["nombreLargoProducto"].'</td>
							<td style="text-align:right;">'.number_format($datos[$i]["consumoUnitarioOrdenProduccionMaterial"],2,".",",").'</td>
							<td style="text-align:right;">'.number_format($datos[$i]["cantidadBomOrdenProduccionMaterial"],2,".",",").'</td>
						</tbody>';

						$i++;
					}					
		  		}
		  		echo '</table>';
		  	?>
		  </div>
		</div>
  	</div>

  	<!-- IMPRIMO LA OBSERVACIÓN DE LA ORDEN DE PRODUCCION DENTRO DE UN PANEL -->
  	<div class="list-group" style="border:1px;">
		<div class="panel panel-primary">
		  <div class="panel-heading" style="height:45px;"><h4>Observaciones</h4></div>
		  <div class="panel-body">
		  <?php 
		  echo '
		  	<div>
		  		<div style="width:1320px; display:inline-block;">'.$camposencabezado[0]['observacionOrdenProduccion'].'</div>
		  	</div>';

		  ?>
		  </div>
		</div>
  	</div>

</div>
<?php 
	echo 
	'<iframe style="width:100%; height:100%;" src="http://'.$_SERVER["HTTP_HOST"].'/kiosko/'.$camposencabezado[0]["referenciaBaseFichaTecnica"] .'?referencia='.$camposencabezado[0]["referenciaBaseFichaTecnica"] .'&modulo=todo&formato=FichaTecnica"></iframe>'
?>

{!!Form::close()!!}
@stop