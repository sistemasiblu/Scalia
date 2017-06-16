@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center></center></h3>@stop

@section('content')
@include('alerts.request')

{!!Html::style('css/kiosko.css')!!}
{!!Html::script('js/kiosko.js')!!}

<div id='form-section' >

	<fieldset id="kiosko-form-fieldset"> 

		<!-- CONTENEDOR PRINCIPAL DE LA PANTALLA -->
		<div id="contenedor-kiosko">

			<!-- CONTENEDOR INTERNO - PADRE DE LOS BOTONES -->
			<div id="kiosko-div" class="col-md-12 col-sm-12 col-lg-12">

				<!-- <div id="kiosko-produccion" class="col-md-12 col-sm-12 col-lg-12"> -->

					<a href="<?php echo 'http://'.$_SERVER["HTTP_HOST"].'/kioskoproduccionfichatecnica'?>">
						<div class="col-md-3 col-sm-3 col-lg-3 button btn btn-primary">
							<span class="span-button"></span>
							<b>FICHA TÉCNICA</b>
							<img class="img" src="imagenes/kiosko/Ficha_Tecnica.png">
						</div>
					</a>

					<a href="<?php echo 'http://'.$_SERVER["HTTP_HOST"].'/kioskoproduccionordenproduccion'?>">
						<div class="col-md-3 col-sm-3 col-lg-3 button btn btn-primary">
							<span class="span-button"></span>
							<b>OP</b>
							<img class="img" src="imagenes/kiosko/Orden_Produccion.png">
						</div>
					</a>

					<a href="<?php echo 'http://'.$_SERVER["HTTP_HOST"].'/kioskoproduccionordencompra'?>">
						<div class="col-md-3 col-sm-3 col-lg-3 button btn btn-primary">
							<span class="span-button"></span>
							<b>OC</b>
							<img class="img" src="imagenes/kiosko/Orden_Compra.png">
						</div>
					</a>
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
@stop
