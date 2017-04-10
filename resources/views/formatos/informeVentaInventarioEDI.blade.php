@extends('layouts.formato')

<title>Ventas e inventario EDI</title>
@section('contenido')

{!!Form::model($consulta)!!}
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
       $logo =  '<img src="data:image/png;base64,' . $base64 .'" alt="Texto alternativo" width="200px"/>';
    }
    return $logo;
}    
    $img = base64('imagenes/Iblu.png');
  
?>
<div>
		<!-- IMPRIMO LA RUTA DE PROCESOS DEL INFORME DE FICHA TECNICA DENTRO DE UN PANEL -->
  	<div class="panel panel-primary">
		<div class="panel-heading" style="height:45px;"><h4>Ruta de procesos</h4></div>
		  <div class="panel-body">
		  <?php 
		  	echo '
		  	<table class="table table-striped">
				<thead>
					<tr>
						<th>Marca</th>
						<th>Tipo Producto</th>
						<th>Linea</th>
						<th>Sublinea</th>
						<th>Categoria</th>
						<th>Esquema</th>
						<th>Tipo Negocio</th>
						<th>Temporada</th>
						<th>Referencia</th>
						<th>Descripcion</th>
						<th>Cant Venta</th>
						<th>Valor Venta</th>
						<th>Cant Inventario</th>
						<th>Valor Inventario</th>
					</tr>
				</thead>
				<tbody>
				';
//						<th>Periodo</th>

		  	for ($i=0; $i < count($consulta); $i++) 
		  	{ 
		  		$datosconsulta = get_object_vars($consulta[$i]);

		  		echo '
		  		<tr>
			      <td>'.$datosconsulta['nombreMarca'].'</td>
			      <td>'.$datosconsulta['nombreTipoProducto'].'</td>
			      <td>'.$datosconsulta['nombreLinea'].'</td>
			      <td>'.$datosconsulta['nombreSublinea'].'</td>
			      <td>'.$datosconsulta['nombreCategoria'].'</td>
			      <td>'.$datosconsulta['nombreEsquema'].'</td>
			      <td>'.$datosconsulta['nombreTipoNegocio'].'</td>
			      <td>'.$datosconsulta['nombreTemporada'].'</td>
			      <td>'.$datosconsulta['codigoAlternoProducto'].'</td>
			      <td>'.$datosconsulta['nombreLargoProducto'].'</td>
			      <td>'.$datosconsulta['cantidadVenta'].'</td>
			      <td>'.$datosconsulta['precio1Venta'].'</td>
			      <td>'.$datosconsulta['cantidadInventario'].'</td>
			      <td>'.$datosconsulta['precio1Inventario'].'</td>
			    </tr>
		  		';

//		  					      <td>'.$datosconsulta['fechaVentaEDI'].'</td>

		  	}
		  	
		  	echo '
		  	</tbody>
			</table>';
		  ?>
		  </div>
		</div>
  	</div>

</div>

  		
{!!Form::close()!!}
@stop