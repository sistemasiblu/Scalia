@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Documentos CRM</center></h3>@stop

@section('content')
@include('alerts.request')

{!!Html::script('js/documentocrm.js')!!}

<script>

    var documentocrmcampo = '<?php echo (isset($documentocrm) ? json_encode($documentocrm->documentocrmcampo) : "");?>';
    documentocrmcampo = (documentocrmcampo != '' ? JSON.parse(documentocrmcampo) : '');

    var documentocrmgrafico = '<?php echo (isset($documentocrm) ? json_encode($documentocrm->documentocrmgrafico) : "");?>';
    documentocrmgrafico = (documentocrmgrafico != '' ? JSON.parse(documentocrmgrafico) : '');

    var documentocrmrol = '<?php echo (isset($documentocrm) ? json_encode($documentocrm->documentocrmrol) : "");?>';
    documentocrmrol = (documentocrmrol != '' ? JSON.parse(documentocrmrol) : '');

    var valoresGrafico = [0,'','','',''];
    $(document).ready(function(){

      protCampos = new Atributos('protCampos','contenedor_protCampos','documentocrmcampo');

      protCampos.altura = '35px';
      protCampos.campoid = 'idDocumentoCRMCampo';
      protCampos.campoEliminacion = 'eliminarDocumentoCRMCampo';

      protCampos.campos   = [
      'idDocumentoCRMCampo',
      'CampoCRM_idCampoCRM',
      'descripcionCampoCRM',
      'mostrarGridDocumentoCRMCampo', 
      'mostrarVistaDocumentoCRMCampo', 
      'obligatorioDocumentoCRMCampo', 
      'solicitanteDocumentoCRMCampo', 
      'asesorDocumentoCRMCampo', 
      'aprobadorDocumentoCRMCampo'
      ];

      protCampos.etiqueta = [
      'input',
      'input',
      'input',
      'checkbox',
      'checkbox',
      'checkbox',
      'checkbox',
      'checkbox',
      'checkbox'
      ];

      protCampos.tipo = [
      'hidden',
      'hidden',
      'text',
      'checkbox',
      'checkbox',
      'checkbox',
      'checkbox',
      'checkbox',
      'checkbox'
      ];

      protCampos.estilo = [
      '',
      '',
      'width: 260px;height:35px;',
      'width: 100px;height:35px; display:inline-block;',
      'width: 100px;height:35px; display:inline-block;',
      'width: 100px;height:35px; display:inline-block;',
      'width: 100px;height:35px; display:inline-block;',
      'width: 100px;height:35px; display:inline-block;',
      'width: 100px;height:35px; display:inline-block;'
      ];

      protCampos.clase    = ['','','','','','','','',''];
      protCampos.sololectura = [true,true,true,false,false,false,false,false,false];  
      protCampos.funciones = ['','','','','','','','',''];
      protCampos.completar = ['off','off','off','off','off','off','off','off','off'];
      protCampos.opciones = ['','','','','','','','',''];

      for(var j=0, k = documentocrmcampo.length; j < k; j++)
      {
        protCampos.agregarCampos(JSON.stringify(documentocrmcampo[j]),'L');

        llenarDatosCampo($('#CampoCRM_idCampoCRM'+j).val(), j);
      }

      protGraficos = new Atributos('protGraficos','contenedor_protGraficos','documentocrmgrafico');

      protGraficos.altura = '35px';
      protGraficos.campoid = 'idDocumentoCRMGrafico';
      protGraficos.campoEliminacion = 'eliminarDocumentoCRMGrafico';

      protGraficos.campos   = [
      'idDocumentoCRMGrafico',
      'serieDocumentoCRMGrafico',
      'tituloDocumentoCRMGrafico',
      'tipoDocumentoCRMGrafico', 
      'valorDocumentoCRMGrafico'
      ];

      protGraficos.etiqueta = [
      'input',
      'select',
      'input',
      'select',
      'select'
      ];

      protGraficos.tipo = [
      'hidden',
      '',
      'text',
      '',
      ''
      ];

      protGraficos.estilo = [
      '',
      'width: 200px;height:35px;',
      'width: 400px;height:35px;',
      'width: 200px;height:35px;',
      'width: 200px;height:35px;'
      ];

      tipoGrafico = [['Barras','Dona','Linea'],['Barras','Dona','Linea']];
      serieGrafico = [['estadocrm','categoriacrm','lineanegocio','eventocrm','origencrm'],['Estado','Categoría','Línea de Negocio','Evento/Campaña', 'Origen']];
      valorGrafico = [['Valor','Cantidad'],['Valor','Cantidad']];

      protGraficos.clase    = ['','','','',''];
      protGraficos.sololectura = [true,false,false,false,false];  
      protGraficos.funciones = ['','','','',''];
      protGraficos.completar = ['off','off','off','off','off'];
      protGraficos.opciones = ['',serieGrafico,'',tipoGrafico,valorGrafico];

      for(var j=0, k = documentocrmgrafico.length; j < k; j++)
      {
        protGraficos.agregarCampos(JSON.stringify(documentocrmgrafico[j]),'L');
      }



      


      permisos = new Atributos('permisos','contenedor_permisos','documentocrmrol');

      permisos.altura = '35px';
      permisos.campoid = 'idDocumentoCRMRol';
      permisos.campoEliminacion = 'eliminarDocumentoCRMRol';

      permisos.campos   = [
      'Rol_idRol',
      'nombreRol',
      'idDocumentoCRMRol',
      'adicionarDocumentoCRMRol',
      'modificarDocumentoCRMRol',
      'consultarDocumentoCRMRol',
      'anularDocumentoCRMRol',
      'aprobarDocumentoCRMRol'
      ];

      permisos.etiqueta = [
      'input',
      'input',
      'input',
      'checkbox',
      'checkbox',
      'checkbox',
      'checkbox',
      'checkbox'
      ];

      permisos.tipo = [
      'hidden',
      'text',
      'hidden',
      'checkbox',
      'checkbox',
      'checkbox',
      'checkbox',
      'checkbox'
      ];

      permisos.estilo = [
      '',
      'width: 530px;height:35px;',
      '',
      'width: 70px;height:35px; display:inline-block;',
      'width: 70px;height:35px; display:inline-block;',
      'width: 70px;height:35px; display:inline-block;',
      'width: 70px;height:35px; display:inline-block;',
      'width: 70px;height:35px; display:inline-block;'
      ];

      permisos.clase    = ['','','','','','','',''];
      permisos.sololectura = [true,true,true,true,true,true,true,true];  
      permisos.funciones = ['','','','','','','',''];
      permisos.completar = ['off','off','off','off','off','off','off','off'];
      permisos.opciones = ['','','','','','','','']

      for(var j=0, k = documentocrmrol.length; j < k; j++)
      {
        permisos.agregarCampos(JSON.stringify(documentocrmrol[j]),'L');

        llenarDatosRol($('#Rol_idRol'+j).val(), j);
      }
        
    });

 </script>

	@if(isset($documentocrm))
		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
			{!!Form::model($documentocrm,['route'=>['documentocrm.destroy',$documentocrm->idDocumentoCRM],'method'=>'DELETE'])!!}
		@else
			{!!Form::model($documentocrm,['route'=>['documentocrm.update',$documentocrm->idDocumentoCRM],'method'=>'PUT'])!!}
		@endif
	@else
		{!!Form::open(['route'=>'documentocrm.store','method'=>'POST'])!!}
	@endif
		<div id='form-section' >
				<fieldset id="documentocrm-form-fieldset">	
					<div class="form-group" id='test'>
						{!!Form::label('codigoDocumentoCRM', 'C&oacute;digo', array('class' => 'col-sm-2 control-label'))!!}
						<div class="col-sm-10">
				            <div class="input-group">
				              	<span class="input-group-addon">
				                	<i class="fa fa-barcode"></i>
				              	</span>
				              	<input type="hidden" id="token" value="{{csrf_token()}}"/>
								{!!Form::text('codigoDocumentoCRM',null,['class'=>'form-control','placeholder'=>'Ingresa el código de la Línea'])!!}
						      	{!!Form::hidden('idDocumentoCRM', null, array('id' => 'idDocumentoCRM'))!!}

						      	{!!Form::hidden('eliminarDocumentoCRMCampo', '', array('id' => 'eliminarDocumentoCRMCampo'))!!}
						      	{!!Form::hidden('eliminarDocumentoCRMCompania', '', array('id' => 'eliminarDocumentoCRMCompania'))!!}
						      	{!!Form::hidden('eliminarDocumentoCRMRol', '', array('id' => 'eliminarDocumentoCRMRol'))!!}

							</div>
						</div>
					</div>
					<div class="form-group" id='test'>
						{!!Form::label('nombreDocumentoCRM', 'Nombre', array('class' => 'col-sm-2 control-label'))!!}
						<div class="col-sm-10">
				            <div class="input-group">
				              	<span class="input-group-addon">
				                	<i class="fa fa-pencil-square-o"></i>
				              	</span>
								{!!Form::text('nombreDocumentoCRM',null,['class'=>'form-control','placeholder'=>'Ingresa el nombre de la Línea'])!!}
				    		</div>
				    	</div>
				    </div>	
				    <div class="form-group" >
			          {!!Form::label('tipoDocumentoCRM', 'Tipo', array('class' => 'col-sm-2 control-label'))!!}
			          <div class="col-sm-10" >
			            <div class="input-group">
			              <span class="input-group-addon">
			                <i class="fa fa-credit-card" ></i>
			              </span>
			              {!!Form::select('tipoDocumentoCRM',
            				array('HelpDesk'=>'HelpDesk','Comercial'=>'Comercial','Gestion Humana'=>'Gestión Humana'), (isset($documentocrm) ? $documentocrm->tipoDocumentoCRM : 0),["class" => "chosen-select form-control", "placeholder" =>"Seleccione el tipo de Documento"])!!}
			            </div>
			          </div>
			        </div>

			        <div class="form-group" >
			          {!!Form::label('GrupoEstado_idGrupoEstado', 'Estados', array('class' => 'col-sm-2 control-label'))!!}
			          <div class="col-sm-10" >
			            <div class="input-group">
			              <span class="input-group-addon">
			                <i class="fa fa-credit-card" ></i>
			              </span>
			              {!!Form::select('GrupoEstado_idGrupoEstado',
            				$grupoestado, (isset($documentocrm) ? $documentocrm->GrupoEstado_idGrupoEstado : 0),["class" => "chosen-select form-control", "placeholder" =>"Seleccione el grupo de estados"])!!}
			            </div>
			          </div>
			        </div>
				
					<div class="form-group">
			          <div class="col-lg-12">
			            <div class="panel panel-default">
			              <div class="panel-heading">Configuración</div>
			              <div class="panel-body">
			                <div class="panel-group" id="accordion">

			                  <div class="panel panel-default">
			                    <div class="panel-heading">
			                      <h4 class="panel-title">
			                        <a data-toggle="collapse" data-parent="#accordion" href="#numeracion">Numeración</a>
			                      </h4>
			                    </div>
			                    <div id="numeracion" class="panel-collapse collapse in">
			                      <div class="panel-body">
			                        
									<div class="form-group" >
						          	{!!Form::label('numeracionDocumentoCRM', 'Tipo Numeración', array('class' => 'col-sm-2 control-label'))!!}
						          	<div class="col-sm-4" >
						            	<div class="input-group">
							              <span class="input-group-addon">
							                <i class="fa fa-credit-card" ></i>
							              </span>
											{!!Form::select('numeracionDocumentoCRM',
				            				array('Automatica'=>'Automática','Manual'=>'Manual'), 
				            				(isset($documentocrm) ? $documentocrm->numeracionDocumentoCRM : 'Automatica'),
				            				["class" => "chosen-select form-control", "placeholder" =>"Seleccione el tipo de Numeración"])!!}
							            </div>
							          </div>
							        </div>


		                          <div class="form-group" >
						          	{!!Form::label('longitudDocumentoCRM', 'Longitud', array('class' => 'col-sm-2 control-label'))!!}
						          	<div class="col-sm-4" >
						            	<div class="input-group">
							              <span class="input-group-addon">
							                <i class="fa fa-credit-card" ></i>
							              </span>
							              {!!Form::text('longitudDocumentoCRM',null,['class'=>'form-control','placeholder'=>'Ingresa Longitud de numeración'])!!}
							            </div>
							          </div>
							        </div>

									<div class="form-group" >
						          		{!!Form::label('desdeDocumentoCRM', 'Desde', array('class' => 'col-sm-2 control-label'))!!}
						          		<div class="col-sm-4" >
						            		<div class="input-group">
								              <span class="input-group-addon">
								                <i class="fa fa-credit-card" ></i>
								              </span>
								              {!!Form::text('desdeDocumentoCRM',null,['class'=>'form-control','placeholder'=>'Ingresa numeración inicial'])!!}
							            	</div>
							          	</div>
							        </div>

									<div class="form-group" >
						          		{!!Form::label('hastaDocumentoCRM', 'Hasta', array('class' => 'col-sm-2 control-label'))!!}
						          		<div class="col-sm-4" >
						            		<div class="input-group">
								              <span class="input-group-addon">
								                <i class="fa fa-credit-card" ></i>
								              </span>
								              {!!Form::text('hastaDocumentoCRM',null,['class'=>'form-control','placeholder'=>'Ingresa numeración final'])!!}
							            	</div>
							          	</div>
							        </div>

			                         
			                      </div> 
			                    </div>
			                  </div>

			                  <div class="panel panel-default">
			                    <div class="panel-heading">
			                      <h4 class="panel-title">
			                        <a data-toggle="collapse" data-parent="#accordion" href="#contenido">Contenido</a>
			                      </h4>
			                    </div>
			                    <div id="contenido" class="panel-collapse collapse in">
			                      <div class="panel-body">
			                        
								    <div class="panel-body">
								      <div class="form-group" id='test'>
								        <div class="col-sm-12">
								          <div class="panel-body" >
								            <div class="form-group" id='test'>
								              <div class="col-sm-12">
								                <div class="row show-grid" style=" border: 1px solid #C0C0C0;">
								                  <div style="overflow:auto; height:350px;">
								                    <div style="width: 100%; display: inline-block;">
								                      <div class="col-md-1" style="width:40px;height: 42px; cursor:pointer;" onclick="abrirModalCampos();">
								                        <span class="glyphicon glyphicon-plus"></span>
								                      </div>
								                      <div class="col-md-1" style="width: 260px;" >Campo</div>
								                      <div class="col-md-1" style="width: 100px;" >Consulta</div>
								                      <div class="col-md-1" style="width: 100px;" >Formulario</div>
								                      <div class="col-md-1" style="width: 100px;" >Obligatorio</div>
								                      <div class="col-md-1" style="width: 100px;" >Solicitante</div>
								                      <div class="col-md-1" style="width: 100px;" >Asesor</div>
								                      <div class="col-md-1" style="width: 100px;" >Aprobador</div>
								                      <div id="contenedor_protCampos">
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
			                    </div>
			                  </div>

			                  <div class="panel panel-default">
			                    <div class="panel-heading">
			                      <h4 class="panel-title">
			                        <a data-toggle="collapse" data-parent="#accordion" href="#graficos">Gráficos</a>
			                      </h4>
			                    </div>
			                    <div id="graficos" class="panel-collapse collapse in">
			                      <div class="panel-body">
			                        
								    <div class="panel-body">
								      <div class="form-group" id='test'>
								        <div class="col-sm-12">
								          <div class="panel-body" >
								            <div class="form-group" id='test'>
								              <div class="col-sm-12">
								                <div class="row show-grid" style=" border: 1px solid #C0C0C0;">
								                  <div style="overflow:auto; height:350px;">
								                    <div style="width: 100%; display: inline-block;">
								                      <div class="col-md-1" style="width:40px;height: 42px; cursor:pointer;" onclick="protGraficos.agregarCampos(valoresGrafico,'A');">
								                        <span class="glyphicon glyphicon-plus"></span>
								                      </div>
								                      <div class="col-md-1" style="width: 200px;" >Serie</div>
								                      <div class="col-md-1" style="width: 400px;" >Título del Gráfico</div>
								                      <div class="col-md-1" style="width: 200px;" >Tipo</div>
								                      <div class="col-md-1" style="width: 200px;" >Valor</div>
								                      <div id="contenedor_protGraficos">
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
			                    </div>
			                  </div>
							<div class="panel panel-default">
			                    <div class="panel-heading">
			                      <h4 class="panel-title">
			                        <a data-toggle="collapse" data-parent="#accordion" href="#permisos">Permisos</a>
			                      </h4>
			                    </div>
			                    <div id="permisos" class="panel-collapse collapse in">
			                      <div class="panel-body">
			                        

			                <ul class="nav nav-tabs">
							  <li class="active"><a data-toggle="tab" href="#permRol">Roles</a></li>
							</ul>


							<div class="tab-content">
		                         
			                        
							
  								<div id="permRol" class="tab-pane fade  in active">
			                        <div class="panel-body" >
								      <div class="form-group" id='test'>
								        <div class="col-sm-12">
								          <div class="panel-body" >
								            <div class="form-group" id='test'>
								              <div class="col-sm-12">
								                <div class="row show-grid" style=" border: 1px solid #C0C0C0;">
								                  <div style="overflow:auto; height:350px;">
								                    <div style="width: 100%; display: inline-block;">
								                      <div class="col-md-1" style="width:40px;height: 42px; cursor:pointer;" onclick="abrirModalRol();">
								                        <span class="glyphicon glyphicon-plus"></span>
								                      </div>
								                      <div class="col-md-1" style="width: 530px;" >Rol</div>
								                      <div class="col-md-1" style="width: 70px;height: 42px; cursor:pointer;"><center><span title="Adicionar" class="fa fa-plus"></span></center></div>
									                <div class="col-md-1" style="width: 70px;height: 42px; cursor:pointer;"><center><span title="Modificar" class="fa fa-pencil"></span></center></div>
									                <div class="col-md-1" style="width: 70px;height: 42px; cursor:pointer;"><center><span title="Consultar" class="fa fa-search"></span></center></div>
									                <div class="col-md-1" style="width: 70px;height: 42px; cursor:pointer;"><center><span title="Anular" class="fa fa-trash"></span></center></div>
									                <div class="col-md-1" style="width: 70px;height: 42px; cursor:pointer;"><center><span title="Aprobar" class="fa fa-check"></span></center></div>
								                      
								                      <div id="contenedor_permisos">
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
							</div>

			                      </div> 
			                    </div>
			                  </div>

			                  
			                </div>
			              </div>
			            </div>
			          </div>
			        </div>	



				</fieldset>	




				@if(isset($documentocrm))
					{!!Form::submit(((isset($_GET['accion']) and $_GET['accion'] == 'eliminar') ? 'Eliminar' : 'Modificar'),["class"=>"btn btn-primary"])!!}
				@else
  					{!!Form::submit('Adicionar',["class"=>"btn btn-primary"])!!}
 				@endif
		</div>


	{!!Form::close()!!}		
		<!-- Modal -->

@stop
<div id="ModalCampos" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width:70%;">

    <!-- Modal content-->
    <div style="" class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Selecci&oacute;n de Campos</h4>
      </div>
      <div class="modal-body">
      <?php 
        echo '<iframe style="width:100%; height:400px; " id="campos" name="campos" src="http://'.$_SERVER["HTTP_HOST"].'/campocrmgridselect"></iframe>'
      ?>
      </div>
    </div>
  </div>
</div>

<div id="myModalCompania" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width:70%;">

    <!-- Modal content-->
    <div style="" class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Selecci&oacute;n de Compañías</h4>
      </div>
      <div class="modal-body">
      <?php 
        echo '<iframe style="width:100%; height:400px; " id="companias" name="companias" src="http://'.$_SERVER["HTTP_HOST"].'/companiagridselect"></iframe>'
      ?>
      </div>
    </div>
  </div>
</div>

<div id="myModalRol" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width:70%;">

    <!-- Modal content-->
    <div style="" class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Selecci&oacute;n de Roles</h4>
      </div>
      <div class="modal-body">
      <?php 
        echo '<iframe style="width:100%; height:400px; " id="roles" name="roles" src="http://'.$_SERVER["HTTP_HOST"].'/rolgridselect"></iframe>'
      ?>
      </div>
    </div>
  </div>
</div>