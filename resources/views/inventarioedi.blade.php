@extends('layouts.vista')

@section('titulo')
	<h3 id="titulo">
		<center>Inventarios EDI</center>
	</h3>
@stop
@section('content')
@include('alerts.request')
{!!Html::script('js/inventarioedi.js')!!}

{!!Html::script('js/dropzone.js'); !!}<!--Llamo al dropzone-->
{!!Html::style('assets/dropzone/dist/min/dropzone.min.css'); !!}<!--Llamo al dropzone-->
{!!Html::style('css/dropzone.css'); !!}<!--Llamo al dropzone-->

<?php
	$inventarioediinformacion = (isset($inventarioedi) ? $inventarioedi->inventarioediInformaciones : "");

	$idInventarioEDIA = (isset($_GET['accion']) ? $_GET['idInventarioEDI'] : '');
?>
	<script>
		
		var inventarioediContactos = '<?php echo (isset($inventarioedi) ? json_encode($inventarioedi->inventarioediContactos) : "");?>';
		inventarioediContactos = (inventarioediContactos != '' ? JSON.parse(inventarioediContactos) : '');
		var inventarioediProductos = '<?php echo (isset($inventarioedi) ? json_encode($inventarioedi->inventarioediProductos) : "");?>';
		inventarioediProductos = (inventarioediProductos != '' ? JSON.parse(inventarioediProductos) : '');
		var inventarioediExamenMedico = '<?php echo (isset($inventarioedi) ? json_encode($inventarioedi->inventarioediExamenMedicos) : "");?>';
		inventarioediExamenMedico = (inventarioediExamenMedico != '' ? JSON.parse(inventarioediExamenMedico) : '');
		var inventarioediArchivo = '<?php echo (isset($inventarioedi) ? json_encode($inventarioedi->inventarioediarchivos) : "");?>';
		inventarioediArchivo = (inventarioediArchivo != '' ? JSON.parse(inventarioediArchivo) : '');
		var valorContactos = [0,'','','','',''];
		var valorProductos = [0,'',''];
		var valorExamen = [0,0,0,0,0,0];
		var valorArchivo = [0,'','',''];


		var idTipoExamen = '<?php echo isset($idTipoExamen) ? $idTipoExamen : 0;?>';
		var nombreTipoExamen = '<?php echo isset($nombreTipoExamen) ? $nombreTipoExamen : "";?>';
		var idFrecuenciaMedicion = '<?php echo isset($idFrecuenciaMedicion) ? $idFrecuenciaMedicion : 0;?>';
		var nombreFrecuenciaMedicion = '<?php echo isset($nombreFrecuenciaMedicion) ? $nombreFrecuenciaMedicion : "";?>';
		
		var listaTarea = [JSON.parse(idTipoExamen),JSON.parse(nombreTipoExamen)];
		var frencuenciaMedicion = [JSON.parse(idFrecuenciaMedicion),JSON.parse(nombreFrecuenciaMedicion)];

		$(document).ready(function(){

			seleccionarTipoInventarioEDI();

			contactos = new Atributos('contactos','contenedor_contactos','contactos_');
			contactos.campos = ['idInventarioEDIContacto','nombreInventarioEDIContacto','cargoInventarioEDIContacto','telefonoInventarioEDIContacto','movilInventarioEDIContacto','correoElectronicoInventarioEDIContacto'];
			contactos.etiqueta = ['input','input','input','input','input','input'];
			contactos.tipo = ['hidden','text','text','text','text','text'];
			contactos.estilo = ['','width: 330px; height:35px;','width: 270px;height:35px;','width: 150px;height:35px;','width: 150px;height:35px;','width: 230px;height:35px;'];
			contactos.clase = ['','','','','',''];
			contactos.sololectura = [false,false,false,false,false,false];

			productos = new Atributos('productos','contenedor_productos','productos_');
			productos.campos = ['idInventarioEDIProducto','codigoInventarioEDIProducto','nombreInventarioEDIProducto'];
			productos.etiqueta = ['input','input','input'];
			productos.tipo = ['hidden','text','text'];
			productos.estilo = ['','width: 380px; height:35px;','width: 750px;height:35px;'];
			productos.clase = ['','',''];
			productos.sololectura = [false,false,false];

			examen = new Atributos('examen','contenedor_examen','examen');
			examen.campos = ['idInventarioEDIExamenMedico', 'TipoExamenMedico_idTipoExamenMedico','ingresoInventarioEDIExamenMedico','retiroInventarioEDIExamenMedico','periodicoInventarioEDIExamenMedico','FrecuenciaMedicion_idFrecuenciaMedicion'];
			examen.etiqueta = ['input','select','checkbox','checkbox','checkbox','select'];
			examen.tipo = ['hidden','','checkbox','checkbox','checkbox',''];
			examen.estilo = ['','width: 300px;height:35px;','width: 90px;height:33px;display:inline-block;','width: 90px;height:33px;display:inline-block;','width: 90px;height:33px;display:inline-block;','width: 300px;height:35px;'];
			examen.clase = ['','','','','',''];
			examen.sololectura = [false,false,false,false,false,false];
			examen.completar = ['off','off','off','off','off','off'];
			examen.opciones = ['',listaTarea,'','','',frencuenciaMedicion];
			examen.funciones  = ['','','','','',''];

			// archivo = new Atributos('archivo','contenedor_archivo','archivo');
			// archivo.campos = ['idInventarioEDIArchivo', 'tituloInventarioEDIArchivo','fechaInventarioEDIArchivo','rutaInventarioEDIArchivo'];
			// archivo.etiqueta = ['input','input','input','input'];
			// archivo.tipo = ['hidden','text','text','text'];
			// archivo.estilo = ['','width: 300px;height:35px;','width: 200px;height:35px;','width: 600px;height:35px;'];
			// archivo.clase = ['','','','',];
			// archivo.sololectura = [false,false,false,false];
			// archivo.completar = ['off','off','off','off'];
			// archivo.opciones = ['','','',''];
			// archivo.funciones  = ['','','',''];


			for(var j=0, k = inventarioediContactos.length; j < k; j++)
			{
				contactos.agregarCampos(JSON.stringify(inventarioediContactos[j]),'L');
			}

			for(var j=0, k = inventarioediProductos.length; j < k; j++)
			{
				productos.agregarCampos(JSON.stringify(inventarioediProductos[j]),'L');
			}

			for(var j=0, k = inventarioediExamenMedico.length; j < k; j++)
			{
				examen.agregarCampos(JSON.stringify(inventarioediExamenMedico[j]),'L');
			}

			for(var j=0, k = inventarioediArchivo.length; j < k; j++)
			{
				archivo.agregarCampos(JSON.stringify(inventarioediArchivo[j]),'L');
			}

			

		});
	</script>
	@if(isset($inventarioedi))
		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
			{!!Form::model($inventarioedi,['route'=>['inventarioedi.destroy',$inventarioedi->idInventarioEDI],'method'=>'DELETE', 'files' => true])!!}
		@else
			{!!Form::model($inventarioedi,['route'=>['inventarioedi.update',$inventarioedi->idInventarioEDI],'method'=>'PUT', 'files' => true])!!}
		@endif
	@else
		{!!Form::open(['route'=>'inventarioedi.store','method'=>'POST', 'files' => true])!!}
	@endif

		<div id="form_section">
			<fieldset id="inventarioedi-form-fieldset">
				<table width="100%">
					<tr>
						<td>
							<div class="form-group" style="width:565px; display: inline;">
								{!!Form::label('TipoIdentificacion_idTipoIdentificacion', 'Tipo de Identificaci&oacute;n', array('class' => 'col-sm-2 control-label','style'=>'width:180px;'))!!}
								<div class="col-sm-10" style="width:340px;">
									<div class="input-group">
										<span class="input-group-addon">
											<i class="fa fa-credit-card" style="width: 14px;"></i>
										</span>
										{!! Form::hidden('idInventarioEDI', null, array('id' => 'idInventarioEDI')) !!}
										{!!Form::select('TipoIdentificacion_idTipoIdentificacion',$tipoIdentificacion, (isset($inventarioedi) ? $inventarioedi->TipoIdentificacion_idTipoIdentificacion : 0),["class" => "chosen-select form-control", "placeholder" =>"Seleccione el tipo de identificaci&oacute;n",'style'=>'width:300px;'])!!}
									</div>
								</div>
							</div>
							<div class="form-group" style="width:565px; display: inline; " >
								{!!Form::label('documentoInventarioEDI', 'Documento No.', array('class' => 'col-sm-2 control-label','style'=>'width:180px;padding-left:30px;'))!!}
								<div class="col-sm-10" style="width:340px;">
									<div class="input-group" >
										<span class="input-group-addon">
											<i class="fa fa-barcode" style="width: 14px;"></i>
										</span>
										{!!Form::text('documentoInventarioEDI',null,['class'=>'form-control','placeholder'=>'Ingresa el n&uacute;mero de documento','style'=>'width:300px;'])!!}
									</div>
								</div>
							</div>
							<div class="form-group" style="width:565px; display: inline;">
								{!!Form::label('nombre1InventarioEDI', 'Primer Nombre', array('class' => 'col-sm-2 control-label','style'=>'width:180px;'))!!}
								<div class="col-sm-10" style="width:340px;">
									<div class="input-group">
										<span class="input-group-addon">
											<i class="fa fa-pencil-square-o" style="width: 14px;"></i>
										</span>
										{!!Form::text('nombre1InventarioEDI',null,['class'=>'form-control','placeholder'=>'Ingresa el primer nombre del inventarioedi','style'=>'width:300px;','id'=>'nombre1InventarioEDI', 'onchange'=>'llenaNombreInventarioEDI()'])!!}
									</div>
								</div>
							</div>
							<div class="form-group" style="width:565px; display: inline;">
								{!!Form::label('nombre2InventarioEDI', 'Segundo Nombre', array('class' => 'col-sm-2 control-label','style'=>'width:180px;padding-left:30px;'))!!}
								<div class="col-sm-10" style="width:340px;">
									<div class="input-group">
										<span class="input-group-addon">
											<i class="fa fa-pencil-square-o" style="width: 14px;"></i>
										</span>
										{!!Form::text('nombre2InventarioEDI',null,['class'=>'form-control','placeholder'=>'Ingresa el segundo nombre del inventarioedi','style'=>'width:300px;','id'=>'nombre2InventarioEDI', 'onchange'=>'llenaNombreInventarioEDI()'])!!}
									</div>
								</div>
							</div>
							<div class="form-group" style="width:565px; display: inline;">
								{!!Form::label('apellido1InventarioEDI', 'Primer Apellido', array('class' => 'col-sm-2 control-label','style'=>'width:180px;'))!!}
								<div class="col-sm-10" style="width:340px;">
									<div class="input-group">
										<span class="input-group-addon">
											<i class="fa fa-pencil-square-o" style="width: 14px;"></i>
										</span>
										{!!Form::text('apellido1InventarioEDI',null,['class'=>'form-control','placeholder'=>'Ingresa el primer apellido del inventarioedi','style'=>'width:300px;','id'=>'apellido1InventarioEDI', 'onchange'=>'llenaNombreInventarioEDI()'])!!}
									</div>
								</div>
							</div>
							<div class="form-group" style="width:565px; display: inline;">
								{!!Form::label('apellido2InventarioEDI', 'Segundo Apellido', array('class' => 'col-sm-2 control-label','style'=>'width:180px;padding-left:30px;'))!!}
								<div class="col-sm-10" style="width:340px;">
									<div class="input-group">
										<span class="input-group-addon">
											<i class="fa fa-pencil-square-o" style="width: 14px;"></i>
										</span>
										{!!Form::text('apellido2InventarioEDI',null,['class'=>'form-control','placeholder'=>'Ingresa el segundo apellido del inventarioedi','style'=>'width:300px;','id'=>'apellido2InventarioEDI', 'onchange'=>'llenaNombreInventarioEDI()'])!!}
									</div>
								</div>
							</div>
							<div class="form-group" style="width:1000px; display: inline;">
								{!!Form::label('nombreCompletoInventarioEDI', 'Nombre Completo', array('class' => 'col-sm-2 control-label','style'=>'width:180px;'))!!}
								<div class="col-sm-10" style="width:820px;">
									<div class="input-group">
										<span class="input-group-addon">
											<i class="fa fa-navicon" style="width: 14px;"></i>
										</span>
										{!!Form::text('nombreCompletoInventarioEDI',null,['class'=>'form-control','placeholder'=>'Nombre completo del InventarioEDI','style'=>'width:820px;','readonly'=>true])!!}
									</div>
								</div>
							</div>
							<div class="form-group" style="width:565px; display: inline;">
								{!!Form::label('fechaCreacionInventarioEDI', 'Fecha Creaci&oacute;n', array('class' => 'col-sm-2 control-label','style'=>'width:180px;'))!!}
								<div class="col-sm-10" style="width:340px;">
									<div class="input-group">
										<span class="input-group-addon">
											<i class="fa fa-calendar" style="width: 14px;"></i>
										</span>
										{!!Form::text('fechaCreacionInventarioEDI',date('Y-m-d'),['class'=>'form-control','placeholder'=>'Ingresa la fecha creaci&oacute;n del inventarioedi','style'=>'width:300px;','readonly'=>true])!!}
									</div>
								</div>
							</div>
							<div class="form-group" style="width:565px; display: inline;">
								{!!Form::label('estadoInventarioEDI', 'Estado', array('class' => 'col-sm-2 control-label','style'=>'width:180px;padding-left:30px;'))!!}
								<div class="col-sm-10" style="width:340px;">
									<div class="input-group">
										<span class="input-group-addon">
											<i class="fa fa-bar-chart-o" style="width: 14px;"></i>
										</span>
										{!!Form::select('estadoInventarioEDI',array('ACTIVO'=>'Activo','INACTIVO'=>'Inactivo'),(isset($inventarioedi) ? $inventarioedi->estadoInventarioEDI : 0),["class" => "chosen-select form-control",'style'=>'width:300px;'])!!}
									</div>
								</div>
							</div>
							<div class="form-group" style="width:1000px; display: inline;">
								<div class="col-lg-12">
									<div class="panel panel-default">
										<div class="panel-body">
											{!! Form::hidden('tipoInventarioEDI', null, array('id' => 'tipoInventarioEDI')) !!}
											<div class="checkbox-inline">
												<label>
													{!!Form::checkbox('tipoInventarioEDI1','01',false, array('id' => 'tipoInventarioEDI1', 'onclick'=>'validarTipoInventarioEDI()'))!!}Empleado
												</label>
											</div>
											<div class="checkbox-inline">
												<label>
													{!!Form::checkbox('tipoInventarioEDI1','02',false, array('id' => 'tipoInventarioEDI2', 'onclick'=>'validarTipoInventarioEDI()'))!!}Proveedor
												</label>
											</div>
											<div class="checkbox-inline" style="display:none;">
												<label>
													{!!Form::hidden('tipoInventarioEDI1','03',false, array('id' => 'tipoInventarioEDI3', 'onclick'=>'validarTipoInventarioEDI()'))!!}Cliente
												</label>
											</div>
											<div class="checkbox-inline" style="display:none;">
												<label>
													{!!Form::hidden('tipoInventarioEDI1','04',false, array('id' => 'tipoInventarioEDI4', 'onclick'=>'validarTipoInventarioEDI()'))!!}Entidad Estatal
												</label>
											</div>
											<div class="checkbox-inline" style="display:none;">
												<label>
													{!!Form::hidden('tipoInventarioEDI1','05',false, array('id' => 'tipoInventarioEDI5', 'onclick'=>'validarTipoInventarioEDI()'))!!}Seguridad Social
												</label>
											</div>
										</div>
									</div>
								</div>
							</div>
						</td>
						<td>
							<div class="form-group" style="width:250px; display: inline;" >
								<div class="col-sm-10" style="width:250px;">
									<div class="panel panel-default">
										<input id="imagenInventarioEDI" name="imagenInventarioEDI" type="file" >
									</div>
								</div>
							</div>
						</td>
					</tr>
				</table>
				<div class="form-group">
					<div class="col-lg-12">
						<div class="panel panel-default">
							<div class="panel-heading">Detalles</div>
							<div class="panel-body">
								<div class="panel-group" id="accordion">
									<div class="panel panel-default">
										<div class="panel-heading">
											<h4 class="panel-title">
												<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Datos Generales</a>
											</h4>
										</div>
										<div id="collapseOne" class="panel-collapse collapse in">
											<div class="panel-body">
												<div class="form-group" style="width:600px; display: inline;" >
													{!!Form::label('Ciudad_idCiudad', 'Ciudad', array('class' => 'col-sm-2 control-label','style'=>'width:180px;'))!!}
													<div class="col-sm-10" style="width:400px;">
														<div class="input-group" >
															<span class="input-group-addon">
																<i class="fa fa-flag" style="width: 14px;"></i>
															</span>
															{!!Form::select('Ciudad_idCiudad',$ciudad, (isset($inventarioedi) ? $inventarioedi->Ciudad_idCiudad : 0),["class" => "chosen-select form-control", "placeholder" =>"Seleccione la ciudad",'style'=>'width:340px;'])!!}

														</div>
													</div>
												</div>
												<div class="form-group" style="width:600px; display: inline;">
													{!!Form::label('direccionInventarioEDI', 'Direcci&oacute;n', array('class' => 'col-sm-2 control-label','style'=>'width:180px;padding-left:30px;'))!!}
													<div class="col-sm-10" style="width:400px;">
														<div class="input-group">
															<span class="input-group-addon">
																<i class="fa fa-home" style="width: 14px;"></i>
															</span>
															{!!Form::text('direccionInventarioEDI',null,['class'=>'form-control','placeholder'=>'Ingresa la direcci&oacute;n','style'=>'width:340px;'])!!}
														</div>
													</div>
												</div>
												<div class="form-group" style="width:600px; display: inline;">
													{!!Form::label('telefonoInventarioEDI', 'Tel&eacute;fono', array('class' => 'col-sm-2 control-label','style'=>'width:180px;'))!!}
													<div class="col-sm-10" style="width:400px;">
														<div class="input-group">
															<span class="input-group-addon">
																<i class="fa fa-phone" style="width: 14px;"></i>
															</span>
															{!!Form::text('telefonoInventarioEDI',null,['class'=>'form-control','placeholder'=>'Ingresa el n&uacute;mero de tel&eacute;fono','style'=>'width:340px;'])!!}
														</div>
													</div>
												</div>
												<div class="form-group" style="width:600px; display: inline;">
													{!!Form::label('faxInventarioEDI', 'Fax', array('class' => 'col-sm-2 control-label','style'=>'width:180px;padding-left:30px;'))!!}
													<div class="col-sm-10" style="width:400px;">
														<div class="input-group">
															<span class="input-group-addon">
																<i class="fa fa-fax" style="width: 14px;"></i>
															</span>
															{!!Form::text('faxInventarioEDI',null,['class'=>'form-control','placeholder'=>'Ingresa el fax','style'=>'width:340px;'])!!}
														</div>
													</div>
												</div>
												<div class="form-group" style="width:600px; display: inline;">
													{!!Form::label('movil1InventarioEDI', 'M&oacute;vil 1', array('class' => 'col-sm-2 control-label','style'=>'width:180px;'))!!}
													<div class="col-sm-10" style="width:400px;">
														<div class="input-group">
															<span class="input-group-addon">
																<i class="fa fa-mobile-phone" style="width: 14px;"></i>
															</span>
															{!!Form::text('movil1InventarioEDI',null,['class'=>'form-control','placeholder'=>'Ingresa el n&uacute;mero del m&oacute;vil 1','style'=>'width:340px;'])!!}
														</div>
													</div>
												</div>
												<div class="form-group" style="width:600px; display: inline;">
													{!!Form::label('movil2InventarioEDI', 'M&oacute;vil 2', array('class' => 'col-sm-2 control-label','style'=>'width:180px;padding-left:30px;'))!!}
													<div class="col-sm-10" style="width:400px;">
														<div class="input-group">
															<span class="input-group-addon">
																<i class="fa fa-mobile" style="width: 14px;"></i>
															</span>
															{!!Form::text('movil2InventarioEDI',null,['class'=>'form-control','placeholder'=>'Ingresa el n&uacute;mero del m&oacute;vil 2','style'=>'width:340px;'])!!}
														</div>
													</div>
												</div>
												<div class="form-group" style="width:600px; display: inline;">
													{!!Form::label('sexoInventarioEDI', 'Sexo', array('class' => 'col-sm-2 control-label','style'=>'width:180px;'))!!}
													<div class="col-sm-10" style="width:400px;">
														<div class="input-group">
															<span class="input-group-addon">
																<i class="fa fa-user" style="width: 14px;"></i>
															</span>
															{!!Form::select('sexoInventarioEDI',
															array('F'=>'Femenino','M'=>'Masculino'), 
															(isset($inventarioedi) ? $inventarioedi->sexoInventarioEDI : 0),["class" => "form-control", "placeholder" =>"Seleccione el sexo del inventarioedi",'style'=>'width:340px;'])!!}
														</div>
													</div>
												</div>
												<div class="form-group" style="width:600px; display: inline;">
													{!!Form::label('correoElectronicoInventarioEDI', 'E-Mail', array('class' => 'col-sm-2 control-label','style'=>'width:180px;padding-left:30px;'))!!}
													<div class="col-sm-10" style="width:400px;">
														<div class="input-group">
															<span class="input-group-addon">
																<i class="fa fa-envelope" style="width: 14px;"></i>
															</span>
															{!!Form::text('correoElectronicoInventarioEDI',null,['class'=>'form-control','placeholder'=>'Ingresa el correo','style'=>'width:340px;'])!!}
														</div>
													</div>
												</div>
												<div class="form-group" style="width:600px; display: inline;">
													{!!Form::label('paginaWebInventarioEDI', 'P&aacute;gina Web', array('class' => 'col-sm-2 control-label','style'=>'width:180px;'))!!}
													<div class="col-sm-10" style="width:400px;">
														<div class="input-group">
															<span class="input-group-addon">
																<i class="fa fa-laptop" style="width: 14px;"></i>
															</span>
															{!!Form::text('paginaWebInventarioEDI',null,['class'=>'form-control','placeholder'=>'Ingresa la p&aacute;gina web','style'=>'width:340px;'])!!}
														</div>
													</div>
												</div>
												<div class="form-group" style="width:600px; display: none;" id="cargo">
													{!!Form::label('Cargo_idCargo', 'Cargo', array('class' => 'col-sm-2 control-label','style'=>'width:180px;padding-left:30px;'))!!}
													<div class="col-sm-10" style="width:400px;">
														<div class="input-group">
															<span class="input-group-addon">
																<i class="fa fa-mobile" style="width: 14px;"></i>
															</span>
															{!!Form::select('Cargo_idCargo',$cargo, (isset($inventarioedi) ? $inventarioedi->Cargo_idCargo : 0),["class" => "js-example-placeholder-single js-states form-control", "placeholder" =>"Seleccione el cargo",'style'=>'width:340px;'])!!}
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="panel panel-default" style="display:none;" id="pestanaLaboral">
										<div class="panel-heading">
											<h4 class="panel-title">
												<a data-toggle="collapse" data-parent="#accordion" href="#laboral">Informaci&oacute;n Laboral</a>
											</h4>
										</div>
										<div id="laboral" class="panel-collapse collapse">
											<div class="panel-body">
												
												<div class="form-group" style="width:600px; display: inline;">
													{!!Form::label('fechaIngresoInventarioEDIInformacion', 'Fecha Ingreso', array('class' => 'col-sm-2 control-label','style'=>'width:180px;'))!!}
													<div class="col-sm-10" style="width:400px;">
														<div class="input-group">
															<span class="input-group-addon">
																<i class="fa fa-fax" style="width: 14px;"></i>
															</span>
															{!!Form::text('fechaIngresoInventarioEDIInformacion',(isset($inventarioedi->inventarioediInformaciones) ? $inventarioedi->inventarioediInformaciones->fechaIngresoInventarioEDIInformacion : null),['class'=>'form-control','placeholder'=>'Seleccione la fecha de ingreso','style'=>'width:340px;'])!!}
														</div>
													</div>
												</div>
												<div class="form-group" style="width:600px; display: inline;">
													{!!Form::label('fechaRetiroInventarioEDIInformacion', 'Fecha Retiro', array('class' => 'col-sm-2 control-label','style'=>'width:180px;'))!!}
													<div class="col-sm-10" style="width:400px;">
														<div class="input-group">
															<span class="input-group-addon">
																<i class="fa fa-fax" style="width: 14px;"></i>
															</span>
															{!!Form::text('fechaRetiroInventarioEDIInformacion',(isset($inventarioedi->inventarioediInformaciones) ? $inventarioedi->inventarioediInformaciones->fechaRetiroInventarioEDIInformacion : null),['class'=>'form-control','placeholder'=>'Seleccione la fecha de retiro','style'=>'width:340px;'])!!}
														</div>
													</div>
												</div>
												<div class="form-group" style="width:600px; display: inline;">
													{!!Form::label('tipoContratoInventarioEDIInformacion', 'Tipo de Contrato', array('class' => 'col-sm-2 control-label','style'=>'width:180px;'))!!}
													<div class="col-sm-10" style="width:400px;">
														<div class="input-group">
															<span class="input-group-addon">
																<i class="fa fa-user" style="width: 14px;"></i>
															</span>
															{!!Form::select('tipoContratoInventarioEDIInformacion',
															array('C'=>'Contratista','TF'=>'T&eacute;rmino Fijo','I'=>'Indefinido','S'=>'Servicios'),(isset($inventarioedi->inventarioediInformaciones) ? $inventarioedi->inventarioediInformaciones->tipoContratoInventarioEDIInformacion : null),["class" => "js-example-placeholder-single js-states form-control", "placeholder" =>"Seleccione el tipo de contrato",'style'=>'width:340px;'])!!}
														</div>
													</div>
												</div>
												<div class="form-group" style="width:600px; display: inline;">
													{!!Form::label('aniosExperienciaInventarioEDIInformacion', 'A&ntilde;os de Experiencia', array('class' => 'col-sm-2 control-label','style'=>'width:180px;'))!!}
													<div class="col-sm-10" style="width:400px;">
														<div class="input-group">
															<span class="input-group-addon">
																<i class="fa fa-user" style="width: 14px;"></i>
															</span>
															{!!Form::text('aniosExperienciaInventarioEDIInformacion',(isset($inventarioedi->inventarioediInformaciones) ? $inventarioedi->inventarioediInformaciones->aniosExperienciaInventarioEDIInformacion : null),['class'=>'form-control','placeholder'=>'Digite los a&ntilde;os de experiencia','style'=>'width:340px;'])!!}
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="panel panel-default" style="display:none;" id="pestanaEducacion">
										<div class="panel-heading">
											<h4 class="panel-title">
												<a data-toggle="collapse" data-parent="#accordion" href="#educacion">Educaci&oacute;n</a>
											</h4>
										</div>
										<div id="educacion" class="panel-collapse collapse">
											<div class="panel-body">
												<div class="form-group" id='test'>
													<div class="col-sm-10" style="width: 100%;">
														<div class="input-group">
															{!!Form::textarea('educacionInventarioEDIInformacion',(isset($inventarioedi->inventarioediInformaciones) ? $inventarioedi->inventarioediInformaciones->educacionInventarioEDIInformacion : null),['class'=>'ckeditor','placeholder'=>'Ingresa la educaci&oacute;n'])!!}
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="panel panel-default" style="display:none;" id="pestanaExperiencia">
										<div class="panel-heading">
											<h4 class="panel-title">
												<a data-toggle="collapse" data-parent="#accordion" href="#experiencia">Experiencia</a>
											</h4>
										</div>
										<div id="experiencia" class="panel-collapse collapse">
											<div class="panel-body">
												<div class="form-group" id='test'>
													<div class="col-sm-10" style="width: 100%;">
														<div class="input-group">
															{!!Form::textarea('experienciaInventarioEDIInformacion',(isset($inventarioedi->inventarioediInformaciones) ? $inventarioedi->inventarioediInformaciones->experienciaInventarioEDIInformacion : null),['class'=>'ckeditor','placeholder'=>'Ingresa la experiencia'])!!}
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="panel panel-default" style="display:none;" id="pestanaFormacion">
										<div class="panel-heading">
											<h4 class="panel-title">
												<a data-toggle="collapse" data-parent="#accordion" href="#formacion">Formaci&oacute;n</a>
											</h4>
										</div>
										<div id="formacion" class="panel-collapse collapse">
											<div class="panel-body">
												<div class="form-group" id='test'>
													<div class="col-sm-10" style="width: 100%;">
														<div class="input-group">
															{!!Form::textarea('formacionInventarioEDIInformacion',(isset($inventarioedi->inventarioediInformaciones) ? $inventarioedi->inventarioediInformaciones->formacionInventarioEDIInformacion : null),['class'=>'ckeditor','placeholder'=>'Ingresa la experiencia'])!!}
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="panel panel-default" style="display:none;" id="pestanaPersonal">
										<div class="panel-heading">
											<h4 class="panel-title">
												<a data-toggle="collapse" data-parent="#accordion" href="#personal">Informaci&oacute;n Personal</a>
											</h4>
										</div>
										<div id="personal" class="panel-collapse collapse">
											<div class="panel-body">
												<div class="form-group" style="width:600px; display: inline;">
													{!!Form::label('fechaNacimientoInventarioEDIInformacion', 'Fecha Nacimiento', array('class' => 'col-sm-2 control-label','style'=>'width:180px;'))!!}
													<div class="col-sm-10" style="width:400px;">
														<div class="input-group">
															<span class="input-group-addon">
																<i class="fa fa-phone" style="width: 14px;"></i>
															</span>
															{!!Form::text('fechaNacimientoInventarioEDIInformacion',(isset($inventarioedi->inventarioediInformaciones) ? $inventarioedi->inventarioediInformaciones->fechaNacimientoInventarioEDIInformacion : null),['class'=>'form-control','placeholder'=>'Ingrese la fecha de nacimiento','style'=>'width:340px;'])!!}
														</div>
													</div>
												</div>
												<div class="form-group" style="width:600px; display: inline;">
													{!!Form::label('estadoCivilInventarioEDIInformacion', 'Estado Civil', array('class' => 'col-sm-2 control-label','style'=>'width:180px;'))!!}
													<div class="col-sm-10" style="width:400px;">
														<div class="input-group">
															<span class="input-group-addon">
																<i class="fa fa-user" style="width: 14px;"></i>
															</span>
															{!!Form::select('estadoCivilInventarioEDIInformacion',
															array('CASADO'=>'Casado','SOLTERO'=>'Soltero'),(isset($inventarioedi->inventarioediInformaciones) ? $inventarioedi->inventarioediInformaciones->estadoCivilInventarioEDIInformacion : null),["class" => "js-example-placeholder-single js-states form-control", "placeholder" =>"Seleccione el estado civil",'style'=>'width:340px;'])!!}
														</div>
													</div>
												</div>
												<div class="form-group" style="width:600px; display: inline;">
													{!!Form::label('numeroHijosInventarioEDIInformacion', 'N&uacute;mero de Hijos', array('class' => 'col-sm-2 control-label','style'=>'width:180px;'))!!}
													<div class="col-sm-10" style="width:400px;">
														<div class="input-group">
															<span class="input-group-addon">
																<i class="fa fa-user" style="width: 14px;"></i>
															</span>
															{!!Form::text('numeroHijosInventarioEDIInformacion',(isset($inventarioedi->inventarioediInformaciones) ? $inventarioedi->inventarioediInformaciones->numeroHijosInventarioEDIInformacion : null),['class'=>'form-control','placeholder'=>'Digite el n&uacute;mero de hijos','style'=>'width:340px;'])!!}
														</div>
													</div>
												</div>
												<div class="form-group" style="width:600px; display: inline;">
													{!!Form::label('composicionFamiliarInventarioEDIInformacion', 'Composici&oacute;n Familiar', array('class' => 'col-sm-2 control-label','style'=>'width:180px;'))!!}
													<div class="col-sm-10" style="width:400px;">
														<div class="input-group">
															<span class="input-group-addon">
																<i class="fa fa-user" style="width: 14px;"></i>
															</span>
															{!!Form::select('composicionFamiliarInventarioEDIInformacion',
															array('VS'=>'Vive Solo','SH'=>'Solo con Hijos','EH'=>'Esposo e Hijos','FO'=>'Familia de Origen','A'=>'Amigos'),(isset($inventarioedi->inventarioediInformaciones) ? $inventarioedi->inventarioediInformaciones->composicionFamiliarInventarioEDIInformacion : null),["class" => "js-example-placeholder-single js-states form-control", "placeholder" =>"Seleccione la composici&oacute;n familiar",'style'=>'width:340px;'])!!}
														</div>
													</div>
												</div>
												<div class="form-group" style="width:600px; display: inline;">
													{!!Form::label('personasACargoInventarioEDIInformacion', 'Personas a Cargo', array('class' => 'col-sm-2 control-label','style'=>'width:180px;'))!!}
													<div class="col-sm-10" style="width:400px;">
														<div class="input-group">
															<span class="input-group-addon">
																<i class="fa fa-user" style="width: 14px;"></i>
															</span>
															{!!Form::text('personasACargoInventarioEDIInformacion',(isset($inventarioedi->inventarioediInformaciones) ? $inventarioedi->inventarioediInformaciones->personasACargoInventarioEDIInformacion : null),['class'=>'form-control','placeholder'=>'Digite el n&uacute;mero de personas a cargo','style'=>'width:340px;'])!!}
														</div>
													</div>
												</div>
												<div class="form-group" style="width:600px; display: inline;">
													{!!Form::label('estratoSocialInventarioEDIInformacion', 'Estrato', array('class' => 'col-sm-2 control-label','style'=>'width:180px;'))!!}
													<div class="col-sm-10" style="width:400px;">
														<div class="input-group">
															<span class="input-group-addon">
																<i class="fa fa-user" style="width: 14px;"></i>
															</span>
															{!!Form::text('estratoSocialInventarioEDIInformacion',(isset($inventarioedi->inventarioediInformaciones) ? $inventarioedi->inventarioediInformaciones->estratoSocialInventarioEDIInformacion : null),['class'=>'form-control','placeholder'=>'Digite el estrato','style'=>'width:340px;'])!!}
														</div>
													</div>
												</div>
												<div class="form-group" style="width:600px; display: inline;">
													{!!Form::label('tipoViviendaInventarioEDIInformacion', 'Tipo de Vivienda', array('class' => 'col-sm-2 control-label','style'=>'width:180px;'))!!}
													<div class="col-sm-10" style="width:400px;">
														<div class="input-group">
															<span class="input-group-addon">
																<i class="fa fa-user" style="width: 14px;"></i>
															</span>
															{!!Form::select('tipoViviendaInventarioEDIInformacion',
															array('PROPIA'=>'Propia','ARRENDADA'=>'Arrendada','FAMILIAR'=>'Familiar'),(isset($inventarioedi->inventarioediInformaciones) ? $inventarioedi->inventarioediInformaciones->tipoViviendaInventarioEDIInformacion : null),["class" => "js-example-placeholder-single js-states form-control", "placeholder" =>"Seleccione el tipo de vivienda",'style'=>'width:340px;'])!!}
														</div>
													</div>
												</div>
												<div class="form-group" style="width:600px; display: inline;">
													{!!Form::label('tipoTransporteInventarioEDIInformacion', 'Tipo de Transporte', array('class' => 'col-sm-2 control-label','style'=>'width:180px;'))!!}
													<div class="col-sm-10" style="width:400px;">
														<div class="input-group">
															<span class="input-group-addon">
																<i class="fa fa-user" style="width: 14px;"></i>
															</span>
															{!!Form::select('tipoTransporteInventarioEDIInformacion',
															array('PIE'=>'A pie','BICICLETA'=>'Bicicleta','PUBLICO'=>'P&uacute;blico','MOTO'=>'Moto','CARRO'=>'Carro'),(isset($inventarioedi->inventarioediInformaciones) ? $inventarioedi->inventarioediInformaciones->tipoTransporteInventarioEDIInformacion : null),["class" => "js-example-placeholder-single js-states form-control", "placeholder" =>"Seleccione el tipo de transporte",'style'=>'width:340px;'])!!}
														</div>
													</div>
												</div>
												<div class="form-group" style="width:600px; display: inline;">
													{!!Form::label('HobbyInventarioEDIInformacion', 'Hobby', array('class' => 'col-sm-2 control-label','style'=>'width:180px;'))!!}
													<div class="col-sm-10" style="width:400px;">
														<div class="input-group">
															<span class="input-group-addon">
																<i class="fa fa-user" style="width: 14px;"></i>
															</span>
															{!!Form::text('HobbyInventarioEDIInformacion',(isset($inventarioedi->inventarioediInformaciones) ? $inventarioedi->inventarioediInformaciones->HobbyInventarioEDIInformacion : null),['class'=>'form-control','placeholder'=>'Digite el hobby','style'=>'width:340px;'])!!}
														</div>
													</div>
												</div>
												<div class="form-group" style="width:600px; display: inline;">
													{!!Form::label('actividadFisicaInventarioEDIInformacion', 'Actividad F&iacute;sica', array('class' => 'col-sm-2 control-label','style'=>'width:180px;'))!!}
													<div class="col-sm-10" style="width:400px;">
														<div class="input-group">
															<span class="input-group-addon">
																<i class="fa fa-user" style="width: 14px;"></i>
															</span>
															{!!Form::select('actividadFisicaInventarioEDIInformacion',
															array('1'=>'S&iacute;','0'=>'No'),(isset($inventarioedi->inventarioediInformaciones) ? $inventarioedi->inventarioediInformaciones->actividadFisicaInventarioEDIInformacion : null),["class" => "js-example-placeholder-single js-states form-control", "placeholder" =>"Seleccione si realiza actividad f&iacute;sica",'style'=>'width:340px;'])!!}
														</div>
													</div>
												</div>
												<div class="form-group" style="width:600px; display: inline;">
													{!!Form::label('consumeLicorInventarioEDIInformacion', 'Consume Licor', array('class' => 'col-sm-2 control-label','style'=>'width:180px;'))!!}
													<div class="col-sm-10" style="width:400px;">
														<div class="input-group">
															<span class="input-group-addon">
																<i class="fa fa-user" style="width: 14px;"></i>
															</span>
															{!!Form::select('consumeLicorInventarioEDIInformacion',
															array('1'=>'S&iacute;','0'=>'No'),(isset($inventarioedi->inventarioediInformaciones) ? $inventarioedi->inventarioediInformaciones->consumeLicorInventarioEDIInformacion : null),["class" => "js-example-placeholder-single js-states form-control", "placeholder" =>"Seleccione si consume licor",'style'=>'width:340px;'])!!}
														</div>
													</div>
												</div>
												<div class="form-group" style="width:600px; display: inline;">
													{!!Form::label('FrecuenciaMedicion_idConsumeLicor', 'Frecuencia', array('class' => 'col-sm-2 control-label','style'=>'width:180px;'))!!}
													<div class="col-sm-10" style="width:400px;">
														<div class="input-group">
															<span class="input-group-addon">
																<i class="fa fa-user" style="width: 14px;"></i>
															</span>
															{!!Form::select('FrecuenciaMedicion_idConsumeLicor',
															$frecuenciaAlcohol, (isset($inventarioedi->inventarioediInformaciones) ? $inventarioedi->inventarioediInformaciones->FrecuenciaMedicion_idConsumeLicor : null),["class" => "js-example-placeholder-single js-states form-control", "placeholder" =>"Seleccione la frencuencia del consumo de licor",'style'=>'width:340px;'])!!}
														</div>
													</div>
												</div>
												<div class="form-group" style="width:600px; display: inline;">
													{!!Form::label('consumeCigarrilloInventarioEDIInformacion', 'Consume Cigarrillo', array('class' => 'col-sm-2 control-label','style'=>'width:180px;'))!!}
													<div class="col-sm-10" style="width:400px;">
														<div class="input-group">
															<span class="input-group-addon">
																<i class="fa fa-user" style="width: 14px;"></i>
															</span>
															{!!Form::select('consumeCigarrilloInventarioEDIInformacion',
															array('1'=>'S&iacute;','0'=>'No'),(isset($inventarioedi->inventarioediInformaciones) ? $inventarioedi->inventarioediInformaciones->consumeCigarrilloInventarioEDIInformacion : null),["class" => "js-example-placeholder-single js-states form-control", "placeholder" =>"Seleccione si consume cigarrillo",'style'=>'width:340px;'])!!}
														</div>
													</div>
												</div>

											</div>
										</div>
									</div>
									<div class="panel panel-default">
										<div class="panel-heading">
											<h4 class="panel-title">
												<a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">Contactos</a>
											</h4>
										</div>
										<div id="collapseTwo" class="panel-collapse collapse">
											<div class="panel-body">
												<div class="form-group" id='test'>
													<div class="col-sm-12">
														<div class="row show-grid">
															<div class="col-md-1" style="width: 40px;" onclick="contactos.agregarCampos(valorContactos,'A')">
																<span class="glyphicon glyphicon-plus"></span>
															</div>
															<div class="col-md-1" style="width: 330px;">Nombre</div>
															<div class="col-md-1" style="width: 270px;">Cargo</div>
															<div class="col-md-1" style="width: 150px;">Tel&eacute;fono</div>
															<div class="col-md-1" style="width: 150px;">M&oacute;vil</div>
															<div class="col-md-1" style="width: 230px;">Correo</div>
															<div id="contenedor_contactos">
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div id="pestanaProducto" class="panel panel-default" style="display:none;">
										<div class="panel-heading">
											<h4 class="panel-title">
												<a data-toggle="collapse" data-parent="#accordion" href="#collapseThree">Productos y Servicios</a>
											</h4>
										</div>
										<div id="collapseThree" class="panel-collapse collapse">
											<div class="panel-body">
												<div class="form-group" id='test'>
													<div class="col-sm-12">
														<div class="row show-grid">
															<div class="col-md-1" style="width: 40px;" onclick="productos.agregarCampos(valorProductos,'A')">
																<span class="glyphicon glyphicon-plus"></span>
															</div>
															<div class="col-md-1" style="width: 380px;">Referencia</div>
															<div class="col-md-1" style="width: 750px;">Descripci&oacute;n</div>
															<div id="contenedor_productos">
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="panel panel-default">
										<div class="panel-heading">
											<h4 class="panel-title">
												<a data-toggle="collapse" data-parent="#accordion" href="#examen">Examenes M&eacute;dicos Requeridos</a>
											</h4>
										</div>
										<div id="examen" class="panel-collapse collapse">
											<div class="panel-body">
												<div class="form-group" id='test'>
													<div class="col-sm-12">
														<div class="row show-grid">
															<div class="col-md-1" style="width: 40px;height: 60px;" onclick="examen.agregarCampos(valorExamen,'A')">
																<span class="glyphicon glyphicon-plus"></span>
															</div>
															<div class="col-md-1" style="width: 300px;display:inline-block;height:60px;">Examen</div>
															<div class="col-md-1" style="width: 90px;display:inline-block;height:60px;">Ingreso</div>
															<div class="col-md-1" style="width: 90px;display:inline-block;height:60px;">Retiro</div>
															<div class="col-md-1" style="width: 90px;display:inline-block;height:60px;">Peri&oacute;dico</div>
															<div class="col-md-1" style="width: 300px;display:inline-block;height:60px;">Periodicidad</div>
															<div id="contenedor_examen">
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="panel panel-default">
										<div class="panel-heading">
											<h4 class="panel-title">
												<a data-toggle="collapse" data-parent="#archivos" href="#archivos">Archivos</a>
											</h4>
										</div>
										<div id="archivos" class="panel-collapse collapse">
											<div class="panel-body">
												<div class="form-group" id='test'>
													<div class="col-sm-12">
														<!-- <div class="row show-grid">
															<div class="col-md-1" style="width: 40px;height: 60px;" onclick="archivo.agregarCampos(valorArchivo,'A')">
																<span class="glyphicon glyphicon-plus"></span>
															</div>
															<div class="col-md-1" style="width: 300px;display:inline-block;height:60px;">T&iacute;tulo</div>
															<div class="col-md-1" style="width: 200px;display:inline-block;height:60px;">Fecha</div>
															<div class="col-md-1" style="width: 600px;display:inline-block;height:60px;">Ruta</div>
															<div id="contenedor_archivo">
															</div>
														</div> -->

														<div id="upload" class="col-md-4">
															<div class="input-group">  
															   <div class="form-group">
														        	<div class="input-group">
														            	<div class="dropzone dropzone-previews" id="dropzoneInventarioEDIArchivo"></div>  
														        	</div>
														    	</div>
															</div>
														</div>	
													</div>
												</div>
											</div>
											<center>
												<div style="border: 1px solid; width:80%; height:300px;">		
												<?php
												if ($idInventarioEDIA != '') 
												{
													$eliminar = '';
													$archivoSave = DB::Select('SELECT * from inventarioediarchivo where InventarioEDI_idInventarioEDI = '.$idInventarioEDIA);
													for ($i=0; $i <count($archivoSave) ; $i++) 
													{ 
														$archivoS = get_object_vars($archivoSave[$i]);

														echo '<div id="'.$archivoS['idInventarioEDIArchivo'].'" style="width:50%; height:50%; border:1px solid; float:left;"> <center>
														<a target="_blank" href="http://'.$_SERVER["HTTP_HOST"].'/imagenes'.$archivoS['rutaInventarioEDIArchivo'].'"><img src="http://'.$_SERVER["HTTP_HOST"].'/imagenes'.$archivoS['rutaInventarioEDIArchivo'].'"  width="25%"></a></center>';
														$eliminar .=$archivoS['idInventarioEDIArchivo'].','; 
														echo' <a style="cursor:pointer;" onclick="eliminarDiv(document.getElementById('.$archivoS['idInventarioEDIArchivo'].').id);">Borrar archivo</a>

														<input type="hidden" id="idInventarioEDIArchivo[]" name="idInventarioEDIArchivo[]" value="'.$archivoS['idInventarioEDIArchivo'].'" >

														<input type="hidden" id="tituloInventarioEDIArchivo[]" name="tituloInventarioEDIArchivo[]" value="'.$archivoS['tituloInventarioEDIArchivo'].'" >

														<input type="hidden" id="fechaInventarioEDIArchivo[]" name="fechaInventarioEDIArchivo[]" value="'.$archivoS['fechaInventarioEDIArchivo'].'" >

														
														<input type="hidden" id="rutaInventarioEDIArchivo[]" name="rutaInventarioEDIArchivo[]" value="'.$archivoS['rutaInventarioEDIArchivo'].'" ></div>';
													}

													echo '<input type="hidden" name="eliminarArchivo" id="eliminarArchivo" value="">';
												}
												
												 ?>							
												</div>
											</center>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-12">
						@if(isset($inventarioedi))
							{!!Form::submit(((isset($_GET['accion']) and $_GET['accion'] == 'eliminar') ? 'Eliminar' : 'Modificar'),["class"=>"btn btn-primary","onclick"=>'validarFormulario(event);'])!!}
						@else
							{!!Form::submit('Adicionar',["class"=>"btn btn-primary","onclick"=>'validarFormulario(event);'])!!}
						@endif
					</div>
				</div>			
			</fieldset>
		</br></br></br></br>
		</div>
		<input type="hidden" id="token" value="{{csrf_token()}}"/>

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Modal Header</h4>
      </div>
      <div class="modal-body">
      	<div class="form-group" style="width:565px; display: inline;">
			{!!Form::label('tituloInventarioEDIArchivo', 'Titulo', array('class' => 'col-sm-2 control-label','style'=>'width:180px;'))!!}
			<div class="col-sm-10" style="width:340px;">
				<div class="input-group">
					<span class="input-group-addon">
						<i class="fa fa-pencil-square-o" style="width: 14px;"></i>
					</span>
					{!!Form::text('tituloInventarioEDIArchivo',null,['class'=>'form-control','placeholder'=>'Ingresa el titulo del archivo','style'=>'width:300px;','id'=>'tituloInventarioEDIArchivo'])!!}
				</div>
			</div>
		</div>
		<?php $fechahoy = Carbon\Carbon::now();?>
		<div class="form-group" style="width:565px; display: inline;">
			{!!Form::label('fechaInventarioEDIArchivo', 'Fecha', array('class' => 'col-sm-2 control-label','style'=>'width:180px;'))!!}
			<div class="col-sm-10" style="width:340px;">
				<div class="input-group">
					<span class="input-group-addon">
						<i class="fa fa-pencil-square-o" style="width: 14px;"></i>
					</span>
					{!!Form::text('fechaInventarioEDIArchivo',$fechahoy->toDateTimeString() ,['class'=>'form-control','readonly','style'=>'width:300px;','id'=>'fechaInventarioEDIArchivo'])!!}
				</div>
			</div>
		</div>

		
		{!!Form::hidden('archivoInventarioEDI', 0, array('id' => 'archivoInventarioEDI'))!!}
		{!!Form::hidden('archivoInventarioEDIArray', '', array('id' => 'archivoInventarioEDIArray'))!!}

        <div id="preview">
       		<center><img id="viewer" frameborder="0" scrolling="no" width="60%" height="60%"></center>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Ok</button>
      </div>
    </div>
  </div>
</div>			
	{!!Form::close()!!}
	
	<script type="text/javascript">
		document.getElementById('contenedor').style.width = '1350px';
		document.getElementById('contenedor-fin').style.width = '1350px';
		 
		//mostrarPestanas();

        $('#fechaNacimientoInventarioEDIInformacion').datetimepicker(({
			format: "YYYY-MM-DD"
		}));

		$('#fechaIngresoInventarioEDIInformacion').datetimepicker(({
			format: "YYYY-MM-DD"
		}));

		$('#fechaRetiroInventarioEDIInformacion').datetimepicker(({
			format: "YYYY-MM-DD"
		}));

		
		$('#imagenInventarioEDI').fileinput({
			language: 'es',
			uploadUrl: '#',
			allowedFileExtensions : ['jpg', 'png','gif'],
			 initialPreview: [
			 '<?php if(isset($inventarioedi->imagenInventarioEDI))
						echo Html::image("imagenes/". $inventarioedi->imagenInventarioEDI,"Imagen no encontrada",array("style"=>"width:148px;height:158px;"));
							             ;?>'
            ],
			dropZoneTitle: 'Seleccione su foto',
			removeLabel: '',
			uploadLabel: '',
			browseLabel: '',
			uploadClass: "",
			uploadLabel: "",
			uploadIcon: "",
		});

	//--------------------------------- DROPZONE ---------------------------------------
	var baseUrl = "{{ url("/") }}";
    var token = "{{ Session::getToken() }}";
    Dropzone.autoDiscover = false;
    var myDropzone = new Dropzone("div#dropzoneInventarioEDIArchivo", {
        url: baseUrl + "/dropzone/uploadFiles",
        params: {
            _token: token
        },
        
    });

   	 fileList = Array();
   	var i = 0;

    //Configuro el dropzone
    myDropzone.options.myAwesomeDropzone =  {
    paramName: "file", // The name that will be used to transfer the file
    maxFilesize: 40, // MB
    addRemoveLinks: true,
    clickable: true,
    previewsContainer: ".dropzone-previews",
    clickable: false,
    uploadMultiple: true,
    accept: function(file, done) {

      }
    };
    //envio las funciones a realizar cuando se de clic en la vista previa dentro del dropzone
     myDropzone.on("addedfile", function(file) {
          file.previewElement.addEventListener("click", function(reg) {
            // abrirModal(file);
            // pos = fileList.indexOf(file["name"]);
            // alert(pos);
            // console.log(fileList[pos]);
            // $("#tituloInventarioEDIArchivo").val(fileList[pos]["titulo"]);
          });
        });

    document.getElementById('archivoInventarioEDIArray').value = '';
    myDropzone.on("success", function(file, serverFileName) {
    					abrirModal(file);
                        fileList[i] = {"serverFileName" : serverFileName, "fileName" : file.name,"fileId" : i, "titulo" : '' };
						// console.log(fileList);

                        document.getElementById('archivoInventarioEDIArray').value += file.name+',';
                        console.log(document.getElementById('archivoInventarioEDIArray').value);
                        i++;
                    });


    </script>

<style>
#dropzoneInventarioEDIArchivo {
width: 1150px;
height: 200px;
min-height: 0px !important;
}   
</style>    
@stop