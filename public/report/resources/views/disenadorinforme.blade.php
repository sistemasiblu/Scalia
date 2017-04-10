<?php 
	//como parametros se reciben:
	// idInforme. es el id del informe a modificar o eliminar, o cero si es una adicion
	// accion. indica la accion o modo en que se abre el informe, adicion, modificacion o eliminacion
	// NOTA estos parametros se envia a una funcion AJAX en el ready de la vista para que 
	// cargue toda la informacion
	$idInforme = (isset($_GET["idInforme"]) ? $_GET["idInforme"] : 0);
	$accion = (isset($_GET["accion"]) ? $_GET["accion"] : 'adicionar');
	// En caso de que hayan enviado el id en cero o no lo hayan enviado, la accion sera obligatoriamente ADICION
	$accion = ($idInforme == 0 ? 'adicionar' : $accion);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Diseñador de Informes</title>
    <!-- Librerías de Jquery y Jquery-ui -->
	{!! Html::script('jquery/jquery-2-2-3.min.js'); !!}
	{!! Html::script('jquery/jquery-ui-1-11-14.min.js'); !!}
	{!!Html::style('jquery/jquery-ui-1-11-4.css'); !!}

	<!-- Librerías de Font Awesome (iconos) -->
    {!!Html::style('assets/font-awesome-v4.3.0/css/font-awesome.min.css'); !!}

    
	<!-- Librerías de Bootstrap -->
	{!!Html::style('assets/bootstrap-v3.3.5/css/bootstrap.min.css'); !!}
    {!!Html::script('assets/bootstrap-v3.3.5/js/bootstrap.min.js'); !!}	

    <!-- Librerías para el selector de colores (color Picker) -->
    {!! Html::style('assets/colorpicker/css/bootstrap-colorpicker.min.css'); !!}
    {!! Html::script('assets/colorpicker/js/bootstrap-colorpicker.js'); !!}

    <!-- Librerías del proyecto de diseñador de informes -->
	{!! Html::script('js/disenadorinforme.js'); !!}
    {!! Html::style('css/scalia-ui.css'); !!}

</head>
<body>

<div class="clearfix">
	<!-- Token para ejecuciones de ajax -->
	<input type="hidden" id="token" value="{{csrf_token()}}"/>

	<!-- 
	Creamos los paneles del formulario de la siguiente forma

	-----------------------------------------------------
	|                                                   |
	|             id: panelSuperior                     |
	|                                                   |
	|---------------------------------------------------|
	|      id: panelInferior                            |
	|            |                                      |
	|            |                                      |
	| id: panel  |       id: panelDerecho               |
	| Izquierdo  |                                      |
	|            |                                      |
	|            |                                      |
	----------------------------------------------------|


	 -->
	<div id="panelSuperior">

		<div class="panel panel-primary panelLayout" style="height:135px;">

			<!-- Campos de creacion del informe (nombre y descripcion)  -->
			  <div class="panel-heading" style="height: 38px; font-size: 18px; font-weight: bold; font-style: italic;">
			  	<div style="float: left;">
					<span  class="BotonGuardar" title="Guardar" onclick="guardarInforme($('#accionInforme').val());"></span>
				</div>
				<center>Diseñador de Informes</center>
			  	
			  </div>
			  <div class="panel-body">
				<fieldset id="compania-form-fieldset">	
					<div class="form-group" id='test'>
			          {!! Form::label('nombreInforme', 'Nombre', array('class' => 'col-sm-1 control-label')) !!}
			          <div class="col-sm-11">
			            <div class="input-group">
			              <span class="input-group-addon">
			                <i class="fa fa-edit"></i>
			              </span>
			              {!!Form::text('nombreInforme',null,['class'=>'form-control','placeholder'=>'Ingrese el Nombre del Informe'])!!}
			              {!! Form::hidden('idInforme', null, array('id' => 'idInforme')) !!}
			              <input id="accionInforme" type="hidden" value="insertar"/>
			            </div>
			          </div>
			        </div>

			        <div class="form-group" id='test'>
			          {!! Form::label('descripcionInforme', 'Descripción', array('class' => 'col-sm-1 control-label')) !!}
			          <div class="col-sm-11">
			            <div class="input-group">
			              <span class="input-group-addon">
			                <i class="fa fa-edit"></i>
			              </span>
			              {!!Form::textarea('descripcionInforme',null,['class'=>'form-control', 'style' => 'height: 55px;', 'placeholder'=>'Ingrese la Descripción del Informe'])!!}
			            </div>
			          </div>
			        </div>

			    </fieldset>
					 
			  </div>
			</div>	
	</div>

	<div id="panelInferior">
	

		<div id="panelIzquierdo">
		    <span id="position"></span>
		    
		    <!-- Barra gris que divide los paneles izquierdo y derecho, con funcionalidad de cambio de tamaño -->
		    <div id="barraDrag"></div>
		    <!-- <div class="CampoBase campoClonable">Campo</div>
			<div class="CampoBase campoClonable">Campo</div> -->
			
			<!-- Objetos arrastrables a los paneles contenedores de encabezado y pie en el panel derecho -->
			<!-- Paneles de tipo acordeon apra las propiedades del reporte -->
			<div class="panel-group" >

				<div class="panel panel-primary">
						<div class="panel-heading">
							<h5 class="panel-title">
							  <a data-toggle="collapse" href="#controles">Controles</a>
							</h5>
						</div>
					<div id="controles" class="panel-collapse collapse in">
						<div class="EtiquetaBase etiquetaClonable" title="Etiqueta de Texto"></div>
						<div class="ImagenBase imagenClonable" title="Imagen"></div>
						<div class="EditorBase editorClonable" title="Editor de texto"></div>

						
					</div> 

					

				</div>

				<div  style="display:none;" class="panel panel-primary">
						<div class="panel-heading">
							<h5 class="panel-title">
							  <a data-toggle="collapse" href="#conexion">Conexion a Base de Datos</a>
							</h5>
						</div>
					<div id="conexion" class="panel-collapse collapse">
						<table class="table table-striped">
						    <tbody>
						      <tr>
						        <td>{!! Form::label('sistemaInformacion', 'Sistema de informaci&oacute;n') !!}</td>
						        <td>
						        	<select id="sistemaInformacion" onchange="consultarTablaVista(this.value);" placeholder="Seleccione el sistema de informaci&oacute;n" style="width:130px;">
										<option>Seleccione Base de datos</option>
									</select>
								</td>
						      </tr>
						      <tr>
						        <td>{!! Form::label('tablaDocumento', 'Tabla / Vista') !!}</td>
						        <td>
						        	<select id="tablaDocumento" onchange="consultarCampos(document.getElementById('sistemaInformacion').value, this.value);" placeholder="Seleccione la tabla o vista"  style="width:130px;">
										<option >Seleccione Tabla o Vista</option>
									</select>
						        </td>
						      </tr>
						    
						    </tbody>
						  </table>
						  <div id="camposTabla" style="width: 100%; height: 250px; overflow: auto; overflow-y: auto;"> 
						  </div>
					</div> 

					

				</div>
			
				<div class="panel panel-primary" >
				  <div class="panel-heading">
				    <h5 class="panel-title">
				      <a data-toggle="collapse" href="#estilos">Estilos</a>
				    </h5>
				  </div>
				  <div id="estilos" class="panel-collapse collapse">
			  		No se han creado estilos
				        
				  </div>
				</div>


				<div class="panel panel-primary" >
				  <div class="panel-heading">
				    <h4 class="panel-title">
				      <a data-toggle="collapse" href="#basedatos">Sistemas de Información</a>
				    </h4>
				  </div>
				  <div id="basedatos" class="panel-collapse collapse">
				    No se han creado Sistemas de Información
				  </div>
				</div>


				<div  style="display:none;" class="panel panel-primary" >
				  <div class="panel-heading">
				    <h4 class="panel-title">
				      <a data-toggle="collapse" href="#regional">Configuración Regional</a>
				    </h4>
				  </div>
				  <div id="regional" class="panel-collapse collapse">
				    <table class="table table-striped">
					    <tbody>
					      <tr>
					        <td>{!! Form::label('simboloMoneda', 'Símbolo Moneda') !!}</td>
					        <td>{!!Form::text('simboloMoneda',null)!!}</td>
					      </tr>
					      <tr>
					        <td>{!! Form::label('ubicacionMoneda', 'Ubicación Moneda') !!}</td>
					        <td>{!!Form::text('ubicacionMoneda',null)!!}</td>
					      </tr>
					      <tr>
					        <td>{!! Form::label('ubicacionMoneda', 'Separador Miles') !!}</td>
					        <td>{!!Form::text('ubicacionMoneda',null)!!}</td>
					      </tr>
					      <tr>
					        <td>{!! Form::label('ubicacionMoneda', 'Separador Decimales') !!}</td>
					        <td>{!!Form::text('ubicacionMoneda',null)!!}</td>
					      </tr>
					      <tr>
					        <td>{!! Form::label('ubicacionMoneda', 'Negativos en Rojo') !!}</td>
					        <td>{!!Form::text('ubicacionMoneda',null)!!}</td>
					      </tr>
					      
					    </tbody>
					  </table>
				  </div>
				</div>

				<div class="panel panel-primary" >
				  <div class="panel-heading">
				    <h4 class="panel-title">
				      <a data-toggle="collapse" href="#propdoc">Propiedades del Documento</a>
				    </h4>
				  </div>
				  <div id="propdoc" class="panel-collapse collapse">
					<table class="table table-striped" style='width:100%;'>
					    <tbody>
					 	  <tr>
							<th style="width: 40%;">
	                    		<b>Propiedad</b>
	                        </th>
	                        <th style="width: 30%;">
	                      		<b>Fila Par</b>
		                    </th>
		                    <th style="width: 30%;">
	                      		<b>Fila Impar</b>
		                    </th>
		                  </tr>

					      <tr>
					        <td>{!! Form::label('CPcolorFondoParInformePropiedad', 'Color Fondo') !!}</td>
					        <td>
								<div id="CPcolorFondoParInformePropiedad" class="input-group colorpicker-component" style="width: 100%;">
								  <input id="colorFondoParInformePropiedad" type="hidden" value="#00AABB" class="form-control"/>
								  <span class="input-group-addon"><i style="width: 100%;"></i></span>
								</div>
								<script>
								  $(function () {
								      $('#CPcolorFondoParInformePropiedad').colorpicker();
								  });
								</script>
					        </td>
					        <td>
								<div id="CPcolorFondoImparInformePropiedad" class="input-group colorpicker-component" style="width: 100%;">
								  <input id="colorFondoImparInformePropiedad" type="hidden" value="#00AABB" class="form-control"/>
								  <span class="input-group-addon"><i style="width: 100%;"></i></span>
								</div>
								<script>
								  $(function () {
								      $('#CPcolorFondoImparInformePropiedad').colorpicker();
								  });
								</script>
					        </td>
					      </tr>
					      <tr>
					        <td>{!! Form::label('CPcolorBordeParInformePropiedad', 'Color Borde') !!}</td>
					        <td>
								<div id="CPcolorBordeParInformePropiedad" class="input-group colorpicker-component" style="width: 100%;">
								  <input id="colorBordeParInformePropiedad" type="hidden" value="#00AABB" class="form-control"/>
								  <span class="input-group-addon"><i style="width: 100%;"></i></span>
								</div>
								<script>
								  $(function () {
								      $('#CPcolorBordeParInformePropiedad').colorpicker();
								  });
								</script>
					        </td>
					        <td>
								<div id="CPcolorBordeImparInformePropiedad" class="input-group colorpicker-component" style="width: 100%;">
								  <input id="colorBordeImparInformePropiedad" type="hidden" value="#00AABB" class="form-control"/>
								  <span class="input-group-addon"><i style="width: 100%;"></i></span>
								</div>
								<script>
								  $(function () {
								      $('#CPcolorBordeImparInformePropiedad').colorpicker();
								  });
								</script>
					        </td>
					      </tr>
					      <tr>
					        <td>{!! Form::label('CPcolorTextoParInformePropiedad', 'Color Texto') !!}</td>
					        <td>
								<div id="CPcolorTextoParInformePropiedad" class="input-group colorpicker-component" style="width: 100%;">
								  <input id="colorTextoParInformePropiedad" type="hidden" value="#00AABB" class="form-control"/>
								  <span class="input-group-addon"><i style="width: 100%;"></i></span>
								</div>
								<script>
								  $(function () {
								      $('#CPcolorTextoParInformePropiedad').colorpicker();
								  });
								</script>
					        </td>
					        <td>
								<div id="CPcolorTextoImparInformePropiedad" class="input-group colorpicker-component" style="width: 100%;">
								  <input id="colorTextoImparInformePropiedad" type="hidden" value="#00AABB" class="form-control"/>
								  <span class="input-group-addon"><i style="width: 100%;"></i></span>
								</div>
								<script>
								  $(function () {
								      $('#CPcolorTextoImparInformePropiedad').colorpicker();
								  });
								</script>
					        </td>
					       </tr>
					      <tr>
					        <td>{!! Form::label('fuenteTextoParInformePropiedad', 'Fuente') !!}</td>
					        <td>
					        	<select id="fuenteTextoParInformePropiedad">
									<option style="font-family: 'Comic Sans MS'" value="Comic Sans MS">Comic Sans MS</option>
									<option style="font-family: Arial" value="Arial">Arial</option>
									<option style="font-family: 'Times New Roman'" value="Times New Roman">Times New Roman</option>
								</select>
								<!-- http://www.cssfontstack.com/Web-Fonts  -->
					        </td>
					        <td>
					        	<select id="fuenteTextoImparInformePropiedad">
									<option style="font-family: 'Comic Sans MS'" value="Comic Sans MS">Comic Sans MS</option>
									<option style="font-family: Arial" value="Arial">Arial</option>
									<option style="font-family: 'Times New Roman'" value="Times New Roman">Times New Roman</option>
								</select>
								<!-- http://www.cssfontstack.com/Web-Fonts  -->
					        </td>
					      </tr>
					      <tr>
					        <td>{!! Form::label('tamañoTextoParInformePropiedad', 'Tamaño') !!}</td>
					        <td>
					        	<select id="tamañoTextoParInformePropiedad">
									<option style="font-size: 6px;" value="6px">6</option>
									<option style="font-size: 8px;" value="8px">8</option>
									<option style="font-size: 10px;" value="10px">10</option>
									<option style="font-size: 12px;" value="12px">12</option>
									<option style="font-size: 14px;" value="14px">14</option>
									<option style="font-size: 16px;" value="16px">16</option>
									<option style="font-size: 18px;" value="18px">18</option>
									<option style="font-size: 20px;" value="20px">20</option>
									<option style="font-size: 22px;" value="22px">22</option>
									<option style="font-size: 24px;" value="24px">24</option>
								</select>
					        </td>
					        <td>
					        	<select id="tamañoTextoImparInformePropiedad">
									<option style="font-size: 6px;" value="6px">6</option>
									<option style="font-size: 8px;" value="8px">8</option>
									<option style="font-size: 10px;" value="10px">10</option>
									<option style="font-size: 12px;" value="12px">12</option>
									<option style="font-size: 14px;" value="14px">14</option>
									<option style="font-size: 16px;" value="16px">16</option>
									<option style="font-size: 18px;" value="18px">18</option>
									<option style="font-size: 20px;" value="20px">20</option>
									<option style="font-size: 22px;" value="22px">22</option>
									<option style="font-size: 24px;" value="24px">24</option>
								</select>
					        </td>
					      </tr>
					      <tr>
					        <td>{!! Form::label('negrillaParInformePropiedad', 'Negrilla') !!}</td>
					        <td>
					        	<input type="checkbox" id="negrillaParInformePropiedad" value="0"/>
					        </td>
					        <td>
					        	<input type="checkbox" id="negrillaImparInformePropiedad" value="0"/>
					        </td>
					      </tr>
					      <tr>
					        <td>{!! Form::label('italicaParInformePropiedad', 'Itálica') !!}</td>
					        <td>
					        	<input type="checkbox" id="italicaParInformePropiedad"  value="0"/>
					        </td>
					        <td>
					        	<input type="checkbox" id="italicaImparInformePropiedad"  value="0"/>
					        </td>
					      </tr>
					      <tr>
					        <td>{!! Form::label('subrayadoParInformePropiedad', 'Subrayado') !!}</td>
					        <td>
					        	<input type="checkbox" id="subrayadoParInformePropiedad" value="0"/>
					        </td>
					        <td>
					        	<input type="checkbox" id="subrayadoImparInformePropiedad" value="0"/>
					        </td>
					      </tr>
					      
					    </tbody>
				  	</table>

				  </div>
				</div>

				<div  style="display:none;" class="panel panel-primary" >
				  <div class="panel-heading">
				    <h4 class="panel-title">
				      <a data-toggle="collapse" href="#permisos">Permisos</a>
				    </h4>
				  </div>
				  <div id="permisos" class="panel-collapse collapse">
				    
				  </div>
				</div>
			</div>

		</div>
		<div id="panelDerecho">
		<button  style="display:none;" onclick="adicionarCapa(1, 0, $('#accionInforme').val());">
			<i class="fa fa-plus" title="Adicionar Capa General"></i>
		</button>
			
		<button onclick="adicionarCapa(2, 0, $('#accionInforme').val());">
			<i class="fa fa-plus" title="Adicionar Capa Contable"></i>
		</button>
			<!-- creacion de capas del informe (varios reportes en uno) -->
			<ul id="tabcapa" class="nav nav-tabs">
				
			</ul>

			<!-- dentro de este div de capas se crean los layers de informe -->
			<div id="contentcapa" class="tab-content">
			</div>
		</div>

	</div>
</div>


<!-- Modal Estilos-->
<div id="ModalEstilo" class="modal fade" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="height: 100%">
        <!-- Modal content-->
        <div class="modal-content" style="width: 100%;">
            <div class="modal-header btn-default active" style="border-radius: 3px;">
                <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
                <h4 class="modal-title"><span class="fa fa-info-sign"></span>&nbsp; Estilos</h4>
            </div>
            <div class="modal-body" style="height:440px;">
	            <div class="container" style="width: 100%;height: 100%;overflow-y:scroll;">
					{!! Form::label('nombreEstiloInforme', 'Nombre') !!}
					<input id="idEstiloInforme" type="hidden" value="0"/>
					<input id="accionEstiloInforme" type="hidden" value="consultar"/>
					<input id="nombreEstiloInforme" type="text" value="" placeholder="Nombre del Estilo" class="form-control"/>
					<table class="table table-striped" style='width:100%;'>
					    <tbody>
					 	  <tr>
							<th style="width: 50%;">
	                    		<b>Propiedad</b>
	                        </th>
	                        <th style="width: 50%;">
	                      		<b>Valor</b>
		                    </th>
		                  </tr>

					      <tr>
					        <td>{!! Form::label('CPbackground-color', 'Color Fondo') !!}</td>
					        <td>
								<div id="CPbackground-color" class="input-group colorpicker-component" style="width: 100%;">
								  <input id="background-color" type="hidden" value="#00AABB" class="form-control"/>
								  <span class="input-group-addon"><i style="width: 100%;"></i></span>
								</div>
								<script>
								  $(function () {
								      $('#CPbackground-color').colorpicker();
								  });
								</script>
					        </td>
					      </tr>
					      <tr>
					        <td>{!! Form::label('CPborder', 'Color Borde') !!}</td>
					        <td>
								<div id="CPborder" class="input-group colorpicker-component" style="width: 100%;">
								  <input id="border" type="hidden" value="#00AABB" class="form-control"/>
								  <span class="input-group-addon"><i style="width: 100%;"></i></span>
								</div>
								<script>
								  $(function () {
								      $('#CPborder').colorpicker();
								  });
								</script>
					        </td>
					      </tr>
					      <tr>
					        <td>{!! Form::label('CPcolor', 'Color Texto') !!}</td>
					        <td>
								<div id="CPcolor" class="input-group colorpicker-component" style="width: 100%;">
								  <input id="color" type="hidden" value="#00AABB" class="form-control"/>
								  <span class="input-group-addon"><i style="width: 100%;"></i></span>
								</div>
								<script>
								  $(function () {
								      $('#CPcolor').colorpicker();
								  });
								</script>
					        </td>
					       </tr>
					      <tr>
					        <td>{!! Form::label('font-family', 'Fuente') !!}</td>
					        <td>
					        	<select id="font-family">
									<option style="font-family: 'Comic Sans MS'" value="Comic Sans MS">Comic Sans MS</option>
									<option style="font-family: Arial" value="Arial">Arial</option>
									<option style="font-family: 'Times New Roman'" value="Times New Roman">Times New Roman</option>
								</select>
								<!-- http://www.cssfontstack.com/Web-Fonts  -->
					        </td>
					      </tr>
					      <tr>
					        <td>{!! Form::label('font-size', 'Tamaño') !!}</td>
					        <td>
					        	<select id="font-size">
									<option style="font-size: 6px;" value="6px">6</option>
									<option style="font-size: 8px;" value="8px">8</option>
									<option style="font-size: 10px;" value="10px">10</option>
									<option style="font-size: 12px;" value="12px">12</option>
									<option style="font-size: 14px;" value="14px">14</option>
									<option style="font-size: 16px;" value="16px">16</option>
									<option style="font-size: 18px;" value="18px">18</option>
									<option style="font-size: 20px;" value="20px">20</option>
									<option style="font-size: 22px;" value="22px">22</option>
									<option style="font-size: 24px;" value="24px">24</option>
								</select>
					        </td>
					      </tr>
					      <tr>
					        <td>{!! Form::label('font-weight', 'Negrilla') !!}</td>
					        <td>
					        	<input type="checkbox" id="font-weight" value="0"/>
					        </td>
					      </tr>
					      <tr>
					        <td>{!! Form::label('font-style', 'Itálica') !!}</td>
					        <td>
					        	<input type="checkbox" id="font-style"  value="0"/>
					        </td>
					      </tr>
					      <tr>
					        <td>{!! Form::label('text-decoration', 'Subrayado') !!}</td>
					        <td>
					        	<input type="checkbox" id="text-decoration" value="0"/>
					        </td>
					      </tr>
					      
					    </tbody>
				  </table>

				</div> 
            </div>
            <div class="modal-footer btn-default active" style="border-radius: 3px; text-align:center;">
                <button id="guardarEstilo" type="button" class="btn btn-primary" onclick="OcultarEstilo();">OK</button>
                <button id="cancelarEstilo" type="button" class="btn btn-primary" onclick="OcultarEstilo('cancelar');">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<!--Fin Modal -->



<!-- Modal Sistema de Informacion-->
<div id="ModalSistema" class="modal fade" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="height: 100%">
        <!-- Modal content-->
        <div class="modal-content" style="width: 100%;">
            <div class="modal-header btn-default active" style="border-radius: 3px;">
                <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
                <h4 class="modal-title"><span class="fa fa-info-sign"></span>&nbsp; Sistemas de Información</h4>
            </div>
            <div class="modal-body" style="height:440px;">
	            <div class="container" style="width: 100%;height: 100%;overflow-y:scroll;">

					<input id="accionSistemaInformacion" type="hidden" value="consultar"/>

					<fieldset id="sistemainformacion-form-fieldset">	
						<div class="form-group" id='test'>
				          {!!Form::label('codigoSistemaInformacion', 'C&oacute;digo', array('class' => 'col-sm-2 control-label')) !!}
				          <div class="col-sm-10">
				            <div class="input-group">
				              <span class="input-group-addon">
				                <i class="fa fa-barcode"></i>
				              </span>
				              <input id="codigoSistemaInformacion" type="text" value="" placeholder="Ingresa el código del sistema de informaci&oacute;n" class="form-control"/>
				              <input id="idSistemaInformacion" type="hidden" value="consultar"/>
				            </div>
				          </div>
				        </div>

		
						<div class="form-group" id='test'>
				          {!!Form::label('nombreSistemaInformacion', 'Nombre', array('class' => 'col-sm-2 control-label')) !!}
				          <div class="col-sm-10">
				            <div class="input-group">
				              <span class="input-group-addon">
				                <i class="fa fa-pencil-square-o "></i>
				              </span>
								<input id="nombreSistemaInformacion" type="text" value="" placeholder="Ingresa el nombre del sistema de informaci&oacute;n" class="form-control"/>
				            </div>
				          </div>
				    	</div>

				        <div class="form-group" id='test'>
				        {!! Form::label('webSistemaInformacion', 'Sistema Web', array('class' => 'col-sm-2 control-label')) !!}
				        <div class="col-sm-1">
				          <div class="input-group">
				            <span class="input-group-addon">
				              <i class="fa fa-check-circle "></i>
				            </span>
				            <input type="checkbox" id="webSistemaInformacion" value="" class="checkbox-inline"/>
				          </div>
				        </div>
				        </div>
						

						</br></br></br></br></br>
				        

				        <div class="form-group" id='test'>
				          {!!Form::label('ipSistemaInformacion', 'Host', array('class' => 'col-sm-2 control-label')) !!}
				          <div class="col-sm-10">
				            <div class="input-group">
				              <span class="input-group-addon">
				                <i class="fa fa-certificate"></i>
				              </span>
				              <input id="ipSistemaInformacion" type="text" value="" placeholder="Ingresa la IP del sistema de informaci&oacute;n" class="form-control"/>
				            </div>
				          </div>
				        </div>

				        <div class="form-group" id='test'>
				          {!!Form::label('puertoSistemaInformacion', 'Puerto', array('class' => 'col-sm-2 control-label')) !!}
				          <div class="col-sm-10">
				            <div class="input-group">
				              <span class="input-group-addon">
				                <i class="fa fa-cubes"></i>
				              </span>
				              <input id="puertoSistemaInformacion" type="text" value="" placeholder="Ingresa el puerto del sistema de informaci&oacute;n" class="form-control"/>
				            </div>
				          </div>
				        </div>

				        <div class="form-group" id='test'>
				          {!!Form::label('usuarioSistemaInformacion', 'Usuario', array('class' => 'col-sm-2 control-label')) !!}
				          <div class="col-sm-10">
				            <div class="input-group">
				              <span class="input-group-addon">
				                <i class="fa fa-user"></i>
				              </span>
				              <input id="usuarioSistemaInformacion" type="text" value="" placeholder="Ingresa el usuario del sistema de informaci&oacute;n" class="form-control"/>
				            </div>
				          </div>
				        </div>

				        <div class="form-group" id='test'>
				          {!!Form::label('claveSistemaInformacion', 'Clave', array('class' => 'col-sm-2 control-label')) !!}
				          <div class="col-sm-10">
				            <div class="input-group">
				              <span class="input-group-addon">
				                <i class="fa fa-key"></i>
				              </span>
				              <input id="claveSistemaInformacion" type="text" value="" placeholder="Ingresa la contrase&ntilde;a" class="form-control"/>
				            </div>
				          </div>
				        </div>

				        <div class="form-group" id='test'>
				          {!!Form::label('bdSistemaInformacion', 'Esquema', array('class' => 'col-sm-2 control-label')) !!}
				          <div class="col-sm-10">
				            <div class="input-group">
				              <span class="input-group-addon">
				                <i class="fa fa-database"></i>
				              </span>
				              <input id="bdSistemaInformacion" type="text" value="" placeholder="Ingresa la base del sistema de informaci&oacute;n" class="form-control"/>
				            </div>
				          </div>
				        </div>

				        <div class="form-group" id='test'>
				          {!!Form::label('motorbdSistemaInformacion', 'DBMS', array('class' => 'col-sm-2 control-label')) !!}
				          <div class="col-sm-10">
				            <div class="input-group">
				              <span class="input-group-addon">
				                <i class="fa fa-database"></i>
				              </span>
				        {!!Form::text('motorbdSistemaInformacion',null,['class'=>'form-control','placeholder'=>'Ingresa el motor de la base del sistema de informaci&oacute;n'])!!}
				            </div>
				          </div>
				        </div>

					    </br></br></br></br></br></br></br></br></br></br>   
					    <input type="hidden" id="token" value="{{csrf_token()}}"/>
				    </fieldset>
					

				</div> 
            </div>
            <div class="modal-footer btn-default active" style="border-radius: 3px; text-align:center;">
                <button id="guardarSistema" type="button" class="btn btn-primary" onclick="OcultarSistema();">OK</button>
                <button id="cancelarSistema" type="button" class="btn btn-primary" onclick="OcultarSistema('cancelar');">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<!--Fin Modal Sistema de Informacion -->



<!-- Modal Concepto-->
<div id="ModalConcepto"  class="modal fade" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="height: 100%">
        <!-- Modal content-->
        <div class="modal-content" style="width: 100%;">
            <div class="modal-header btn-default active" style="border-radius: 3px;">
                <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
                <h4 class="modal-title"><span class="fa fa-info-sign"></span>&nbsp; Concepto Contable</h4>
            </div>
            <div class="modal-body" style="height:250px;">
	            <div class="container" style="width: 100%;height: 100%;">
	            <fieldset id="accidente-form-fieldset">	
					
					<input id="accionConcepto" type="hidden" value="consultar"/>
					<input id="numeroConcepto" type="hidden" value=""/>

					<div class="form-group" id='test'>
						{!!Form::label('nombreConcepto', 'Concepto', array('class' => 'col-sm-4 control-label'))!!}
						<div class="col-sm-8">
				            <div class="input-group">
				              	<span class="input-group-addon">
				                	<i class="fa fa-barcode"></i>
				              	</span>
								<input type="text" id="nombreConcepto" value="" placeholder"Descripción del Concepto" class="form-control" >
							</div>
						</div>
					</div>

					<div class="form-group" >
						{!!Form::label('', 'Tipo Movimiento', array('class' => 'col-sm-4 control-label'))!!}
						<div class="col-sm-8">
				            <div id="grupo-tipoMovimientoConcepto" class="input-group">
				              	<span class="input-group-addon">
				                	<i class="fa fa-barcode"></i>
				              	</span>
								
								<input type="hidden" id="tipoMovimientoConcepto" value="1" >

							    <a id="MovimientoContable" onclick="seleccionarBotonMultiple('tipoMovimientoConcepto', this.id);" title="Movimiento Contable"><img id="iconoMovimientoConcepto" src="http://<?php echo $_SERVER["HTTP_HOST"];?>/imagenes/movimientocontable.png" class="botonOpciones" style="background-color: #A9E2F3;"></a>
								  
								<a id="SaldoContable"  onclick="seleccionarBotonMultiple('tipoMovimientoConcepto', this.id);" title="Saldo Contable"><img id="iconoSaldoConcepto" src="http://<?php echo $_SERVER["HTTP_HOST"];?>/imagenes/saldocontable.png" class="botonOpciones"></a>
								    
							</div>
						</div>
					</div>

					<div class="form-group" >
						{!!Form::label('contenido', 'Tipo Valor', array('class' => 'col-sm-4 control-label'))!!}
						<div class="col-sm-8">
				            <div id="grupo-tipoValorConcepto" class="input-group">
				              	<span class="input-group-addon">
				                	<i class="fa fa-barcode"></i>
				              	</span>
								
								<input type="hidden" id="tipoValorConcepto" value="Puc">

							    <a id="Puc" onclick="seleccionarBotonTipoValor('tipoValorConcepto', this.id);" title="Plan de cuentas"><img id="iconoPucConcepto" src="http://<?php echo $_SERVER["HTTP_HOST"];?>/imagenes/abaco.png" class="botonOpciones" style="background-color: #A9E2F3;" ></a>
								<a id="Formula" onclick="seleccionarBotonTipoValor('tipoValorConcepto', this.id);" title="Fórmula"><img id="iconoFormulaConcepto" src="http://<?php echo $_SERVER["HTTP_HOST"];?>/imagenes/funcion.png" class="botonOpciones" onclick="MostrarFormula();"></a>
							    <a id="Valor" onclick="seleccionarBotonTipoValor('tipoValorConcepto', this.id);" title="Valor fijo"><img id="iconoValorConcepto" src="http://<?php echo $_SERVER["HTTP_HOST"];?>/imagenes/moneda.png" class="botonOpciones"  ></a>
							    <a id="Porcentaje" onclick="seleccionarBotonTipoValor('tipoValorConcepto', this.id);" title="Porcentaje"><img id="iconoPorcentajeConcepto" src="http://<?php echo $_SERVER["HTTP_HOST"];?>/imagenes/porcentaje.png" class="botonOpciones"></a>
								    <!-- #A9E2F3 -->
								<input type="text" id="valorConcepto" value="" placeholder"Descripción del Concepto" class="form-control"  readonly="readonly">

							</div>
						</div>
					</div>

					<div class="form-group" id='test'>
						{!!Form::label('', 'Excluir Nits', array('class' => 'col-sm-4 control-label'))!!}
						<div class="col-sm-8">
				            <div class="input-group">
				              	<span class="input-group-addon">
				                	<i class="fa fa-barcode"></i>
				              	</span>
								<input type="text" id="excluirTerceroConcepto" value="" placeholder"Nits a Excluir" class="form-control" title="Ingrese los numeros de nit a excluir separados por coma (no ingrese puntos ni digito de verificacion" readonly="readonly">
							</div>
						</div>
					</div>

			  		<div class="form-group" id='test'>
						{!!Form::label('', 'Estilo', array('class' => 'col-sm-4 control-label'))!!}
						<div class="col-sm-8">
				            <div class="input-group">
				              	<span class="input-group-addon">
				                	<i class="fa fa-barcode"></i>
				              	</span>
								<select id="conceptoEstilo" class="form-control">
									
								</select>
							</div>
						</div>
					</div>
					
					<div class="form-group" id='test'>
						{!!Form::label('', 'Uso', array('class' => 'col-sm-4 control-label'))!!}
						<div class="col-sm-8">
				            <div class="input-group">
				              	<span class="input-group-addon">
				                	<i class="fa fa-barcode"></i>
				              	</span>
				              	<input type="hidden" id="detalleConcepto" value="1" >
				              	<input type="hidden" id="resumenConcepto" value="0" >
				              	<input type="hidden" id="graficoConcepto" value="0" >

								<img id="detalle"  onclick="seleccionarBotonSimple(this.id);" src="http://<?php echo $_SERVER["HTTP_HOST"];?>/imagenes/vistadetalle.png" class="botonOpciones" style="background-color: #A9E2F3;" title="Informe Detallado">
		  						<img id="resumen"  onclick="seleccionarBotonSimple(this.id);" src="http://<?php echo $_SERVER["HTTP_HOST"];?>/imagenes/vistaresumen.png" class="botonOpciones" style="background-color: white;" title="Informe Resumido">
				  				<img id="grafico"  onclick="seleccionarBotonSimple(this.id);" src="http://<?php echo $_SERVER["HTTP_HOST"];?>/imagenes/grafico.png" class="botonOpciones" style="background-color: white;" title="Gráficos">
							</div>
						</div>
					</div>

			  		
				</div> 
            </div>
            <div class="modal-footer btn-default active" style="border-radius: 3px; text-align:center;">
                <button id="guardarConcepto" type="button" class="btn btn-primary" onclick="OcultarConcepto();">OK</button>
                <button id="cancelarConcepto" type="button" class="btn btn-primary" onclick="OcultarConcepto('cancelar');">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<!--Fin Modal Concepto -->

<!-- Modal Concepto-->
<div id="ModalFormula"  class="modal fade" data-keyboard="false" data-backdrop="static" >
    <div class="modal-dialog" style="height: 100%">
        <!-- Modal content-->
        <div class="modal-content" style="width: 100%;">
            <div class="modal-header btn-default active" style="border-radius: 3px;">
                <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
                <h4 class="modal-title"><span class="fa fa-info-sign"></span>&nbsp; Fórmula</h4>
            </div>
            <div class="modal-body" style="height:300px;">


            		<div class="col-md-9" style="height:100%;">
            			<div class="col-md-3" >
            				<label>Concepto</label>
            			</div>
						<div class="col-md-9" >
						<select id="conceptoFormula" class="form-control" onchange="concatenarDatos('Concepto', this.value)">
								
						</select>
						</div>
						<div id="concatenado" class="col-md-12" style="height:80%; margin; 10px; border: 2px outset; border-color: #F2F2F2; overflow: auto;">
					        {!!Form::hidden('formulaconcatenada','',['id' => 'formulaconcatenada'])!!}
					        <div id="contenedorFormula"></div>
					        {!!Form::hidden('contadorFormula',0,['id' => 'contadorFormula'])!!}
				        
				    	</div>
					</div>

				    
					<div id="concatenado" class="col-md-3">
					    <div  style="margin:4px 0px 0px 0px;">
					      {!! Form::text('valorConstante',0,array( 'id' => 'valorConstante', "class"=>"form-control", 'height' => '52',  'style' => 'font-size:20px; font-weight: bold; text-align: right;', 'onblur' => 'concatenarDatos("Constante",this.value)','title' => 'Valor Constante')) !!}
					    </div>
					    <div  style="margin:4px 0px 0px 0px;">
					      {!! HTML::image('imagenes/mas.png','mas',array("class"=>"btn btn-success",'width' => '52', 'height' => '52',  'onclick' => 'concatenarDatos("Operador","+")','title' => 'mas')) !!}
					      {!! HTML::image('imagenes/menos.png','menos',array("class"=>"btn btn-success",'width' => '52', 'height' => '52',  'onclick' => 'concatenarDatos("Operador","-")','title' => 'menos')) !!}
					    </div>
					    <div  style="margin:4px 0px 0px 0px;">
					      {!! HTML::image('imagenes/multiplicacion.png','multiplicacion',array("class"=>"btn btn-success",'width' => '52', 'height' => '52', 'onclick' => 'concatenarDatos("Operador","*")','title' => 'multiplicacion')) !!}
					      {!! HTML::image('imagenes/division.png','division',array("class"=>"btn btn-success",'width' => '52', 'height' => '52',  'onclick' => 'concatenarDatos("Operador","/")','title' => 'division')) !!}
					    </div>
					    <div  style="margin:4px 0px 0px 0px;">

					      {!! HTML::image('imagenes/parentesisabre.png','parentesisabre',array("class"=>"btn btn-success",'width' => '52', 'height' => '52',  'onclick' => 'concatenarDatos("Operador","(")','title' => 'parentesisabre')) !!}
					      {!! HTML::image('imagenes/parentesiscierra.png','parentesiscierra',array("class"=>"btn btn-success",'width' => '52', 'height' => '52' , 'onclick' => 'concatenarDatos("Operador",")")','title' => 'parentesiscierra')) !!}
					    </div>
					    <div id="borrar" style="margin:4px 0px 0px 0px;">
					      <span id="borrartodo">

					      {!!Form::button('AC',["class"=>"btn btn-danger", 'style'=>'height:52px; width:52px;', 'onclick' => 'borrarTodo();'])!!}
					      </span>
					      <span id="borrarultimo">
					      {!! HTML::image('imagenes/backspace.png','Borrar Ultimo',array("class"=>"btn btn-danger",'width' => '52', 'height' => '52',  'onclick' => 'borrarUltimo(document.getElementById(\'contadorFormula\').value)','title' => 'mas')) !!}
					      </span>
					    </div>
					</div>

            </div>
            <div class="modal-footer btn-default active" style="border-radius: 3px; text-align:center;">
                <button id="guardarConcepto" type="button" class="btn btn-primary" onclick="OcultarFormula();">OK</button>
                <button id="cancelarConcepto" type="button" class="btn btn-primary" onclick="OcultarFormula('cancelar');">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<!--Fin Modal Concepto -->


<!-- Cuando se termine de cargar el HTML, ejecutamos los maestros laterales de Sistemas de informacion y estilos -->
<script type="text/javascript">
	$( document ).ready(function() 
	{
		var idInforme = "<?php echo $idInforme; ?>";
		var accion = "<?php echo $accion; ?>";

		$("#accionInforme").val(accion);
		// al terminar de cargar el formulario, llamamos las funciones que consultan
		// los sistemas de informacion o bases de datos
	    cargarSistemaInformacion();

		// carga los datos del encabezado del informe
		cargarInforme(idInforme, accion);

		// carga los datos de las capas del informe con sus respectivos contenidos
		cargarInformeCapa(idInforme, accion);
		
		// Carga los estilos
		cargarEstiloInforme();
	});
 </script>

</body>
</html>