totalCapas = 0;
/****************************************************
**
** CATEGORIAS DE INFORME
**
****************************************************/
function cargarCategoriaInforme()
{
    var token = document.getElementById('token').value;
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        dataType: "json",
        url:   'http://'+location.host+'/consultarCategoriaInforme',
        data: {},
        type:  'post',
        beforeSend: function(){
            },
        success: function(data){
		
        	var tabla = '<table class="table table-striped" style="width:100%">'+
			    '<tbody>';
	

            for(var i=0; i < data.length; i++)
            {
            	tabla += '<tr>'+
            		'<td width="50px" style="font-size: 16px; cursor: pointer;  text-align:center; vertical-align:middle;">'+
            			'<a href="javascript:editarCategoriaInforme(\'modificar\','+data[i].idCategoriaInforme+')">'+
                            '<span style="color:green;" class="glyphicon glyphicon-pencil"></span>'+
                        '</a>&nbsp;'+
                        '<a href="javascript:editarCategoriaInforme(\'eliminar\','+data[i].idCategoriaInforme+')">'+
                            '<span style="color:red;" class="glyphicon glyphicon-trash"></span>'+
                        '</a>'+
                    '</td>'+
			        '<td style="font-size: 16px; cursor: pointer;">'+
			        '	<a href="javascript:mostrarInformesCategoria('+data[i].idCategoriaInforme+')">'+
                    '        <div class="panel-footer">'+
                    '            <span class="pull-left" title="'+data[i].observacionCategoriaInforme+'">'+data[i].nombreCategoriaInforme+'</span>'+
                    '            <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>'+
                    '            <div class="clearfix"></div>'+
                    '        </div>'+
                    '    </a>'+
			        '</td>'+

			      '</tr>';

            }

	        tabla += '</tbody>'+
			'</table>';

			$("#categorias").html(tabla) ; 

            
        },
        error:    function(xhr,err){
            alert('Se ha producido un error: ' +err);
        }
    });
}


/****************************************************
**
** VISTA MODAL: CATEGORIAS DE INFORME
**
****************************************************/
function editarCategoriaInforme(accion, id)
{
    var token = document.getElementById('token').value;
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        dataType: "json",
        url:   'http://'+location.host+'/consultarCategoriaInforme',
        data: {accion: accion, idCategoriaInforme: id},
        type:  'post',
        beforeSend: function(){
            },
        success: function(data){
        	
        	$("#accionCategoriaInforme").attr('value', accion);

            for(var i=0; i < data.length; i++)
            { 

                $("#idCategoriaInforme").attr('value', data[i].idCategoriaInforme);
                $("#nombreCategoriaInforme").attr('value', data[i].nombreCategoriaInforme);
                $("#observacionCategoriaInforme").html(data[i].observacionCategoriaInforme);
              
            }
            $("#guardarCategoria").html(accion.charAt(0).toUpperCase() + accion.slice(1));
            $("#guardarCategoria").attr('class', (accion == 'insertar' ? 'btn btn-success' : (accion == 'modificar' ? 'btn btn-warning' : 'btn btn-danger')));

            $("#ModalCategoria").modal('show');
        },
        error:    function(xhr,err){
            alert('Se ha producido un error al cargar las Categorías');
        }
    });

}

/****************************************************
**
** ALMACENAMIENTO: GUARDAR SISTEMAS DE INFORMACION Y OCULTAR MODAL
**
****************************************************/
function OcultarCategoria(accion)
{

	var valores = new Array();
	valores[0] = $('#idCategoriaInforme').val();
	valores[1] = $('#nombreCategoriaInforme').val();
	valores[2] = $('#observacionCategoriaInforme').val();

	var accion = (accion) ? accion : $("#accionCategoriaInforme").val();
	id = 0;
	if(accion != 'cancelar')
	{
		var token = document.getElementById('token').value;
	    $.ajax({
	        headers: {'X-CSRF-TOKEN': token},
	        dataType: "json",
	        url:   'http://'+location.host+'/guardarCategoriaInforme',
	        data: {accion: accion, idCategoriaInforme: $("#idCategoriaInforme").val(), valores: valores},
	        type:  'post',
	        beforeSend: function(){
	            },
	        success: function(data){
	        
	        },
	        error:    function(xhr,err){
	            alert('Se genero un error: ' +err);
	        }
	    });
	}
	cargarCategoriaInforme();
	$("#ModalCategoria").modal('hide');

}

function mostrarInformesCategoria(idCategoriaInforme)
{
	$("#idCategoria").val(idCategoriaInforme);
	var token = document.getElementById('token').value;
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        dataType: "json",
        url:   'http://'+location.host+'/mostrarInformesCategoria',
        data: {idCategoriaInforme: idCategoriaInforme},
        type:  'post',
        beforeSend: function(){
            },
        success: function(data){
		
        	informes = '';	

            for(var i=0; i < data.length; i++)
            {
            	informes +=
	            	'<div class="col-lg-3 col-md-3">'+
	                    '<div class="panel panel-info">'+
	                    '    <div class="panel-heading">'+
	                    '        <div class="row">'+
	                    '            <div class="col-xs-3">'+
	                    '                <i class="fa fa-file-text-o fa-5x"></i>'+
	                    '            </div>'+
	                    '            <div class="col-xs-9 text-right">'+
	                    '				<a href="javascript:editarInforme(\'modificar\','+data[i].idInforme+', modo, $(\'#idCategoria\').val());"><span style="color:green;" class="glyphicon glyphicon-pencil"></span></a>'+
	                    '				<a href="javascript:editarInforme(\'duplicar\','+data[i].idInforme+', modo, $(\'#idCategoria\').val());"><span style="color:brown;" class="glyphicon glyphicon-duplicate"></span></a>'+
                        '               <a href="javascript:editarInforme(\'mover\','+data[i].idInforme+', modo, $(\'#idCategoria\').val());"><span style="color:black;" class="glyphicon glyphicon-move"></span></a>'+
                        '               <a href="javascript:editarInforme(\'eliminar\','+data[i].idInforme+', modo, $(\'#idCategoria\').val());"><span style="color:red;" class="glyphicon glyphicon-trash"></span></a>'+
	                    '            </div>'+
	                    '            <div class="col-xs-9 text-right">'+
	                    '                <div>'+data[i].nombreInforme+'</div>'+
	                    '            </div>'+
	                    '        </div>'+
	                    '    </div>'+
	                    '    <a href="javascript:mostrarInformeDetalle('+data[i].idInforme+')">'+
	                    '        <div class="panel-footer">'+
	                    '            <span class="pull-left">Generar Informe</span>'+
	                    '            <span class="pull-right"><i class="fa fa-print"></i></span>'+
	                    '            <div class="clearfix"></div>'+
	                    '        </div>'+
	                    '    </a>'+
	                    '</div>'+
	                '</div>';

            }

	        $("#informes").html(informes) ; 

            
        },
        error:    function(xhr,err){
            alert('Se ha producido un error: ' +err);
        }
    });
}

function mostrarInformeDetalle(idInforme)
{
	var token = document.getElementById('token').value;
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        dataType: "json",
        url:   'http://'+location.host+'/consultarInforme',
        data: {idInforme: idInforme},
        type:  'post',
        beforeSend: function(){
            },
        success: function(data){
		
        	informes = '';	
            for(var i=0; i < data.length; i++)
            {
            	$("#ModalFiltro .modal-title").html(
            			'<span class="fa fa-file-text"></span>&nbsp;'+data[i].nombreInforme) ; 

            	$("#idInforme").val(idInforme) ; 

            	
            	$("#generacion").html(
            			'<div class="col-xs-12 text-center">'+
				        '	<div style="font-size:18px;">'+data[i].descripcionInforme+'</div>'+
				        '</div>'+
				        '<ul id="tabcapa" class="nav nav-tabs">'+				
						'</ul>'+
						'<div id="contentcapa" class="tab-content">'+
						'</div>'); 
            }

	        
	        cargarInformeCapa(idInforme, 'consulta');

	        $("#ModalFiltro").modal('show');
            
        },
        error:    function(xhr,err){
            alert('Se ha producido un error: ' +err);
        }
    });
}


/****************************************************
**
** CARGAR CAPAS DE INFORME
**
****************************************************/
function cargarInformeCapa(idInforme, accion)
{
    if(idInforme != 0)
    {
        var token = document.getElementById('token').value;
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            url:   'http://'+location.host+'/consultarInformeCapa',
            data: {accion: accion, idInforme: idInforme},
            type:  'post',
            beforeSend: function(){
                },
            success: function(data){
                
                // con el id de informe consultamos los datos de encabezado y los llenamos en la vista
                // la capa contable solo la adicionamos una sola vez, para esto usamos un switch
                $capacont = false;
                for(var i=0; i < data.length; i++)
                {
                    
                    if((data[i].tipoInformeCapa == 2 && $capacont == false) || data[i].tipoInformeCapa == 1)
                    {
                        adicionarTitulosFiltro(data[i].tipoInformeCapa, data[i].idInformeCapa, 
                        accion, data[i].SistemaInformacion_idSistemaInformacion,
                        data[i].tablaInformeCapa);
                    }

                    if(data[i].tipoInformeCapa == 2)
                        $capacont = true;
                }

            },
            error:    function(xhr,err){
                alert('Se ha producido un error: ' +err);
            }
        });
    }
}


function adicionarTitulosFiltro(tipo, idInformeCapa, accion, idSistema, tabla)
{
    totalCapas++;
    //
    // $('#tabcapa').append($('<li><a href="#tab' + totalCapas + '" role="tab" data-toggle="tab">Tab ' + totalCapas + '<button class="close" type="button" title="Remove this page">×</button></a></li>'));
    $("#tabcapa").append('<li id="hojacapa'+totalCapas+'" >'+
                                '<input type="hidden" id="idInformeCapa'+totalCapas+'" value="'+idInformeCapa+'">'+
                                '<input type="hidden" id="tipoInformeCapa'+totalCapas+'" value="'+tipo+'">'+
                                '<input type="hidden" id="idSistemaInformacion'+totalCapas+'" value="">'+
                                '<input type="hidden" id="nombreTabla'+totalCapas+'" value="">'+
                                '<a data-toggle="tab" href="#capa'+totalCapas+'" onclick="seleccionarCapaFiltro('+totalCapas+');">'+
                                        '<span class="fa fa-filter"></span>&nbsp;Capa '+totalCapas+
                                '</a>'+
                        '</li>');

	
    // Luego de crear la pestaña de la capa llenamos los campos de idSistemaInformacion y nombreTabla con la
    // informacion del panel de conexion a la base de datos 
    $("#idSistemaInformacion"+totalCapas).val(idSistema);
    $("#nombreTabla"+totalCapas).val(tabla);    


    adicionarRegistroFiltro(tipo, 'capa'+totalCapas);

}


function adicionarRegistroFiltro(tipo, idcapa)
{

	if(tipo == 1)
	{

		$("#contentcapa").append(
		'<div  class="tab-pane" id="'+idcapa+'">'+
			'<div class="col-md-6" id="condicion" style="width: 100%; height:350px; background-color: white; border: 1px solid; border-color: #ddd; ">'+
		        '<div id="operadores">'+
		        '  <div class="panel-body">'+
		        '  <div class="form-group">'+
		        '    <div class="col-sm-12">'+
		        '      <div class="row show-grid">'+
		        '        <div class="col-md-1" style="width: 40px;" onclick="informecondicion.agregarCampos(valorinformecondicion, \'A\')">'+
		        '          <span class="glyphicon glyphicon-plus"></span>'+
		        '        </div>'+
		        '        <div class="col-md-1" style="width: 100px;">Agrupador</div>'+
		        '        <div class="col-md-1" style="width: 280px;">Campo</div>'+
		        '        <div class="col-md-1" style="width: 160px;">Operador</div>'+
		        '        <div class="col-md-1" style="width: 190px;">Valor</div>'+
		        '        <div class="col-md-1" style="width: 100px;">Agrupador</div>'+
		        '        <div class="col-md-1" style="width: 100px;">Conector</div>'+
		        '        <div id="contenedor_condicion'+idcapa+'">'+
		        '        </div>'+
		        '      </div>'+
		        '    </div>'+
		        '  </div>'+
		        '</div>'+
		        '</div>'+
		    '</div>'+
		'</div>');
		
	}
    else 
    {
        
        filtro = 
            '<div class="col-lg-6">'+
            '   <div class="col-lg-12">'+
            '       <div class="panel panel-primary">'+
            '           <div class="panel-heading">'+
            '               <i class="fa fa-calendar fa-lg"></i> Período<br>'+
            '           </div>'+
            '           <div class="panel-body">'+
            '              <div class="col-lg-6">'+
            '                   <label for="desde'+idcapa+'">Desde </label>'+
            '                   <div class="input-group date" id="desde'+idcapa+'">'+
            '                       <input type="text" id="fechaInicio'+idcapa+'" class="form-control" />'+
            '                       <span class="input-group-addon">'+
            '                           <span class="glyphicon glyphicon-calendar"></span>'+
            '                       </span>'+
            '                   </div>'+
            '               </div>'+
            '               <div class="col-lg-6">'+
            '                   <label for="hasta'+idcapa+'">Hasta </label>'+
            '                   <div class="input-group date" id="hasta'+idcapa+'">'+
            '                       <input type="text" id="fechaFin'+idcapa+'" class="form-control" />'+
            '                       <span class="input-group-addon">'+
            '                           <span class="glyphicon glyphicon-calendar"></span>'+
            '                       </span>'+
            '                   </div>'+
            '               </div>'+
            '           </div>'+
            '       </div>'+
            '   </div>'+
            '   <div class="col-lg-12">'+
            '       <div class="panel panel-primary">'+
            '           <div class="panel-heading">'+
            '               <i class="fa fa-table fa-lg"></i> Tipo de Contabilidad<br>'+
            '           </div>'+
            '           <div class="panel-body">'+
            '                    <div class="segmented-control" style="width: 45%; display: inline-block; color: brown;">'+
            '                      <input type="checkbox" name="comLoc'+idcapa+'" id="comLoc'+idcapa+'" checked="checked" >'+
            '                      <label for="comLoc'+idcapa+'" data-value="Local">Local</label>'+
            '                    </div>'+
            '                    <div class="segmented-control" style="width: 45%; display: inline-block; color: brown;">'+
            '                      <input type="checkbox" name="comNiif'+idcapa+'" id="comNiif'+idcapa+'" >'+
            '                      <label for="comNiif'+idcapa+'" data-value="Niif">Niif</label>'+
            '                    </div>'+
            '           </div>'+
            '       </div>'+
            '   </div>'+
            '   <div class="col-lg-12">'+
            '       <div class="panel panel-primary">'+
            '           <div class="panel-heading">'+
            '               <i class="fa fa-database fa-lg"></i> Bases de Datos<br>'+
            '           </div>'+
            '           <div class="panel-body">'+
            '               <div id="grupobd'+idcapa+'" style="height:70px; overflow: auto;">'+
            '               </div>'+
            '           </div>'+
            '       </div>'+
            '   </div>'+
            '</div>'+
            '<div class="col-lg-6">'+
            '   <div class="col-lg-12">'+
            '       <div class="panel panel-primary">'+
            '           <div class="panel-heading">'+
            '               <i class="fa fa-table fa-lg"></i> Columnas Complemento<br>'+
            '           </div>'+
            '           <div class="panel-body">'+
            '                    <div class="segmented-control" style="width: 33%; display: inline-block; color: purple;">'+
            '                      <input type="checkbox" name="comPorPG'+idcapa+'" id="comPorPG'+idcapa+'" title="Porcentaje vertical basado en un concepto base" onclick="abrirModalConcepto(this.checked, '+$("#idInformeCapa"+idcapa.replace('capa','')).val()+', \''+idcapa+'\');">'+
            '                      <label for="comPorPG'+idcapa+'" data-value="% Vert PYG">% Vert PYG</label>'+
            '                       <input type="hidden" id="conceptoPorcentajeVertical'+idcapa+'" value="">'+
            '                    </div>'+
            '                    <div class="segmented-control" style="width: 33%; display: inline-block; color: purple;">'+
            '                      <input type="checkbox" name="comPorBG'+idcapa+'" id="comPorBG'+idcapa+'" title="Porcentaje vertical basado en grupos (formulas)" >'+
            '                      <label for="comPorBG'+idcapa+'" data-value="% Vert BG">% Vert BG</label>'+
            '                       <input type="hidden" id="conceptoPorcentajeVertical'+idcapa+'" value="">'+
            '                    </div>'+
            '                    <div class="segmented-control" style="width: 30%; display: inline-block; color: purple;">'+
            '                      <input type="checkbox" name="comVar'+idcapa+'" id="comVar'+idcapa+'" >'+
            '                      <label for="comVar'+idcapa+'" data-value="Variación">Variación</label>'+
            '                    </div>'+
            '           </div>'+
            '       </div>'+
            '   </div>'+
            '   <div class="col-lg-12">'+
            '       <div class="panel panel-primary">'+
            '           <div class="panel-heading">'+
            '               <i class="fa fa-usd fa-lg"></i> Cifras<br>'+
            '           </div>'+
            '           <div class="panel-body">'+
            '                    <div id="grupocifra'+idcapa+'" class="segmented-control" style="width: 100%; color: green;">'+
            '                      <input type="radio" name="cifras" id="cifra'+idcapa+'Pesos" checked>'+
            '                      <input type="radio" name="cifras" id="cifra'+idcapa+'Miles">'+
            '                      <input type="radio" name="cifras" id="cifra'+idcapa+'Millon" >'+
            '                      <label for="cifra'+idcapa+'Pesos" data-value="Pesos">Pesos</label>'+
            '                      <label for="cifra'+idcapa+'Miles" data-value="Miles">Miles</label>'+
            '                      <label for="cifra'+idcapa+'Millon" data-value="Millones">Millones</label>'+
            '                    </div>'+
            '           </div>'+
            '       </div>'+
            '   </div>'+
            '   <div class="col-lg-12">'+
            '       <div class="panel panel-primary">'+
            '           <div class="panel-heading">'+
            '               <i class="fa fa-th-list fa-lg"></i> Formato<br>'+
            '           </div>'+
            '           <div class="panel-body">'+
            '                    <div id="grupoformato'+idcapa+'" class="segmented-control" style="width: 100%; color: red;">'+
            '                      <input type="radio" name="formato" id="form'+idcapa+'Det" checked>'+
            '                      <input type="radio" name="formato" id="form'+idcapa+'Res">'+
            '                      <input type="radio" name="formato" id="form'+idcapa+'Gra" >'+
            '                      <label for="form'+idcapa+'Det" data-value="Detallado">Detallado</label>'+
            '                      <label for="form'+idcapa+'Res" data-value="Resumido">Resumido</label>'+
            '                      <label for="form'+idcapa+'Gra" data-value="Grafico">Grafico</label>'+
            '                    </div>'+
            '           </div>'+
            '       </div>'+
            '   </div>'+
            '</div>';

        $("#contentcapa").append(filtro);
        
        crearCheckBoxSistemaInformacion(idcapa);
        
 
            $('#desde'+idcapa+', #hasta'+idcapa).datetimepicker({
                
                format: 'YYYY-MM-DD'
            });

    }
}


function crearCheckBoxSistemaInformacion(idcapa)
{
    filtro = '';
    var token = document.getElementById('token').value;
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        dataType: "json",
        url:   'http://'+location.host+'/consultarSistemaInformacion',
        data: {esWeb: '1'},
        type:  'post',
        beforeSend: function(){
            },
        success: function(data){
        
            filtro = '';
            
            
            for(var i=0; i < data.length; i++)
            {
                filtro += 
                '<div class="segmented-control" style="width: 30%; display: inline-block; color: orange;">'+
                '   <input type="checkbox" name="bd'+idcapa+data[i].idSistemaInformacion+'" id="bd'+idcapa+data[i].idSistemaInformacion+'" >'+
                '   <label for="bd'+idcapa+data[i].idSistemaInformacion+'" data-value="'+data[i].nombreSistemaInformacion+'">'+data[i].nombreSistemaInformacion+'</label>'+
                '</div>';
            }
            $("#grupobd"+idcapa).html(filtro);
        },
        error:    function(xhr,err){
            alert('Se ha producido un error: ' +err);
        }

    });
}

/****************************************************
**
** SELECCIONAR LA CAPA DE FILTRO
**
****************************************************/
function seleccionarCapaFiltro(numeroCapa)
{
    // cuando el usuario hace click sobre la capa, el sistema debe cambair parametros en el prototipo que agrega
    // los registros de condicion, y consultar de nuevo los campos de la tabla asociada a la capa

	informecondicion.nombre = "informecondicion";
    informecondicion.contenedor = "contenedor_condicioncapa"+numeroCapa;
    informecondicion.contenido = "informecapa"+numeroCapa+"_";

	var token = document.getElementById('token').value;
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        dataType: "json",
        data: {	idSistema: $("#idSistemaInformacion"+numeroCapa).val(), 
        		nombreTabla: $("#nombreTabla"+numeroCapa).val()},
        url:   'http://'+location.host+'/conexionDocumentoCampos/',
        type:  'post',
        beforeSend: function(){
            },
        success: function(respuesta){
           
            // almacenamos los campos retornados en un array para luego asignarlo al prototipo que agrega condiciones de filtro
            var campoFiltro = new Array();
            var comentarioFiltro = new Array();
            for (var i = 0; i < respuesta.length ; i++)
            {
                campoFiltro[i] = respuesta[i]["Campo"];
                comentarioFiltro[i] = respuesta[i]["Comentario"];
            }
            informecondicion.opciones[1] = [campoFiltro, comentarioFiltro];
			// alert([campoFiltro, comentarioFiltro]);
 
            },
            error:    function(xhr,err){ 
                alert("No se pudieron consultar los campos de la tabla/vista");
            }
        });


} 

/****************************************************
**
** ALMACENAMIENTO: GUARDAR FILTROS Y OCULTAR MODAL
**
****************************************************/
function GenerarInforme()
{

	// limpiamos el campo oculto de bases de datos
	$("#idSistema").val('') ; 
	// recorremos cada una de las CAPAS del informe
	$(".nav-tabs").each(function()
    {
        $(this).find( "li[id*='hojacapa']" ).each(function()
        {
            
            // Dependiendo de la capa varian los parametros de filtro
            // si es de tipo informe general, los filtros son una multiregitro con condicion tipo where
            // si es de tipo contable, los filtros son botones de radio y checkbox
            var numeroCapa = $(this).attr('id').replace('hojacapa','');
            var idCapa = $("#idInformeCapa"+numeroCapa).val();
           

            if($("#tipoInformeCapa"+numeroCapa).val() == 1)
            {
        		var condicion = '';
        		//concatenamos los ID de sistemas de informacion de cada capa
        		$("#idSistema").val( ($("#idSistema").val() != '' ? ',' : '') + $("#idSistemaInformacion"+numeroCapa).val()) ; 

        		//concatenamos los NOMBRES de las tablas de cada capa
        		$("#nombreTabla").val( ($("#nombreTabla").val() != '' ? ',' : '') + $("#nombreTabla"+numeroCapa).val()) ; 

                // dentro de cada capa, consultamos las para obtener sus objetos
                $("#ModalFiltro").find( "div[id*='informecapa"+numeroCapa+"_']" ).each(function()
                {
                        
                    condicion += 
        	            $(this).find( "[id*='parentesisInicioInformeCondicion']" ).val()+' '+
        	            $(this).find( "[id*='campoInformeCondicion']" ).val()+' ';

        	            // tanto el operador como el valor se presentan de diferente forma dependiendod e la consicion
        	            switch($(this).find( "[id*='operadorInformeCondicion']" ).val())
        	            {
        	            	case 'likei':
        	            		condicion += ' like '+
        	            					"'"+$(this).find( "[id*='valorInformeCondicion']" ).val()+"*' ";
        	            	break;

        	            	case 'likef':
        	            		condicion += ' like '+
        	            					"'*"+$(this).find( "[id*='valorInformeCondicion']" ).val()+"' ";
        	            	break;

        	            	case 'like':
        	            		condicion += ' like '+
        	            					"'*"+$(this).find( "[id*='valorInformeCondicion']" ).val()+"*' ";
        	            	break;

        	            	case 'not like':
        	            		condicion += 'not like '+
        	            					"'*"+$(this).find( "[id*='valorInformeCondicion']" ).val()+"*' ";
        	            	break;

        	            	case 'in':
        	            		condicion += ' in '+
        	            					"('"+$(this).find( "[id*='valorInformeCondicion']" ).val()+"') ";
        	            	break;

        	            	default:
        	            		condicion += $(this).find( "[id*='operadorInformeCondicion']" ).val()+' '+
        	            					"'"+$(this).find( "[id*='valorInformeCondicion']" ).val()+"' ";
        	            	break;
        	            }
        	             
        	            condicion += $(this).find( "[id*='parentesisFinInformeCondicion']" ).val()+' '+
        	            			 $(this).find( "[id*='conectorInformeCondicion']" ).val();

                                     
                });
                window.open('http://'+location.host+'/generarInforme'+
                    '?idInforme='+$("#idInforme").val()+
                    '&idSistema='+$("#idSistema").val()+
                    '&nombreTabla='+$("#nombreTabla").val()+
                    '&condicion='+condicion,
                    "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,width=1024px,height=768px");

            }
            else // TIPO CONTABLE
            {
                var fechaInicio = $("[id*='fechaIniciocapa"+numeroCapa+"']" ).val();
                var fechaFin = $("[id*='fechaFincapa"+numeroCapa+"']" ).val();

                // Valor del check box de bases de datos
                var valorBD = "";
                $("#grupobdcapa"+numeroCapa+" input[type='checkbox']" ).each(function()
                {
                    if($(this).prop('checked'))
                        valorBD += $(this).prop('id').replace('bdcapa'+numeroCapa,'')+',';
                    
                });


                // Valor del check box de bases de datos
                var valorBD = "";
                $("#grupobdcapa"+numeroCapa+" input[type='checkbox']" ).each(function()
                {
                    if($(this).prop('checked'))
                        valorBD += $(this).prop('id').replace('bdcapa'+numeroCapa,'')+',';
                });

                // validamos que el usuario haya ingresado por lo menos
                // el rango de fechas y una base de datos
                var error = '';
                if(fechaInicio == '' || fechaFin == '')
                    error += " - Ingrese ambas fechas\n";
                else if(fechaInicio > fechaFin)
                    error += " - La fecha final debe ser mayor a la fecha inicial\n";

                if(valorBD == '')
                    error += " - Seleccione al menos una base de datos\n"; 

                if(error != '')
                {
                    alert("Por favor corrija los siguientes campos:\n"+error);
                    return;
                }


                // Valor del radio button de cifras
                var valorCifra = "1";
                var selected = $("#grupocifracapa"+numeroCapa+" input[type='radio']:checked");
                if (selected.length > 0) {
                    var aux = selected.prop('id').replace('cifracapa'+numeroCapa,'');
                }
                valorCifra = (aux == 'Pesos' ? 1 : (aux == 'Miles' ? 1000 : (aux == 'Millon' ? 1000000 : 1)));
                

                // Valor del radio button de Formato
                var valorFormato = "Det";
                var selected = $("#grupoformatocapa"+numeroCapa+" input[type='radio']:checked");
                if (selected.length > 0) {
                    valorFormato = selected.prop('id').replace('formcapa'+numeroCapa,'');
                }
                
                

                var colPorcentajeVert = $("#conceptoPorcentajeVerticalcapa"+numeroCapa).val();
                var colPorcentajePG = ($("#comPorPGcapa"+numeroCapa).prop('checked') == true ? 1 : '');
                var colPorcentajeBG = ($("#comPorBGcapa"+numeroCapa).prop('checked') == true ? 1 : '');
                var colVariacion = ($("#comVarcapa"+numeroCapa).prop('checked') == true ? 1 : 0);
                
                var colTipoLocal = ($("#comLoccapa"+numeroCapa).prop('checked') == true ? 'Local' : '');
                var colTipoNiif = ($("#comNiifcapa"+numeroCapa).prop('checked') == true ? 'Niif' : '');
                var tipoCont = colTipoLocal + 
                                ((colTipoLocal != '' && colTipoNiif != '') ? ',' : '')+
                                colTipoNiif;

                //tomamos del modal de filtros, el id del informe a generar
                window.open('http://'+location.host+'/generarInforme'+
                        '?idInforme='+$("#idInforme").val()+
                        '&idSistema='+valorBD+
                        '&fechaInicial='+fechaInicio+
                        '&fechaFinal='+fechaFin+
                        '&cifra='+valorCifra+
                        '&formato='+valorFormato+
                        '&colPorcentajePG='+colPorcentajePG+
                        '&colPorcentajeFormula='+colPorcentajeBG+
                        '&colVariacion='+colVariacion+
                        '&colPorcentajeVert='+colPorcentajeVert+
                        '&tipoContabilidad='+tipoCont);

            }
        });
        

        
    });

    // 

    // for(i = 0; i < informecondicion.contador; i++)
    // {
    //     condicion += 
    //         document.getElementById("parentesisInicioInformeCondicion"+i).value+' '+
    //         document.getElementById("campoInformeCondicion"+i).value+' '+
    //         document.getElementById("operadorInformeCondicion"+i).value+' '+
    //         document.getElementById("valorInformeCondicion"+i).value+' '+
    //         document.getElementById("parentesisFinInformeCondicion"+i).value+' '+
    //         document.getElementById("conectorInformeCondicion"+i).value+' ';
    // }
    // alert(condicion);

    
	// $("#ModalFiltro").modal('hide');
}

function editarInforme(accion, idInforme, modo, idCategoria)
{
	if(idCategoria == 0)
	{
		alert('Debe seleccionar primero una categoría de informe');
		return;
	}

    // si la accion es modificar o adicionar, abrimos el diseñador de informes, pero si es eliminar,
    // ejecutamos un ajax de eliminacion (despues de la confirmacion de borrado)
    switch(accion)
    {
        case 'insertar':
           window.open('http://'+location.host+'/disenadorinforme?idInforme='+idInforme+'&accion='+accion+'&modo='+modo+'&idCategoria='+idCategoria,
            "_blank",'height='+screen.height+',width='+screen.width+',fullscreen=yes');
           break;


        case 'modificar':
	       window.open('http://'+location.host+'/disenadorinforme?idInforme='+idInforme+'&accion='+accion+'&modo='+modo+'&idCategoria='+idCategoria,
        	"_blank",'height='+screen.height+',width='+screen.width+',fullscreen=yes');
           break;
        
        case 'eliminar':
    
            var resp = confirm('Esta seguro de eliminar el informe?');
            if(resp)
            {
                var token = document.getElementById('token').value;
                $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                dataType: "json",
                data: { idInforme: idInforme},
                url:   'http://'+location.host+'/eliminarInforme/',
                type:  'post',
                beforeSend: function(){
                    },
                success: function(respuesta){
                   

                    cargarCategoriaInforme()
                    mostrarInformesCategoria($("#idCategoria").val());
                    alert('Informe Eliminado Correctamente');
         
                    },
                    error:    function(xhr,err){ 
                        alert("No se pudo eliminar el informe, por favor reintente");
                    }
                });
            }
            break;

        case 'duplicar':
            // ejecutamos la funcion de duplicar
            var resp = confirm('Esta seguro de duplicar el informe?');
            if(resp)
            {
                var token = document.getElementById('token').value;
                $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                dataType: "json",
                data: { idInforme: idInforme,
                        idCategoria: idCategoria},

                url:   'http://'+location.host+'/duplicarInforme/',
                type:  'post',
                beforeSend: function(){
                    },
                success: function(respuesta){

                    cargarCategoriaInforme()
                    mostrarInformesCategoria($("#idCategoria").val());
                    alert('Informe Duplicado Correctamente');

         
                    },
                error:    function(xhr,err){ 
                    alert("No se pudo duplicar el informe, por favor reintente");
                    }
                });
            }
            break;

        case 'mover':

            abrirModalCategoria(idInforme);
            
            break;
    }
}

/****************************************************
**
** ALMACENAMIENTO: GUARDAR FILTROS Y OCULTAR MODAL
**
****************************************************/
function OcultarFiltro(accion)
{
	$("#ModalFiltro").modal('hide');
}


function abrirModalConcepto(valor, idcapa, tab)
{
    if(valor == false)
    {
        $("#conceptoPorcentajeVertical"+tab).val('');
    }
    else
    {
        var lastIdx = null;
        window.parent.$("#tinformeconceptoSelect").DataTable().ajax.url("http://"+location.host+"/datosInformeConceptoSelect?idCapa="+idcapa).load();
         // Abrir modal
        window.parent.$("#ModalConceptos").modal()

        $("a.toggle-vis").on( "click", function (e) {
            e.preventDefault();
     
            // Get the column API object
            var column = table.column( $(this).attr("data-column") );
     
            // Toggle the visibility
            column.visible( ! column.visible() );
        } );

        window.parent.$("#tinformeconceptoSelect tbody").on( "mouseover", "td", function () 
        {
            var colIdx = table.cell(this).index().column;

            if ( colIdx !== lastIdx ) {
                $( table.cells().nodes() ).removeClass( "highlight" );
                $( table.column( colIdx ).nodes() ).addClass( "highlight" );
            }
        }).on( "mouseleave", function () 
        {
            $( table.cells().nodes() ).removeClass( "highlight" );
        } );


        // Setup - add a text input to each footer cell
        window.parent.$("#tinformeconceptoSelect tfoot th").each( function () 
        {
            var title = window.parent.$("#tinformeconceptoSelect thead th").eq( $(this).index() ).text();
            $(this).html( "<input type='text' placeholder='Buscar por "+title+"'/>" );
        });
     
        // DataTable
        var table = window.parent.$("#tinformeconceptoSelect").DataTable();
     
        // Apply the search
        table.columns().every( function () 
        {
            var that = this;
     
            $( "input", this.footer() ).on( "blur change", function () {
                if ( that.search() !== this.value ) {
                    that
                        .search( this.value )
                        .draw();
                }
            } );
        })

        window.parent.$("#tinformeconceptoSelect tbody").on( "dblclick", "tr", function () 
        {
            if ( $(this).hasClass("selected") ) {
                $(this).removeClass("selected");
            }
            else {
                table.$("tr.selected").removeClass("selected");
                $(this).addClass("selected");
            }

            var datos = table.rows('.selected').data();
            console.log(datos);

            if (datos.length > 0) 
            {
                $("#conceptoPorcentajeVertical"+tab).val(datos[0][1]);
            }

            window.parent.$("#ModalConceptos").modal("hide");

        } );
    }
}


function abrirModalCategoria(idInforme)
{
    
    

    var lastIdx = null;
    window.parent.$("#tcategoriainformeSelect").DataTable().ajax.url("http://"+location.host+"/datosCategoriaSelect").load();
     // Abrir modal
    window.parent.$("#ModalCategorias").modal();
    $("a.toggle-vis").on( "click", function (e) {
        e.preventDefault();
 
        // Get the column API object
        var column = table.column( $(this).attr("data-column") );
 
        // Toggle the visibility
        column.visible( ! column.visible() );
    } );

    window.parent.$("#tcategoriainformeSelect tbody").on( "mouseover", "td", function () 
    {
        var colIdx = table.cell(this).index().column;

        if ( colIdx !== lastIdx ) {
            $( table.cells().nodes() ).removeClass( "highlight" );
            $( table.column( colIdx ).nodes() ).addClass( "highlight" );
        }
    }).on( "mouseleave", function () 
    {
        $( table.cells().nodes() ).removeClass( "highlight" );
    } );


    // Setup - add a text input to each footer cell
    window.parent.$("#tcategoriainformeSelect tfoot th").each( function () 
    {
        var title = window.parent.$("#tcategoriainformeSelect thead th").eq( $(this).index() ).text();
        $(this).html( "<input type='text' placeholder='Buscar por "+title+"'/>" );
    });
 
    // DataTable
    var table = window.parent.$("#tcategoriainformeSelect").DataTable();
 
    // Apply the search
    table.columns().every( function () 
    {
        var that = this;
 
        $( "input", this.footer() ).on( "blur change", function () {
            if ( that.search() !== this.value ) {
                that
                    .search( this.value )
                    .draw();
            }
        } );
    })

    window.parent.$("#tcategoriainformeSelect tbody").on( "dblclick", "tr", function () 
    {
        if ( $(this).hasClass("selected") ) {
            $(this).removeClass("selected");
        }
        else {
            table.$("tr.selected").removeClass("selected");
            $(this).addClass("selected");
        }

        var datos = table.rows('.selected').data();
        console.log(datos);

        if (datos.length > 0) 
        {
            moverInformeCategoria(idInforme, datos[0][0]);
        }

        window.parent.$("#ModalCategorias").modal("hide");

    } );
   
}

function moverInformeCategoria(idInforme, idCategoria)
{
    alert(idInforme +' - '+ idCategoria);

    var token = document.getElementById('token').value;
    $.ajax({
    headers: {'X-CSRF-TOKEN': token},
    dataType: "json",
    data: { idInforme: idInforme,
            idCategoria: idCategoria},

    url:   'http://'+location.host+'/moverInforme/',
    type:  'post',
    beforeSend: function(){
        },
    success: function(respuesta){

        cargarCategoriaInforme()
        mostrarInformesCategoria($("#idCategoria").val());
        alert('Informe Movido Correctamente');


        },
    error:    function(xhr,err){ 
        alert("No se pudo mover el informe, por favor reintente");
        }
    });


}