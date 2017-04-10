@extends('layouts.vista')

@section('titulo')
	<h3 id="titulo">
		<center>Cargos</center>
	</h3>
@stop

@section('content')

	@include('alerts.request')
	{!!Html::script('js/cargo.js')!!}
	<script>
		var cargoTareaRiesgo = '<?php echo (isset($cargo) ? json_encode($cargo->cargoTareaRiesgos) : "");?>';
		cargoTareaRiesgo = (cargoTareaRiesgo != '' ? JSON.parse(cargoTareaRiesgo) : '');
		
		var cargoVacuna = '<?php echo (isset($cargo) ? json_encode($cargo->cargoVacunas) : "");?>';
		cargoVacuna = (cargoVacuna != '' ? JSON.parse(cargoVacuna) : '');

		var cargoElementoProteccion = '<?php echo (isset($cargo) ? json_encode($cargo->cargoElementoProtecciones) : "");?>';
		cargoElementoProteccion = (cargoElementoProteccion != '' ? JSON.parse(cargoElementoProteccion) : '');

		var cargoExamenMedico = '<?php echo (isset($cargo) ? json_encode($cargo->cargoExamenMedicos) : "");?>';
		cargoExamenMedico = (cargoExamenMedico != '' ? JSON.parse(cargoExamenMedico) : '');

		var cargoResponsabilidad = '<?php echo (isset($cargo) ? json_encode($cargo->CargoResponsabilidad) : "");?>';
		cargoResponsabilidad = (cargoResponsabilidad != '' ? JSON.parse(cargoResponsabilidad) : '');

		var cargoEducacion = '<?php echo (isset($cargoeducacion) ? json_encode($cargoeducacion) : "");?>';
		cargoEducacion = (cargoEducacion != '' ? JSON.parse(cargoEducacion) : '')

		var cargoFormacion = '<?php echo (isset($cargohabilidad) ? json_encode($cargoformacion) : "");?>';
		cargoFormacion = (cargoFormacion != '' ? JSON.parse(cargoFormacion) : '')

		var cargoHabilidad = '<?php echo (isset($cargohabilidad) ? json_encode($cargohabilidad) : "");?>';
		cargoHabilidad = (cargoHabilidad != '' ? JSON.parse(cargoHabilidad) : '')

		var cargocompetencia = '<?php echo (isset($cargocompetencia) ? json_encode($cargocompetencia) : "");?>';
		cargocompetencia = (cargocompetencia != '' ? JSON.parse(cargocompetencia) : '')
;

		var valorTarea = [0,''];
		var valorVacuna = [0,''];
		var valorElemento = [0,''];
		var valorExamen = [0,'',0,0,0,''];
		var ResponsabilidadesModelo = [0,'',0];
		

		var idListaTarea = '<?php echo isset($idListaTarea) ? $idListaTarea : 0;?>';
		var nombreListaTarea = '<?php echo isset($nombreListaTarea) ? $nombreListaTarea : "";?>';
		var idTipoExamen = '<?php echo isset($idTipoExamen) ? $idTipoExamen : 0;?>';
		var nombreTipoExamen = '<?php echo isset($nombreTipoExamen) ? $nombreTipoExamen : "";?>';
		var idListaElemento = '<?php echo isset($idListaElemento) ? $idListaElemento : 0;?>';
		var nombreListaElemento = '<?php echo isset($nombreListaElemento) ? $nombreListaElemento : "";?>';
		var idListaVacuna = '<?php echo isset($idListaVacuna) ? $idListaVacuna : 0;?>';
		var nombreListaVacuna = '<?php echo isset($nombreListaVacuna) ? $nombreListaVacuna : "";?>';
		
		var idFrecuenciaMedicion = '<?php echo isset($idFrecuenciaMedicion) ? $idFrecuenciaMedicion : 0;?>';
		var nombreFrecuenciaMedicion = '<?php echo isset($nombreFrecuenciaMedicion) ? $nombreFrecuenciaMedicion : "";?>';
		
		var listaTarea = [JSON.parse(idListaTarea),JSON.parse(nombreListaTarea)];
		var listaExamen = [JSON.parse(idTipoExamen),JSON.parse(nombreTipoExamen)];
		var listaElemento = [JSON.parse(idListaElemento),JSON.parse(nombreListaElemento)];
		var listaVacuna = [JSON.parse(idListaVacuna),JSON.parse(nombreListaVacuna)];
		var frecuenciaMedicion = [JSON.parse(idFrecuenciaMedicion),JSON.parse(nombreFrecuenciaMedicion)];
		// se crean dos variables para busar los datos y comprarlos con su funcion correspondiente
		//educacion
		//al final se agrega otro parametro para saber a que multiregistro esta. que es el nombre quemado para diferenciarlas en la funcion en  cargo.js
		var validacionesPesoE = ['onchange','validacionesPorcentajeEducacion(this.value);']
		var validacionesPesoF = ['onchange','validacionesPorcentajeFormacion(this.value);']
		var validacionesPesoH = ['onchange','validacionesPorcentajeHabilidad(this.value);']
		var validacionesPesoR = ['onchange','validacionesPorcentajeResponsabilidad(this.value);']
		// var validacionglobal = ['onchange','sumatoriatotal(this.value);']

		//aca se debe crear la variable que va a traer el id y el nombre quemado de la responsabilidad y se debe poner en la respectiva multiregistro



		$(document).ready(function()
		{
			tarea = new Atributos('tarea','contenedor_tarea','tarea');

			tarea.altura = '35px;';
			tarea.campoid = 'idCargoTareaRiesgo';
			tarea.campoEliminacion = 'eliminarTarea';

			tarea.campos = ['idCargoTareaRiesgo', 'ListaGeneral_idTareaAltoRiesgo'];
			tarea.etiqueta = ['input','select'];
			tarea.tipo = ['hidden',''];
			tarea.estilo = ['','width: 900px;height:35px;'];
			tarea.clase = ['',''];
			tarea.sololectura = [false,false];
			tarea.completar = ['off','off'];
			tarea.opciones = ['',listaTarea];
			tarea.funciones  = ['',''];

			vacuna = new Atributos('vacuna','contenedor_vacuna','vacuna');
			
			vacuna.altura = '35px;';
			vacuna.campoid = 'idCargoVacuna';
			vacuna.campoEliminacion = 'eliminarVacuna';

			vacuna.campos = ['idCargoVacuna', 'ListaGeneral_idVacuna'];
			vacuna.etiqueta = ['input','select'];
			vacuna.tipo = ['hidden',''];
			vacuna.estilo = ['','width: 900px;height:35px;'];
			vacuna.clase = ['',''];
			vacuna.sololectura = [false,false];
			vacuna.completar = ['off','off'];
			vacuna.opciones = ['',listaVacuna];
			vacuna.funciones  = ['',''];

			elemento = new Atributos('elemento','contenedor_elemento','elemento');

			elemento.altura = '35px;';
			elemento.campoid = 'idCargoElementoProteccion';
			elemento.campoEliminacion = 'eliminarElemento';

			elemento.campos = ['idCargoElementoProteccion', 'ElementoProteccion_idElementoProteccion'];
			elemento.etiqueta = ['input','select'];
			elemento.tipo = ['hidden',''];
			elemento.estilo = ['','width: 900px;height:35px;'];
			elemento.clase = ['',''];
			elemento.sololectura = [false,false];
			elemento.completar = ['off','off'];
			elemento.opciones = ['',listaElemento];
			elemento.funciones  = ['',''];

			examen = new Atributos('examen','contenedor_examen','examen');

			examen.altura = '36px;';
			examen.campoid = 'idCargoExamenMedico';
			examen.campoEliminacion = 'eliminarExamen';

			examen.campos = ['idCargoExamenMedico', 'TipoExamenMedico_idTipoExamenMedico','ingresoCargoExamenMedico','retiroCargoExamenMedico','periodicoCargoExamenMedico','FrecuenciaMedicion_idFrecuenciaMedicion'];
			examen.etiqueta = ['input','select','checkbox','checkbox','checkbox','select'];
			examen.tipo = ['hidden','','checkbox','checkbox','checkbox',''];
			examen.estilo = ['','width: 300px;height:35px;','width: 90px;height:30px;display:inline-block;','width: 90px;height:30px;display:inline-block;','width: 90px;height:30px;display:inline-block;','width: 300px;height:35px;'];
			examen.clase = ['','','','','',''];
			examen.sololectura = [false,false,false,false,false,false];
			examen.completar = ['off','off','off','off','off','off'];
			examen.opciones = ['',listaExamen,'','','',frecuenciaMedicion];
			examen.funciones  = ['','','','','',''];


				// multiregistro Responsabilidades

  			
			  Responsabilidades = new Atributos('Responsabilidades','Responsabilidad_Modulo','Responsabilidaddescripcion_');

			    Responsabilidades.campoid = 'idCargoResponsabilidad';  //hermanitas             
			    Responsabilidades.campoEliminacion = 'eliminarResponsabilidades';//hermanitas         Cuando se utilice la funcionalidad 
			    Responsabilidades.botonEliminacion = true;//hermanitas
			    // despues del punto son las propiedades que se le van adicionar al objeto
			    Responsabilidades.campos = ['idCargoResponsabilidad','descripcionCargoResponsabilidad','Cargo_idCargo','porcentajeCargoResponsabilidad']; //[arrays ]
			    Responsabilidades.altura = '35px;'; 
			     // correspondiente en el mismo orden del mismo array , no puede tener mas campos que los que esten definidos
			    Responsabilidades.etiqueta = ['input','input','input','input'];
			    Responsabilidades.tipo = ['hidden','','hidden','']; //tipo hidden - oculto para el usuario  y los otros quedan visibles ''
			    Responsabilidades.estilo = ['','width: 600px;height:35px;','','width: 100px;height:35px;'];	

			    // estas propiedades no son muy usadas PERO SON UTILES
			    
			    Responsabilidades.clase = ['','','',''];  //En esta propiedad se puede utilizar las clases , pueden ser de  boostrap  ejm: from-control o clases propias
			    Responsabilidades.sololectura = [false,false,false,false]; //es para que no le bloquee el campo al usuario para que este pueda digitar de lo contrario true 
			    Responsabilidades.completar = ['off','off','off','off']; //autocompleta 
			    
			    Responsabilidades.opciones = ['','','','']; // se utiliza cuando las propiedades de la etiqueta son tipo select 
			    Responsabilidades.funciones  = ['','','',validacionesPesoR]; // cositas mas especificas , ejemplo ; vaya a  propiedad etiqueta y cuando escriba referencia  trae la funcion  




			    // // multiregistro Educacion

  			
			    Educacion = new Atributos('Educacion','Educacion_Modulo','Educaciondescripcion_');

			    Educacion.campoid = 'idCargoEducacion';  //hermanitas             
			    Educacion.campoEliminacion = 'eliminarEducacion';//hermanitas         Cuando se utilice la funcionalidad 
			    Educacion.botonEliminacion = true;//hermanitas
			    // despues del punto son las propiedades que se le van adicionar al objeto
			    Educacion.campos = ['idCargoEducacion','PerfilCargo_idEducacion','nombreEducacion','porcentajeCargoEducacion']; //[arrays ]
			    Educacion.altura = '35px;'; 
			     // correspondiente en el mismo orden del mismo array , no puede tener mas campos que los que esten definidos
			    Educacion.etiqueta = ['input','input','input','input'];
			    Educacion.tipo = ['hidden','hidden','text','text']; //tipo hidden - oculto para el usuario  y los otros quedan visibles ''
			    Educacion.estilo = ['', '', 'width: 600px;height:35px;','width: 100px;height:35px;'];	

			    // estas propiedades no son muy usadas PERO SON UTILES
			    
			    Educacion.clase = ['','','',''];  //En esta propiedad se puede utilizar las clases , pueden ser de  boostrap  ejm: from-control o clases propias
			    Educacion.sololectura = [false,false,false,false]; //es para que no le bloquee el campo al usuario para que este pueda digitar de lo contrario true 
			    Educacion.completar = ['off','off','off','off']; //autocompleta 
			    
			    Educacion.opciones = ['','','','']; // se utiliza cuando las propiedades de la etiqueta son tipo select 
			    Educacion.funciones  = ['','','',validacionesPesoE]; // cositas mas especificas , ejemplo ; vaya a  propiedad etiqueta y cuando escriba referencia  trae la funcion  


    			// // multiregistro Formacion

  			
			    Formacion = new Atributos('Formacion','Formacion_Modulo','Formaciondescripcion_');

			    Formacion.campoid = 'idCargoFormacion';  //hermanitas             
			    Formacion.campoEliminacion = 'eliminarFormacion';//hermanitas         Cuando se utilice la funcionalidad 
			    Formacion.botonEliminacion = true;//hermanitas
			    // despues del punto son las propiedades que se le van adicionar al objeto
			    Formacion.campos = ['idCargoFormacion','PerfilCargo_idFormacion','nombreFormacion','porcentajeCargoFormacion']; //[arrays ]
			    Formacion.altura = '35px;'; 
			     // correspondiente en el mismo orden del mismo array , no puede tener mas campos que los que esten definidos
			    Formacion.etiqueta = ['input','input','input','input'];
			    Formacion.tipo = ['hidden','hidden','text','text']; //tipo hidden - oculto para el usuario  y los otros quedan visibles ''
			    Formacion.estilo = ['', '', 'width: 600px;height:35px;','width: 100px;height:35px;'];	

			    // estas propiedades no son muy usadas PERO SON UTILES
			    
			    Formacion.clase = ['','','',''];  //En esta propiedad se puede utilizar las clases , pueden ser de  boostrap  ejm: from-control o clases propias
			    Formacion.sololectura = [false,false,false,false]; //es para que no le bloquee el campo al usuario para que este pueda digitar de lo contrario true 
			    Formacion.completar = ['off','off','off','off']; //autocompleta 
			    
			    Formacion.opciones = ['','','','']; // se utiliza cuando las propiedades de la etiqueta son tipo select 
			    Formacion.funciones  = ['','','',validacionesPesoF]; // cositas mas especificas , ejemplo ; vaya a  propiedad etiqueta y cuando escriba referencia  trae la funcion  



    			// // multiregistro  Habilidad 

  			
			    Habilidad = new Atributos('Habilidad','Habilidad_Modulo','Habilidaddescripcion_');

			    Habilidad.campoid = 'idCargoHabilidad';  //hermanitas             
			    Habilidad.campoEliminacion = 'eliminarHabilidad';//hermanitas         Cuando se utilice la funcionalidad 
			    Habilidad.botonEliminacion = true;//hermanitas
			    // despues del punto son las propiedades que se le van adicionar al objeto
			    Habilidad.campos = ['idCargoHabilidad','PerfilCargo_idHabilidad','nombreHabilidad','porcentajeCargoHabilidad']; //[arrays ]
			    Habilidad.altura = '35px;'; 
			     // correspondiente en el mismo orden del mismo array , no puede tener mas campos que los que esten definidos
			    Habilidad.etiqueta = ['input','input','input','input'];
			    Habilidad.tipo = ['hidden','hidden','text','text']; //tipo hidden - oculto para el usuario  y los otros quedan visibles ''
			    Habilidad.estilo = ['', '', 'width: 600px;height:35px;','width: 100px;height:35px;'];	

			    // estas propiedades no son muy usadas PERO SON UTILES
			    
			    Habilidad.clase = ['','','',''];  //En esta propiedad se puede utilizar las clases , pueden ser de  boostrap  ejm: from-control o clases propias
			    Habilidad.sololectura = [false,false,false,false]; //es para que no le bloquee el campo al usuario para que este pueda digitar de lo contrario true 
			    Habilidad.completar = ['off','off','off','off']; //autocompleta 
			    
			    Habilidad.opciones = ['','','','']; // se utiliza cuando las propiedades de la etiqueta son tipo select 
			    Habilidad.funciones  = ['','','',validacionesPesoH]; // cositas mas especificas , ejemplo ; vaya a  propiedad etiqueta y cuando escriba referencia  trae la funcion  




	// // multiregistro  Competencias 

  			
			    Competencia = new Atributos('Competencia','Competencia_Modulo','Competenciadescripcion_');

			    Competencia.campoid = 'idCargoCompetencia';  //hermanitas             
			    Competencia.campoEliminacion = 'eliminarCompetencia';//hermanitas         Cuando se utilice la funcionalidad 
			    Competencia.botonEliminacion = true;//hermanitas
			    // despues del punto son las propiedades que se le van adicionar al objeto
			    Competencia.campos = ['idCargoCompetencia','Competencia_idCompetencia','nombreCompetencia','Cargo_idCargo']; //[arrays ]
			    Competencia.altura = '35px;'; 
			     // correspondiente en el mismo orden del mismo array , no puede tener mas campos que los que esten definidos
			    Competencia.etiqueta = ['input','input','input','input'];
			    Competencia.tipo = ['hidden','hidden','text','hidden']; //tipo hidden - oculto para el usuario  y los otros quedan visibles ''
			    Competencia.estilo = ['','','width: 600px;height:35px;',''];	

			    // estas propiedades no son muy usadas PERO SON UTILES
			    
			    Competencia.clase = ['','','',''];  //En esta propiedad se puede utilizar las clases , pueden ser de  boostrap  ejm: from-control o clases propias
			    Competencia.sololectura = [false,false,false,false]; //es para que no le bloquee el campo al usuario para que este pueda digitar de lo contrario true 
			    Competencia.completar = ['off','off','off','off']; //autocompleta 
			    
			    Competencia.opciones = ['','','','']; // se utiliza cuando las propiedades de la etiqueta son tipo select 
			    Competencia.funciones  = ['','','','']; // cositas mas especificas , ejemplo ; vaya a  propiedad etiqueta y cuando escriba referencia  trae la funcion  









			for(var j=0, k = cargoTareaRiesgo.length; j < k; j++)
			{
				tarea.agregarCampos(JSON.stringify(cargoTareaRiesgo[j]),'L');
			}

			for(var j=0, k = cargoVacuna.length; j < k; j++)
			{
				vacuna.agregarCampos(JSON.stringify(cargoVacuna[j]),'L');
			}

			for(var j=0, k = cargoElementoProteccion.length; j < k; j++)
			{
				elemento.agregarCampos(JSON.stringify(cargoElementoProteccion[j]),'L');
			}

			for(var j=0, k = cargoExamenMedico.length; j < k; j++)
			{
				examen.agregarCampos(JSON.stringify(cargoExamenMedico[j]),'L');
			}

			for(var j=0, k = cargoResponsabilidad.length; j < k; j++)
			{
				Responsabilidades.agregarCampos(JSON.stringify(cargoResponsabilidad[j]),'L');
			}


			for(var j=0, k = cargoEducacion.length; j < k; j++)
			{
				Educacion.agregarCampos(JSON.stringify(cargoEducacion[j]),'L');
			}

			for(var j=0, k = cargoFormacion.length; j < k; j++)
			{
				Formacion.agregarCampos(JSON.stringify(cargoFormacion[j]),'L');
			}

			for(var j=0, k = cargoHabilidad.length; j < k; j++)
			{
				Habilidad.agregarCampos(JSON.stringify(cargoHabilidad[j]),'L');
			}

			for(var j=0, k = cargocompetencia.length; j < k; j++)
			{
				Competencia.agregarCampos(JSON.stringify(cargocompetencia[j]),'L');
			}

		});

	</script>

	

	@if(isset($cargo))
		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
			{!!Form::model($cargo,['route'=>['cargo.destroy',$cargo->idCargo],'method'=>'DELETE'])!!}
		@else
			{!!Form::model($cargo,['route'=>['cargo.update',$cargo->idCargo],'method'=>'PUT'])!!}
		@endif
	@else
		{!!Form::open(['route'=>'cargo.store','method'=>'POST'])!!}
	@endif

		<div id="form_section">
			<fieldset id="cargo-form-fieldset">
				<div class="form-group" id='test'>
					{!!Form::label('codigoCargo', 'C&oacute;digo', array('class' => 'col-sm-2 control-label'))!!}
					<div class="col-sm-10">
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-pencil-square-o" style="width: 14px;"></i>
							</span>
							<input type="hidden" id="token" value="{{csrf_token()}}"/>
							{!!Form::text('codigoCargo', null, ['class'=>'form-control','placeholder'=>'Ingresa el c&oacute;digo','id' => 'codigoCargo'])!!}
							{!!Form::hidden('idCargo', null, array('id' => 'idCargo'))!!}
							{!!Form::hidden('eliminarTarea', '', array('id' => 'eliminarTarea'))!!}
					      	{!!Form::hidden('eliminarVacuna', '', array('id' => 'eliminarVacuna'))!!}
					      	{!!Form::hidden('eliminarElemento', '', array('id' => 'eliminarElemento'))!!}
					      	{!!Form::hidden('eliminarExamen', '', array('id' => 'eliminarExamen'))!!}
					      	{!!Form::hidden('eliminarResponsabilidades', '', array('id' => 'eliminarResponsabilidades'))!!}
					      	{!!Form::hidden('eliminarEducacion', '', array('id' => 'eliminarEducacion'))!!}
					      	{!!Form::hidden('eliminarFormacion', '', array('id' => 'eliminarFormacion'))!!}
					      	{!!Form::hidden('eliminarHabilidad', '', array('id' => 'eliminarHabilidad'))!!}
					      	{!!Form::hidden('eliminarCompetencia', '', array('id' => 'eliminarCompetencia'))!!}




						</div>
					</div>
				</div>
				<div class="form-group" id='test'>
					{!!Form::label('nombreCargo', 'Nombre', array('class' => 'col-sm-2 control-label'))!!}
					<div class="col-sm-10">
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-pencil-square-o" style="width: 14px;"></i>
							</span>
							{!!Form::text('nombreCargo',null,['class'=>'form-control','placeholder'=>'Ingresa el nombre'])!!}
						</div>
					</div>
				</div>
				<div class="form-group" id='test'>
					{!!Form::label('salarioBaseCargo', 'Salario Base', array('class' => 'col-sm-2 control-label'))!!}
					<div class="col-sm-10">
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-pencil-square-o" style="width: 14px;"></i>
							</span>
							{!!Form::text('salarioBaseCargo',null,['class'=>'form-control','placeholder'=>'Ingresa el salario'])!!}
						</div>
					</div>
				</div>
				<div class="form-group" id='test'>
					{!!Form::label('nivelRiesgoCargo', 'Nivel Riesgo', array('class' => 'col-sm-2 control-label'))!!}
					<div class="col-sm-10">
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-pencil-square-o" style="width: 14px;"></i>
							</span>
							<input type="hidden" id="token" value="{{csrf_token()}}"/>
							{!!Form::select('nivelRiesgoCargo',
							array('I'=>'Riesgo I', 'II'=>'Riesgo II', 'III'=>'Riesgo III', 'IV'=>'Riesgo IV', 'V'=>'Riesgo V',), (isset($cargo) ? $cargo->nivelRiesgoCargo : ''),["class" => "form-control", "placeholder" =>"Seleccione el nivel de riesgo"])!!}
						</div>
					</div>
				</div>

				<!--  Nuevos Cambios (Depende de , años de experiencia -->

				<!-- Depende De -->
					<div class="form-group" id='test'>
					{!!Form::label('Cargo_IdDepende', 'Depende De', array('class' => 'col-sm-2 control-label'))!!}
					<div class="col-sm-10">
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-pencil-square-o" style="width: 14px;"></i>
							</span>
						{!!Form::select('Cargo_IdDepende',$cargoPadre, (isset($cargo) ? $cargo->Cargo_IdDepende : 0),["class" => "select form-control", "placeholder" =>"Seleccione"])!!}
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="col-lg-12">
						<div class="panel panel-default">
							<div class="panel-heading">Detalles</div>
							<div class="panel-body">
								<div class="panel-group" id="accordion">
									<div class="panel panel-default">
										<div class="panel-heading">
											<h4 class="panel-title">
												<a data-toggle="collapse" data-parent="#accordion" href="#objetivo">Objetivos</a>
											</h4>
										</div>
										<div id="objetivo" class="panel-collapse collapse in">
											<div class="panel-body">
												<div class="form-group" id='test'>
													<div class="col-sm-10" style="width: 100%;">
														<div class="input-group">
															{!!Form::textarea('objetivoCargo',null,['class'=>'ckeditor','placeholder'=>'Ingresa los objetivos'])!!}
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="panel panel-default">
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
														<!-- nuevo campo para 	 peso educacion -->
													     <div class="form-group" id='test'>
															{!!Form::label('porcentajeEducacionCargo', '% Peso', array('class' => 'col-sm-1 control-label'))!!}
															<div class="col-sm-10">
																<div class="input-group">
																	<span class="input-group-addon">
																		<i class="fa fa-pencil-square-o" style="width: 14px;"></i>
																	</span>
																	{!!Form::text('porcentajeEducacionCargo',(isset($cargo) ? $cargo->porcentajeEducacionCargo : 20),['class'=>'form-control','placeholder'=>'Ingrese el % del peso'])!!}
																</div>
															</div>
														</div>
														
															<!-- Detalle responsabilidad -->
															<div class="form-group" id='test'>
														      <div class="col-sm-12">

														        <div class="row show-grid">
														          <div class="col-md-1" style="width:40px;height: 35px; cursor:pointer;" onclick="abrirModalEducacion();">
											                        <span class="glyphicon glyphicon-plus"></span>
											                      </div>
														          <div class="col-md-1" style="width: 600px;display:inline-block;height:35px;">Descripcion</div>

														         <div class="col-md-1" style="width: 100px;display:inline-block;height:35px;">% Peso</div>
														          <!-- este es el div para donde van insertando los registros --> 
														          <div id="Educacion_Modulo">
														          </div>
														        </div>
														      </div>
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
												<a data-toggle="collapse" data-parent="#accordion" href="#experiencia">Experiencia</a>
											</h4>
										</div>
										<div id="experiencia" class="panel-collapse collapse">
											<div class="panel-body">
												<div class="form-group" id='test'>
													<div class="col-sm-10" style="width: 100%;">
													<fieldset>
															<!-- Años de experiencia --> <!-- cambio -->
															<div class="form-group" id='test'  >
																{!!Form::label('aniosExperienciaCargo', 'Años de Experiencia', array('class' => 'col-sm-1 control-label'))!!}
																<div class="col-sm-10">
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-pencil-square-o" style="width: 14px;"></i>
																		</span>
																		{!!Form::text('aniosExperienciaCargo',null,['class'=>'form-control','placeholder'=>'Ingresa los años de experiencia'])!!}
																	</div>
																</div>
															</div>
																<!-- nuevo campo para 	Experiencia peso  -->
															 
															     <div class="form-group" id='test' style="display:inline-block";>
																	{!!Form::label('porcentajeExperienciaCargo', '% Peso', array('class' => 'col-sm-1 control-label'))!!}
																	<div class="col-sm-10">
																		<div class="input-group">
																			<span class="input-group-addon">
																				<i class="fa fa-pencil-square-o" ></i>
																			</span>
																			{!!Form::text('porcentajeExperienciaCargo',(isset($cargo) ? $cargo->porcentajeExperienciaCargo : 20),['class'=>'form-control','placeholder'=>'Ingrese el % peso Experiencia'])!!}
																		</div>
																	</div>
																</div>
															
														</fieldset>
															</br>
														 <div class="panel-body">
															<div class="input-group">
																{!!Form::textarea('experienciaCargo',null,['class'=>'ckeditor','placeholder'=>'Ingresa la experiencia'])!!}
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
												<a data-toggle="collapse" data-parent="#accordion" href="#formacion">Formaci&oacute;n</a>
											</h4>
										</div>
										<div id="formacion" class="panel-collapse collapse">
											<div class="panel-body">
												<div class="form-group" id='test'>
													<div class="col-sm-10" style="width: 100%;">
														<div class="input-group">
															

																<!-- nuevo campo para 	Formacion peso  -->
													     <div class="form-group" id='test'>
															{!!Form::label('porcentajeFormacionCargo', '% Peso', array('class' => 'col-sm-1 control-label'))!!}
															<div class="col-sm-10">
																<div class="input-group">
																	<span class="input-group-addon">
																		<i class="fa fa-pencil-square-o" style="width: 14px;"></i>
																	</span>
																	{!!Form::text('porcentajeFormacionCargo',(isset($cargo) ? $cargo->porcentajeFormacionCargo : 20),['class'=>'form-control','placeholder'=>'Ingrese el % del peso'])!!}
																</div>
															</div>
														</div>
																<!-- Detalle responsabilidad -->
															<div class="form-group" id='test'>
														      <div class="col-sm-12">

														        <div class="row show-grid">
														          <div class="col-md-1" style="width:40px;height: 35px; cursor:pointer;" onclick="abrirModalFormacion();">
											                        <span class="glyphicon glyphicon-plus"></span>
											                      </div>
														          <div class="col-md-1" style="width: 600px;display:inline-block;height:35px;">Descripcion</div>

														         <div class="col-md-1" style="width: 100px;display:inline-block;height:35px;">% Peso</div>
														          <!-- este es el div para donde van insertando los registros --> 
														          <div id="Formacion_Modulo">
														          </div>
														        </div>
														      </div>
														    </div> 
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<!-- cambio de orden aca va hacer habilidades propias del cargo -->
									<div class="panel panel-default">
										<div class="panel-heading">
											<h4 class="panel-title">
												<a data-toggle="collapse" data-parent="#accordion" href="#habilidad">Habilidades propias del cargo</a>
											</h4>
										</div>
										<div id="habilidad" class="panel-collapse collapse">
											<div class="panel-body">
												<div class="form-group" id='test'>
													<div class="col-sm-10" style="width: 100%;">
														<div class="input-group">
														<!-- nuevo campo para 	habilidad peso  -->
													     <div class="form-group" id='test'>
															{!!Form::label('porcentajeHabilidadCargo', '% Peso', array('class' => 'col-sm-1 control-label'))!!}
															<div class="col-sm-10">
																<div class="input-group">
																	<span class="input-group-addon">
																		<i class="fa fa-pencil-square-o" style="width: 14px;"></i>
																	</span>
																	{!!Form::text('porcentajeHabilidadCargo',(isset($cargo) ? $cargo->porcentajeHabilidadCargo : 20),['class'=>'form-control','placeholder'=>'Ingrese el % del peso'])!!}
																</div>
															</div>
														</div>
															<!-- Detalle Habilidad -->
															<div class="form-group" id='test'>
														      <div class="col-sm-12">

														        <div class="row show-grid">
														          <div class="col-md-1" style="width:40px;height: 35px; cursor:pointer;" onclick="abrirModalHabilidad();">
											                        <span class="glyphicon glyphicon-plus"></span>
											                      </div>
														          <div class="col-md-1" style="width: 600px;display:inline-block;height:35px;">Descripcion</div>

														         <div class="col-md-1" style="width: 100px;display:inline-block;height:35px;">% Peso</div>
														          <!-- este es el div para donde van insertando los registros --> 
														          <div id="Habilidad_Modulo">
														          </div>
														        </div>
														      </div>
														    </div> 
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<!-- Responsabilidades -->
									<div class="panel panel-default">
										<div class="panel-heading">
											<h4 class="panel-title">
												<a data-toggle="collapse" data-parent="#accordion" href="#responsabilidad">Responsabilidades</a>
											</h4>
										</div>


										<!-- cambio a una multiregistro para digitar manualmente por el usuario -->
										<div id="responsabilidad" class="panel-collapse collapse">
											<div class="panel-body">
												<div class="form-group" id='test'>
													<div class="col-sm-10" style="width: 100%;">
														<div class="input-group">
														<!-- nuevo campo para 	Experiencia peso  -->
															 
															     <div class="form-group" id='test'>
																	{!!Form::label('porcentajeResponsabilidadCargo', '% Peso ', array('class' => 'col-sm-2 control-label'))!!}
																	<div class="col-sm-10">
																		<div class="input-group">
																			<span class="input-group-addon">
																				<i class="fa fa-pencil-square-o" ></i>
																			</span>
																			{!!Form::text('porcentajeResponsabilidadCargo',(isset($cargo) ? $cargo->porcentajeResponsabilidadCargo : 20),['class'=>'form-control','placeholder'=>'Ingrese el % peso Experiencia'])!!}
																		</div>
																	</div>
																</div>
															<!-- Detalle responsabilidad -->
															<div class="form-group" id='test'>
														      <div class="col-sm-12">

														        <div class="row show-grid">
														          <div class="col-md-1" style="width: 40px;height: 35px;" onclick="Responsabilidades.agregarCampos(ResponsabilidadesModelo,'A')">
														            <span class="glyphicon glyphicon-plus"></span>
														          </div>
														          <div class="col-md-1" style="width: 600px;display:inline-block;height:35px;">Descripcion</div>
														          <div class="col-md-1" style="width: 100px;display:inline-block;height:35px;">% Peso</div>
														          <!-- este es el div para donde van insertando los registros --> 
														          <div id="Responsabilidad_Modulo">
														          </div>
														        </div>
														      </div>
														    </div>  
														</div>
													</div>
												</div>
											</div>
										</div>

									</div>

									<!-- Habilidades Actitudinales -->
										<div class="panel panel-default">
										<div class="panel-heading">
											<h4 class="panel-title">
												<a data-toggle="collapse" data-parent="#accordion" href="#Competencia">Habilidades Actitudinales</a>
											</h4>
										</div>
										<div id="Competencia" class="panel-collapse collapse">
											<div class="panel-body">
												<div class="form-group" id='test'>
													<div class="col-sm-10" style="width: 100%;">
														<div class="input-group">
															<!-- Detalle Habilidad -->
															<div class="form-group" id='test'>
														      <div class="col-sm-12">

														        <div class="row show-grid">
														          <div class="col-md-1" style="width:40px;height: 35px; cursor:pointer;" onclick="abrirModalCompetencia();">
											                        <span class="glyphicon glyphicon-plus"></span>
											                      </div>
														          <div class="col-md-1" style="width: 600px;display:inline-block;height:35px;">Habilidad Actitudinal</div>
														          <!-- este es el div para donde van insertando los registros --> 
														          <div id="Competencia_Modulo">
														          </div>
														        </div>
														      </div>
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
												<a data-toggle="collapse" data-parent="#accordion" href="#tema">Tareas de Alto Riesgo</a>
											</h4>
										</div>
										<div id="tema" class="panel-collapse collapse">
											<div class="panel-body">
												<div class="form-group" id='test'>
													<div class="col-sm-12">
														<div class="row show-grid">
															<div class="col-md-1" style="width: 40px;height: 60px;" onclick="tarea.agregarCampos(valorTarea,'A')">
																<span class="glyphicon glyphicon-plus"></span>
															</div>
															<div class="col-md-1" style="width: 900px;display:inline-block;height:60px;">Descripci&oacute;n</div>
															<div id="contenedor_tarea">
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
												<a data-toggle="collapse" data-parent="#accordion" href="#vacuna">Vacunas Requeridas</a>
											</h4>
										</div>
										<div id="vacuna" class="panel-collapse collapse">
											<div class="panel-body">
												<div class="form-group" id='test'>
													<div class="col-sm-12">
														<div class="row show-grid">
															<div class="col-md-1" style="width: 40px;height: 60px;" onclick="vacuna.agregarCampos(valorVacuna,'A')">
																<span class="glyphicon glyphicon-plus"></span>
															</div>
															<div class="col-md-1" style="width: 900px;display:inline-block;height:60px;">Descripci&oacute;n</div>
															<div id="contenedor_vacuna">
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
												<a data-toggle="collapse" data-parent="#accordion" href="#posicion">Posici&oacute;n Predominante (m&aacute;s del 60% de la jornada)</a>
											</h4>
										</div>
										<div id="posicion" class="panel-collapse collapse">
											<div class="panel-body">
												<div class="form-group" id='test'>
													<div class="col-sm-10" style="width: 100%;">
														<div class="input-group">
															{!!Form::textarea('posicionPredominanteCargo',null,['class'=>'ckeditor','placeholder'=>'Ingresa la posici&oacute;n predominante'])!!}
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="panel panel-default">
										<div class="panel-heading">
											<h4 class="panel-title">
												<a data-toggle="collapse" data-parent="#accordion" href="#restriccion">Restricciones para el cargo</a>
											</h4>
										</div>
										<div id="restriccion" class="panel-collapse collapse">
											<div class="panel-body">
												<div class="form-group" id='test'>
													<div class="col-sm-10" style="width: 100%;">
														<div class="input-group">
															{!!Form::textarea('restriccionesCargo',null,['class'=>'ckeditor','placeholder'=>'Ingresa las restricciones'])!!}
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="panel panel-default">
										<div class="panel-heading">
											<h4 class="panel-title">
												<a data-toggle="collapse" data-parent="#accordion" href="#elemento">Elementos de Protecci&oacute;n Personal</a>
											</h4>
										</div>
										<div id="elemento" class="panel-collapse collapse">
											<div class="panel-body">
												<div class="form-group" id='test'>
													<div class="col-sm-12">
														<div class="row show-grid">
															<div class="col-md-1" style="width: 40px;height: 60px;" onclick="elemento.agregarCampos(valorElemento,'A')">
																<span class="glyphicon glyphicon-plus"></span>
															</div>
															<div class="col-md-1" style="width: 900px;display:inline-block;height:60px;">Descripci&oacute;n</div>
															<div id="contenedor_elemento">
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
												<a data-toggle="collapse" data-parent="#accordion" href="#autoridad">Autoridades</a>
											</h4>
										</div>
										<div id="autoridad" class="panel-collapse collapse">
											<div class="panel-body">
												<div class="form-group" id='test'>
													<div class="col-sm-10" style="width: 100%;">
														<div class="input-group">
															{!!Form::textarea('autoridadesCargo',null,['class'=>'ckeditor','placeholder'=>'Ingresa las autoridades'])!!}
														</div>
													</div>
												</div>
											</div>
										</div>

									</div>
									
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-12">
						@if(isset($cargo))
							{!!Form::submit(((isset($_GET['accion']) and $_GET['accion'] == 'eliminar') ? 'Eliminar' : 'Modificar'),["class"=>"btn btn-primary","onclick"=>'validarFormulario(event);'])!!}
						@else
							{!!Form::submit('Adicionar',["class"=>"btn btn-primary","onclick"=>'validarFormulario(event);'])!!}
						@endif
						</br></br></br></br>
					</div>
				</div>
			</fieldset>
		</div>	
	{!!Form::close()!!}
@stop
<!-- Grid modal para  Educacion -->
<div id="ModalEducacion" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width:70%;">

    <!-- Modal content-->
    <div style="" class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Selecci&oacute;n de Perfil de Educación</h4>
      </div>
      <div class="modal-body">
      <?php 
        echo '<iframe style="width:100%; height:400px; " id="campos" name="campos" src="http://'.$_SERVER["HTTP_HOST"].'/educaciongridselect"></iframe>'
      ?>
      </div>
    </div>
  </div>
</div>

<!-- Grid modal para  Formacion -->
<div id="ModalFormacion" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width:70%;">

    <!-- Modal content-->
    <div style="" class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Selecci&oacute;n de Perfil de Formación</h4>
      </div>
      <div class="modal-body">
      <?php 
        echo '<iframe style="width:100%; height:400px; " id="campos" name="campos" src="http://'.$_SERVER["HTTP_HOST"].'/formaciongridselect"></iframe>'
      ?>
      </div>
    </div>
  </div>
</div>



<!-- Grid modal para  Habilidad -->
<div id="ModalHabilidad" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width:70%;">

    <!-- Modal content-->
    <div style="" class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Selecci&oacute;n de Perfil de Habilidades propias del Cargo</h4>
      </div>
      <div class="modal-body">
      <?php 
        echo '<iframe style="width:100%; height:400px; " id="campos" name="campos" src="http://'.$_SERVER["HTTP_HOST"].'/habiliadadgridselect"></iframe>'
      ?>
      </div>
    </div>
  </div>
</div>


<!-- Grid modal para  Competencia -->
<div id="ModalCompetencia" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width:70%;">

    <!-- Modal content-->
    <div style="" class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Selecci&oacute;n de Habilidades actitudinales</h4>
      </div>
      <div class="modal-body">
      <?php 
        echo '<iframe style="width:100%; height:400px; " id="campos" name="campos" src="http://'.$_SERVER["HTTP_HOST"].'/competenciagridselect"></iframe>'
      ?>
      </div>
    </div>
  </div>
</div>



