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

				<a href="<?php echo 'http://'.$_SERVER["HTTP_HOST"].'/kioskoproduccion'?>">
					<div class="col-md-3 col-sm-3 col-lg-3 button btn btn-primary">
						<span class="span-button"></span>
						<img class="img" src="imagenes/kiosko/Produccion.png">
					</div>
				</a>

				<a href="<?php echo 'http://'.$_SERVER["HTTP_HOST"].'/accesodenegado'?>">
					<div class="col-md-3 col-sm-3 col-lg-3 button btn btn-primary">
						<span class="span-button"></span>
						<img class="img" src="imagenes/kiosko/Comercial.png">
					</div>
				</a>

				<a href="<?php echo 'http://'.$_SERVER["HTTP_HOST"].'/kioskogestionhumana'?>">
					<div class="col-md-3 col-sm-3 col-lg-3 button btn btn-primary">
						<span class="span-button"></span>
						<img class="img" src="imagenes/kiosko/Gestion_Humana.png">
					</div>
				</a>

				<a href="<?php echo 'http://'.$_SERVER["HTTP_HOST"].'/accesodenegado'?>">
					<div class="col-md-3 col-sm-3 col-lg-3 button btn btn-primary">
						<span class="span-button"></span>
						<img class="img" src="imagenes/kiosko/Inventario.png">
					</div>
				</a>

				<a href="<?php echo 'http://'.$_SERVER["HTTP_HOST"].'/accesodenegado'?>">
					<div class="col-md-3 col-sm-3 col-lg-3 button btn btn-primary">
						<span class="span-button"></span>
						<img class="img" src="imagenes/kiosko/Cartera.png">
					</div>
				</a>

				<a href="<?php echo 'http://'.$_SERVER["HTTP_HOST"].'/accesodenegado'?>">
					<div class="col-md-3 col-sm-3 col-lg-3 button btn btn-primary">
						<span class="span-button"></span>
						<img class="img" src="imagenes/kiosko/Contabilidad.png">
					</div>
				</a>

			</div> <!-- CIERRE DE DIV CONTENEDOR INTERNO -->

		</div> <!-- CIERRE CONTENEDOR PRINCIPAL DE LA PANTALLA -->

	</fieldset>
</div>
@stop
