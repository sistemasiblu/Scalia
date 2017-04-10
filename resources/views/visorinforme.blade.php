<?php 
	// existen 3 modos (C= COntable, G=General, F=Full)
	$modo  = (isset($_GET["modo"]) ? $_GET["modo"] : 'G');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Visor de Informes</title>
    <!-- Librerías de Jquery y Jquery-ui -->
	{!! Html::script('jquery/jquery-2-2-3.min.js'); !!}
	{!! Html::script('jquery/jquery-ui-1-11-14.min.js'); !!}
	{!!Html::style('jquery/jquery-ui-1-11-4.css'); !!}

	<!-- Librerías de Font Awesome (iconos) -->
    {!!Html::style('assets/font-awesome-v4.3.0/css/font-awesome.min.css'); !!}

    
	<!-- Librerías de Bootstrap -->
	{!!Html::style('assets/bootstrap-v3.3.5/css/bootstrap.min.css'); !!}
    {!!Html::script('assets/bootstrap-v3.3.5/js/bootstrap.min.js'); !!}	
    
    <!-- Date time picker de bootstrap -->
    {!!Html::style('sb-admin/bower_components/datetimepicker/css/bootstrap-datetimepicker.min.css'); !!}
    {!!Html::script('sb-admin/bower_components/datetimepicker/js/moment.js'); !!}
    {!!Html::script('sb-admin/bower_components/datetimepicker/js/bootstrap-datetimepicker.min.js'); !!}
      

    <!-- Librerías para el selector de colores (color Picker) -->
    {!! Html::style('assets/colorpicker/css/bootstrap-colorpicker.min.css'); !!}
    {!! Html::script('assets/colorpicker/js/bootstrap-colorpicker.js'); !!}

    <!-- Librerías del proyecto de diseñador de informes -->
  	{!! Html::script('js/visorinforme.js'); !!}
  	{!! Html::script('js/general.js'); !!}
	  <!-- {!! Html::script('js/disenadorinforme.js'); !!} -->
    
    {!! Html::style('css/scalia-ui.css'); !!}
    {!! Html::style('css/segmented-controls.css'); !!}

    {!!Html::script('DataTables/media/js/jquery.dataTables.js'); !!}
    {!!Html::style('DataTables/media/css/jquery.dataTables.min.css'); !!}

    

<script type="text/javascript">

	var modo = "<?php echo $modo;?>";
	  var valorinformecondicion = ['','','','','',''];
        
      var parentesisAbre = [["", "(", "((", "(((", "(((("], ["", "(", "((", "(((", "(((("]];
      var parentesisCierra = [["", ")", "))", ")))", "))))"], ["", ")", "))", ")))", "))))"]];

      var operador = [["=", ">", ">=", 
      					"<", "<=", 
      					"likei", "likef",
      					"like", "not like",	"in"],
                      ["Igual a", "Mayor que", "Mayor o igual", 
                      "Menor que", "Menor o igual que", 
                      "Comienza por", "Termina por", 
                      "Contiene", "No contiene", "En la lista"]];
      var campos = [[],[]];
      var conector =  [["AND", "OR"], ["Y", "O"]];

      informecondicion = new Atributos('informecondicion','contenedor_informecondicion','informecondicion_');
      informecondicion.campos   = ['parentesisInicioInformeCondicion', 'campoInformeCondicion', 'operadorInformeCondicion','valorInformeCondicion','parentesisFinInformeCondicion','conectorInformeCondicion'];
      informecondicion.etiqueta = ['select', 'select','select','input','select','select'];
      informecondicion.tipo     = ['','','','text','',''];
      informecondicion.opciones = [parentesisAbre,campos,operador,'',parentesisCierra,conector];
      informecondicion.estilo   = ['width: 100px;height:35px;','width: 280px;height:35px;','width: 160px;height:35px;','width: 190px;height:35px;','width: 100px;height:35px;','width: 100px;height:35px;'];
      informecondicion.clase    = ['','','','','',''];
      informecondicion.sololectura = [false,false,false,false,false,false];


</script>
</head>
<body>

<div class="clearfix">
	<!-- Token para ejecuciones de ajax -->
	<input type="hidden" id="token" value="{{csrf_token()}}"/>

	<!-- 
	Creamos los paneles del formulario de la siguiente forma

	------------------------------------------------------
	|            |               |                       |
	|            |               |                       |
	|            |               |                       |
	|            |               |                       |
	|            |               |                       |
	| id: panel  | id: panel     |  id: panel            |
	| Izquierdo  |     Centro    |      Derecho          |
	|            |               |                       |
	|            |               |                       |
	|            |               |                       |
	|            |               |                       |
	-----------------------------------------------------|


	 -->
	<div id="panelIzquierda"   style="position:absolute; width: 15%; top: 0px; left: 0px;">
				

			<div class="panel panel-primary" >
			  <div class="panel-heading">
			    <h5 class="panel-title" style="font-size:16px;">
			      Categorías
		     		<a style="float:right;" href="javascript:editarCategoriaInforme('insertar',0)"><span class="glyphicon glyphicon-plus"></span></a>
			    </h5>
			  </div>
			  <div id="categorias" style="min-height: 600px;">
		  		No se han creado Categorías
			        
			  </div>
			</div>
	</div>

	<div id="panelCentro" style="position:absolute; width: 85%; top: 0px; left: 15%;">
		
			<div class="panel panel-primary" >
			  <div class="panel-heading">
			    <h5 class="panel-title" style="font-size:16px;">
			      Informes
			      	<input type="hidden" id="idCategoria" value="0">
		     		<a style="float:right;" href="javascript:editarInforme('insertar',0, modo, $('#idCategoria').val())"><span class="glyphicon glyphicon-plus"></span></a>
			    </h5>
			  </div>
			  <div id="informes"  style="min-height: 600px;">
		  		No se han creado Informes en esta categoria
			        
			  </div>
			</div>
	</div>


</div>

<!-- Modal Categorias-->
<div id="ModalCategoria" class="modal fade" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="height: 100%">
        <!-- Modal content-->
        <div class="modal-content" style="width: 100%;">
            <div class="modal-header btn-default active" style="border-radius: 3px;">
                <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
                <h4 class="modal-title"><span class="fa fa-info-sign"></span>&nbsp; 
                Categorías
                </h4>

            </div>
            <div class="modal-body" style="height:200px;">
	            <div class="container" style="width: 100%;height: 100%;overflow-y:scroll;">

					<input id="accionCategoriaInforme" type="hidden" value="consultar"/>

					<fieldset id="categoriainforme-form-fieldset">	
						<div class="form-group" id='test'>
					          {!!Form::label('nombreCategoriaInforme', 'Nombre', array('class' => 'col-sm-2 control-label')) !!}
					          <div class="col-sm-10">
					            <div class="input-group">
					              <span class="input-group-addon">
					                <i class="fa fa-pencil-square-o "></i>
					              </span>
									<input id="nombreCategoriaInforme" type="text" value="" placeholder="Ingresa el nombre del sistema de informaci&oacute;n" class="form-control"/>
									<input id="idCategoriaInforme" type="hidden" value=""/>
					            </div>
					          </div>
				    	</div>

				        <div class="form-group" id='test'>
					        {!! Form::label('observacionCategoriaInforme', 'Observación', array('class' => 'col-sm-2 control-label')) !!}
					        <div class="col-sm-1">
					          <div class="input-group">
					            <span class="input-group-addon">
					              <i class="fa fa-check-circle "></i>
					            </span>
					            <textarea id="observacionCategoriaInforme" style="width: 366px;" class="form-control"></textarea>
					          </div>
					        </div>
				        </div>
						
					    <input type="hidden" id="token" value="{{csrf_token()}}"/>
				    </fieldset>
					

				</div> 
            </div>
            <div class="modal-footer btn-default active" style="border-radius: 3px; text-align:center;">
                <button id="guardarCategoria" type="button" class="btn btn-primary" onclick="OcultarCategoria();">OK</button>
                <button id="cancelarCategoria" type="button" class="btn btn-primary" onclick="OcultarCategoria('cancelar');">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<!--Fin Modal Categorias -->



<!-- Modal Filtros-->
<div id="ModalFiltro" class="modal fade" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="height: 100%; width: 80%;">
        <!-- Modal content-->
        <div class="modal-content" style="width: 100%;">
            <div class="modal-header btn-default active" style="border-radius: 3px;">
                <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
                <h4 class="modal-title"><span class="fa fa-info-sign"></span>&nbsp; 
                Generación de Informe
                </h4>
                <input type="hidden" id="idInforme" value="0">
                <input type="hidden" id="idSistema" value="">
                <input type="hidden" id="nombreTabla" value="">
            </div>
            <div class="modal-body" style="height:450px;">
	            <div class="container" style="width: 100%; height: 100%;">

									
						  <div id="generacion" style="min-height: 450px;">
					  		No se ha seleccionado ningun informe
						        
						  </div>
				

				</div> 
            </div>
            <div class="modal-footer btn-default active" style="border-radius: 3px; text-align:center;">
                <button id="generarInforme" type="button" class="btn btn-primary" onclick="GenerarInforme();">Generar Informe</button>
                <button id="cancelarCategoria" type="button" class="btn btn-primary" onclick="OcultarFiltro('cancelar');">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<!--Fin Modal Filtros -->


<div id="ModalConceptos" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width:70%;">

    <!-- Modal content-->
    <div style="" class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Seleccione el Concepto para el Cálculo</h4>
      </div>
      <div class="modal-body">
       <div class="container">
            <div class="row">
                <div class="container col-md-9">
                    <div class="btn-group" style="margin-left: 94%;margin-bottom:4px" title="Columns">
                        <button  type="button" class="btn btn-default dropdown-toggle"data-toggle="dropdown">
                            <i class="glyphicon glyphicon-th icon-th"></i> 
                            <span class="caret"></span>
                        </button>
                       <ul class="dropdown-menu dropdown-menu-right" role="menu">
                            <li><a class="toggle-vis" data-column="0"><label> ID</label></a></li>
                            <li><a class="toggle-vis" data-column="0"><label> Concepto</label></a></li>
                        </ul>
                    </div>
                    
                    <table id="tinformeconceptoSelect" name="tinformeconceptoSelect" class="display table-bordered col-md-12 col-lg-12">
                        <thead>
                            <tr class="btn-default active">

                                <th><b>ID</b></th>
                                <th><b>Concepto</b></th>
                              
                            </tr>
                        </thead>
                        <tfoot>
                            <tr class="btn-default active">

                                <th>ID</th>
                                <th>Concepto</th>
                               
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>



<div id="ModalCategorias" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width:70%;">

    <!-- Modal content-->
    <div style="" class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Seleccione La Categoría</h4>
      </div>
      <div class="modal-body">
       <div class="container">
            <div class="row">
                <div class="container col-md-9">
                    <div class="btn-group" style="margin-left: 94%;margin-bottom:4px" title="Columns">
                        <button  type="button" class="btn btn-default dropdown-toggle"data-toggle="dropdown">
                            <i class="glyphicon glyphicon-th icon-th"></i> 
                            <span class="caret"></span>
                        </button>
                       <ul class="dropdown-menu dropdown-menu-right" role="menu">
                            <li><a class="toggle-vis" data-column="0"><label> ID</label></a></li>
                            <li><a class="toggle-vis" data-column="0"><label> Categoría</label></a></li>
                        </ul>
                    </div>
                    
                    <table id="tcategoriainformeSelect" name="tcategoriainformeSelect" class="display table-bordered col-md-12 col-lg-12">
                        <thead>
                            <tr class="btn-default active">

                                <th><b>ID</b></th>
                                <th><b>Categoría</b></th>
                              
                            </tr>
                        </thead>
                        <tfoot>
                            <tr class="btn-default active">

                                <th>ID</th>
                                <th>Categoría</th>
                               
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- Cuando se termine de cargar el HTML, ejecutamos el maestro lateral de Categorías -->
<script type="text/javascript">
	$( document ).ready(function() 
	{
		
		// al terminar de cargar el formulario, llamamos las funciones que consultan
		// las categorias
	    cargarCategoriaInforme();

	});
</script>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-46156385-1', 'cssscript.com');
  ga('send', 'pageview');

</script>

</body>
</html>
