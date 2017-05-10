@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center></center></h3>@stop

@section('content')
@include('alerts.request')

{!!Html::style('css/kiosko.css')!!}
{!! Html::style('css/segmented-controls.css'); !!}
{!!Html::script('js/kiosko.js')!!}

<?php
	$meses = array('Todos', 'Enero','Febrero','Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

	$año = array();

	$año[date('Y')] = date('Y');
	$año[date("Y", strtotime("-1 YEAR", strtotime(date("Y"))))] = date("Y", strtotime("-1 YEAR", strtotime(date("Y"))));
?>

<div id='form-section' >
	<fieldset id="kiosko-form-fieldset"> 

		<!-- CONTENEDOR PRINCIPAL DE LA PANTALLA -->
		<div id="contenedor-kiosko">

			<!-- CONTENEDOR INTERNO - PADRE DE LOS BOTONES -->
			<div id="kiosko-div" class="col-md-12 col-sm-12 col-lg-12">

				<!-- <div id="kiosko-produccion" class="col-md-12 col-sm-12 col-lg-12"> -->

					<a onclick="abrirModalCertificadoLaboral();">
						<div class="col-md-5 col-sm-5 col-lg-5 button btn btn-primary">
							<span class="span-button"></span>
							<img class="img" src="imagenes/kiosko/Certificado_Laboral.png">
						</div>
					</a>

					<a onclick="abrirModalReciboPago();">
						<div class="col-md-5 col-sm-5 col-lg-5 button btn btn-primary">
							<span class="span-button"></span>
							<img class="img" src="imagenes/kiosko/Recibo_Pago.png">
						</div>
					</a>

					<input type="hidden" name="documentoUsuario" id="documentoUsuario" value="">
				<!-- </div> -->

				<div id="div-button-back">

					<a onclick="volverAtras();">
						<div class="col-md-3 col-sm-3 col-lg-3 btn btn-primary button-back-left" style="">
							<span class="span-button-back"></span>
							<img class="img" src="imagenes/kiosko/Back.png">
						</div>
					</a>

					<a href="<?php echo 'http://'.$_SERVER["HTTP_HOST"].'/kiosko'?>">
						<div class="col-md-3 col-sm-3 col-lg-3 btn btn-primary button-back-right" style="">
							<span class="span-button-back"></span>
							<img class="img" src="imagenes/kiosko/Home.png">
						</div>
					</a>
					
				</div>

			</div> <!-- CIERRE DE DIV CONTENEDOR INTERNO -->

		</div> <!-- CIERRE CONTENEDOR PRINCIPAL DE LA PANTALLA -->

	</fieldset>
</div>


<script>
	$(document).ready(function(){
		var doc = prompt("Digite su número de cédula por favor.", "");
	    if (doc != null) 
	    {
	        $("#documentoUsuario").val(doc);
	    }

		$("#fechaNacimientoTercero").datetimepicker
		(({
	       format: "YYYY-MM-DD"
	     })
		);

	});
</script>

@stop
<!-- ABRO EL MODAL Y DENTRO DE EL ESTAN LOS FILTROS PARA EL CERTIFICADO LABORAL -->
<div id="modalFiltroCertificado" class="modal fade col-md-12 col-sm-12 col-lg-12" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Certificado Laboral</h4>
      </div>
        <div class="modal-body">

        	<div class="form-group col-md-12 col-sm-12 col-lg-12" id='test'>
	        	{!!Form::label('fechaNacimientoTercero', 'Fecha de nacimiento', array('class' => 'col-sm-2 col-sm-2 col-lg-2 control-label')) !!}
		        <div class="col-sm-10">
		          <div class="input-group">
		            <span class="input-group-addon">
		              <i class="fa fa-calendar"></i>
		            </span>
		            {!!Form::text('fechaNacimientoTercero',null,['class'=>'form-control','placeholder'=>'Ingrese su fecha de nacimiento'])!!}
		          </div>
		        </div>
	      	</div>

	      	<div class="form-group col-md-12 col-sm-12 col-lg-12" id='test'>
	        	{!!Form::label('destinatarioCertificadoLaboral', 'Destinatario', array('class' => 'col-sm-2 col-sm-2 col-lg-2 control-label')) !!}
		        <div class="col-sm-10">
		          <div class="input-group">
		            <span class="input-group-addon">
		              <i class="fa fa-bank"></i>
		            </span>
		            {!!Form::text('destinatarioCertificadoLaboral',null,['class'=>'form-control','placeholder'=>'Digite el nombre del destinatario'])!!}
		          </div>
		        </div>
	      	</div>

	    </div>
      <div class="modal-footer">
      	<button type="button" class="btn btn-primary" onclick="generarCertificadoLaboral('certificado');">Generar</button>
      </div>
    </div>
  </div>
</div>

<!-- ABRO EL MODAL Y DENTRO DE EL ESTAN LOS FILTROS PARA EL RECIBO DE PAGO -->
<div id="modalFiltroRecibo" class="modal fade col-md-12 col-sm-12 col-lg-12" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Quincenas a generar</h4>
      </div>
        <div class="modal-body">

        	<?php

        	$fechaInicial = date('Y-m-d');
        	$fechaFinal = date('Y-m-d',strtotime("-1 MONTH", strtotime($fechaInicial)));

        	$Q2 = DB::Select('
        		SELECT 
					nombreLiquidacionNomina, 
					fechaInicioLiquidacionNomina, 
					fechaFinLiquidacionNomina 
				FROM 
					Iblu.LiquidacionNomina 
				WHERE estadoLiquidacionNomina = "DEFINITIVA" 
					AND fechaInicioLiquidacionNomina <= '.$fechaInicial.'
				    AND fechaFinLiquidacionNomina >= '.$fechaInicial);

	        	$disabled = '';
	        	if (empty($Q2)) 
	        		$disabled = '';
	        	else
	        		$disabled = '';


        	while($fechaInicial >= $fechaFinal)
        	{
        		$numMes = date('m',strtotime($fechaInicial));
        		setlocale(LC_TIME, 'spanish');
        		$mes = strftime("%B",mktime(0, 0, 0, $numMes, 1, 2000));
        		$año = date('Y',strtotime($fechaInicial));

        		echo'
        		<div id="checkgestionhumana">
		        	<div class="segmented-control" style="width: 45%; display: inline-block; color: #255986;" >
			            <input type="checkbox" '.$disabled.' name="Q1-'.$numMes.'-'.$año.'" id="Q1-'.$numMes.'-'.$año.'">
			            <label for="Q1-'.$numMes.'-'.$año.'" data-value="Q1 '.$mes.' '.$año.'">Q1 '.$mes.' '.$año.'</label>
		        	</div>';

		        	echo'
		        	<div class="segmented-control" style="width: 45%; display: inline-block; color: #255986;" >
			            <input type="checkbox" '.$disabled.' name="Q2-'.$numMes.'-'.$año.'" id="Q2-'.$numMes.'-'.$año.'">
			            <label for="Q2-'.$numMes.'-'.$año.'" data-value="Q2 '.$mes.' '.$año.'">Q2 '.$mes.' '.$año.'</label>
		        	</div>
		        </div>';

        		$fechaInicial = date("Y-m-d", strtotime("-1 MONTH", strtotime($fechaInicial)));

        	}

        	?>

	    </div>
      <div class="modal-footer">
      	<button type="button" class="btn btn-primary" onclick="generarReciboPago('recibo');">Generar</button>
      </div>
    </div>
  </div>
</div>
