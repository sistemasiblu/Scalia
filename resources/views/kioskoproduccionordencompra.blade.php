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

			<div class="form-group col-md-12 col-sm-12 col-lg-12" id='test'>
	        {!!Form::label('ocOrdenProduccion', 'OC', array('class' => 'col-sm-2 col-sm-2 col-lg-2 control-label')) !!}
	        <div class="col-sm-10">
	          <div class="input-group">
	            <span class="input-group-addon">
	              <i class="fa fa-barcode"></i>
	            </span>
	            {!!Form::text('ocOrdenProduccion',null,['class'=>'form-control','placeholder'=>'Digite la OC a consultar'])!!}
	          </div>
	        </div>
	      </div>

			<!-- CONTENEDOR INTERNO - PADRE DE LOS BOTONES -->
			<div id="kiosko-div" class="col-md-12 col-sm-12 col-lg-12">

				<!-- <div id="kiosko-produccion" class="col-md-12 col-sm-12 col-lg-12"> -->

				<div class="col-md-4 col-sm-4 col-lg-4">
							
				</div>

					<a onclick="imprimirOrdenCompra(document.getElementById('ocOrdenProduccion').value,'OrdenCompra')">
						<div class="col-md-3 col-sm-3 col-lg-3 button btn btn-primary">
							<span class="span-button"></span>
							<img class="img" src="imagenes/kiosko/Buscar.png">
						</div>
					</a>

				<div class="col-md-4 col-sm-4 col-lg-4 ">
							
				</div>
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
