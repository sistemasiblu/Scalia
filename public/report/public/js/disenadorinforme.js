var totalCapas=0;
var totalConceptos=1;

 
  
function cambiarTamañoPanel(panel, numero, tipo)
{
    divPadre = "#"+panel+numero;
    divHijo = "#"+panel+"Contenedor"+numero;

    if(tipo == 'R')
    {
        $(divPadre).height(parseFloat($(divPadre).height()) - 20) ;
    }
    else
    {
        $(divPadre).height(parseFloat($(divPadre).height()) + 20) ;
    }
    $(divHijo).height($(divPadre).height() - 36) ;

}

function seleccionarBotonMultiple(campo, idBoton)
{
    $("#grupo-"+campo+" a img").attr('style','background-color: white;');
    $("#"+idBoton+" img").attr('style','background-color: #A9E2F3;');
    $("#"+campo).attr('value',idBoton);
}

function seleccionarBotonSimple(idBoton)
{
    if($("#"+idBoton).attr('style') === 'undefined' || $("#"+idBoton).attr('style') == 'background-color: white;' )
    {
        $("#"+idBoton).attr('style','background-color: #A9E2F3;');  
        $("#"+idBoton+"Concepto").attr('value',1);
    }
    else
    {
        $("#"+idBoton).attr('style','background-color: white;');
        $("#"+idBoton+"Concepto").attr('value',0);
    }
}


function seleccionarBotonTipoValor(campo, idBoton)
{
    // ejecuta la funcion de boton multiple para que se resalte y asigne su valor en el campo oculto
    seleccionarBotonMultiple(campo, idBoton);

    // adicionalmente tenemos que implementar la funcionalidad de cada tipo
    //
    // TIPO             FUNCIONALIDAD
    //-------------------------------------------------------
    // Puc              Permite que el usuario digite en el campo las cuentas PUC por rangos o independientes
    // Formula          Abre el div de diseño de formulas
    // Valor            Permite digitar un valor numerico en el campo
    // Porcentaje       Permite digitar un valor numerico en el campo


    switch($("#"+campo).val()) 
    {
        case 'Puc':
            $("#valorConcepto").attr('readonly',false);
            $("#excluirTerceroConcepto").attr('readonly',false);
            $("#valorConcepto").attr('placeholder','Ingrese los números de cuenta');
            $("#valorConcepto").attr('title',"Para digitar rangos de cuentas, separelas por guion (1005-10059999)\npara cuentas independientes separelas por coma (10050110,10050111,10050112)\npara excluir cuentas ingreselas comenzando por la letra x (x10050105)");
            $("#valorConcepto").focus();
            break;
        case 'Formula':
            
            break;
        case 'Valor':
            $("#valorConcepto").attr('readonly',false);
            $("#excluirTerceroConcepto").attr('readonly',true);
            $("#valorConcepto").attr('placeholder','Ingrese el valor fijo');
            $("#valorConcepto").attr('title','El valor puede contener decimales separados por punto, no debe incluir signos de moneda ni caracteres alfabeticos');
            $("#valorConcepto").focus();
            break;
            break;

        case 'Porcentaje':
            $("#valorConcepto").attr('readonly',false);
            $("#excluirTerceroConcepto").attr('readonly',true);
            $("#valorConcepto").attr('placeholder','Ingrese el porcentaje');
            $("#valorConcepto").attr('title','El porcentaje debe digitarse como valor entre 1 y 100, puede contener decimales separados por punto, no debe incluir el signo de %');
            $("#valorConcepto").focus();
            break;
            break;

    }



}

var zIndex = 0;
function make_draggable(elements)
{   
    elements.draggable({
        containment:'parent',
        cursor: "move",
        // grid: [ 10, 10 ]
        // start:function(e,ui){ ui.helper.css('z-index',++zIndex); },
        // stop:function(e,ui){
        // }
    });

    //elements.innerHtml = '<input type="text" id="estilo" value=""/>';
} 


/****************************************************
**
** SISTEMA DE INFORMACION
**
****************************************************/
function cargarSistemaInformacion()
{
    var token = document.getElementById('token').value;
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        dataType: "json",
        url:   'http://'+location.host+'/consultarSistemaInformacion',
        data: {},
        type:  'post',
        beforeSend: function(){
            },
        success: function(data){
		
        	var tabla = '<table class="table table-striped" style="width:100%">'+
			    '<tbody>';
			
            var select = document.getElementById('sistemaInformacion');
			
            tabla += '<th style="width:40px;padding: 1px 8px;" data-orderable="false">'+
	                    	'<a href="javascript:editarSistemaInformacion(\'insertar\',0)"><span class="glyphicon glyphicon-plus"></span></a>'+
                      '</th>'+
                      '<th>'+
                      '	<b>Nombre</b>'+
                      '</th>';

            select.options.length = 0;
            var option = '';

            option = document.createElement('option');
            option.value = null;
            option.text = 'Seleccione la tabla';
            select.appendChild(option);

            for(var i=0; i < data.length; i++)
            {
            	tabla += '<tr>'+
            		'<td><a href="javascript:editarSistemaInformacion(\'modificar\','+data[i].idSistemaInformacion+')">'+
                            '<span class="glyphicon glyphicon-pencil"></span>'+
                        '</a>&nbsp;'+
                        '<a href="javascript:editarSistemaInformacion(\'eliminar\','+data[i].idSistemaInformacion+')">'+
                            '<span class="glyphicon glyphicon-trash"></span>'+
                        '</a>'+
                    '</td>'+
			        '<td><span>'+data[i].nombreSistemaInformacion+'</span></td>'+
			      '</tr>';

                option = document.createElement('option');
                option.value = data[i].idSistemaInformacion;
                option.text = data[i].nombreSistemaInformacion;
                // option.selected = (tablaDocumento ==  data[i].nombreSistemaInformacion ? true : false);
                select.appendChild(option);
            }

	        tabla += '</tbody>'+
			'</table>';

			$("#basedatos").html(tabla) ; 

            
        },
        error:    function(xhr,err){
            alert('Se ha producido un error: ' +err);
        }
    });
}


/****************************************************
**
** GRID: ESTILOS DE INFORME
**
****************************************************/
function cargarEstiloInforme()
{
    var token = document.getElementById('token').value;
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        dataType: "json",
        url:   'http://'+location.host+'/consultarEstiloInforme',
        data: {accion: 'consultar'},
        type:  'post',
        beforeSend: function(){
            },
        success: function(data){
		      // en el momento en que vamos creando la tabla de estilos para el panel izquierdo, tambien
              // creamos un diseño para el selector de estilos que se muestra en cada campo draggable
            var selectorEstilo = '';
        	var tabla = '<table class="table table-striped" style="width:100%">'+
			    '<tbody>';
			      // href="javascript:editarEstiloInforme(\'insertar\',0)" 
			tabla += '<tr>'+
						'<th style="width:40px;padding: 1px 8px;" data-orderable="false">'+
	                    	'<a href="javascript:editarEstiloInforme(\'insertar\',0)"><span class="glyphicon glyphicon-plus"></span></a>'+
	                      '</th>'+
	                      '<th>'+
	                      '	<b>Nombre</b>'+
	                    '</th>'+
	                 '</tr>';

            for(var i=0; i < data.length; i++)
            { 
            	
                var estilo = 'style="background-color:'+data[i].colorFondoEstiloInforme+';'+
            						'border: solid 1px '+data[i].colorBordeEstiloInforme+';'+
            						'color: '+data[i].colorTextoEstiloInforme+';'+
            						'font-family: '+data[i].fuenteTextoEstiloInforme+';'+
            						'font-size: '+data[i].tamañoTextoEstiloInforme+';'+
            						'font-weight: '+(data[i].negrillaEstiloInforme == 1 ? 'bold' : 'normal')+';'+
                                    'font-style: '+(data[i].italicaEstiloInforme == 1 ? 'italic' : '')+';'+
                                    'text-decoration: '+(data[i].subrayadoEstiloInforme == 1 ? 'underline' : '')+';'+
            					'"';

                var estiloSeleccion = 'style="background-color:'+data[i].colorFondoEstiloInforme+';'+
                                    'border: solid 1px '+data[i].colorBordeEstiloInforme+';'+
                                    'color: '+data[i].colorTextoEstiloInforme+';'+
                                    'font-family: '+data[i].fuenteTextoEstiloInforme+';'+
                                    'font-weight: '+(data[i].negrillaEstiloInforme == 1 ? 'bold' : 'normal')+';'+
                                    'font-style: '+(data[i].italicaEstiloInforme == 1 ? 'italic' : '')+';'+
                                    'text-decoration: '+(data[i].subrayadoEstiloInforme == 1 ? 'underline' : '')+';'+
                                'width: 100%; height: 100%;"';

                selectorEstilo += '<a  class="botonEstilo" '+estiloSeleccion+' ><span id="'+data[i].idEstiloInforme+'" onclick="asignarEstiloControl(this);" title="'+data[i].nombreEstiloInforme+'">'+data[i].nombreEstiloInforme+'</span></a>';

            	tabla += '<tr>'+
            		'<td><a href="javascript:editarEstiloInforme(\'modificar\','+data[i].idEstiloInforme+')">'+
                            '<span class="glyphicon glyphicon-pencil"></span>'+
                        '</a>&nbsp;'+
                        '<a href="javascript:editarEstiloInforme(\'eliminar\','+data[i].idEstiloInforme+')">'+
                            '<span class="glyphicon glyphicon-trash"></span>'+
                        '</a>'+
                    '</td>'+
			        '<td '+estilo+'><span>'+data[i].nombreEstiloInforme+'</span></td>'+
			      '</tr>';
            }

	        tabla += '</tbody>'+
			'</table>';

                
            $(".selectorEstilo").html(selectorEstilo) ; 
			$("#estilos").html(tabla) ; 


			// con los mismos datos, cambiamos la lista de seleccion de estilos del modal de conceptos
			var select = document.getElementById('conceptoEstilo');
            
            select.options.length = 0;
            var option = '';

            option = document.createElement('option');
            option.value = '';
            option.text = 'Seleccione el Estilo...';
            select.appendChild(option);

            for(var j=0,k=data.length;j<k;j++)
            {
				option = document.createElement('option');
                option.value = data[j].idEstiloInforme;
                option.text = data[j].nombreEstiloInforme;
                option.style = 	'height: 30px;'+
                				'background-color:'+data[j].colorFondoEstiloInforme+';'+
				                'border: solid 1px '+data[j].colorBordeEstiloInforme+';'+
				                'color:'+data[j].colorTextoEstiloInforme+';'+
				                'font-family:'+data[j].fuenteTextoEstiloInforme+';'+
				                'font-size:'+data[j].tamañoTextoEstiloInforme+'px;'+
				                'font-weight:'+(data[j].negrillaEstiloInforme == 1 ? 'bold' : 'normal')+';'+
				                'font-style:'+(data[j].italicaEstiloInforme == 1 ? 'italic' : 'normal')+';'+
				                'text-decoration:'+(data[j].subrayadoEstiloInforme == 1 ? 'underline' : 'normal')+';';
                // option.selected = (document.getElementById("Ausentismo_idAusentismo").value == data[j].idEstiloInforme ? true : false);
                select.appendChild(option);

            }

        },
        error:    function(xhr,err){
            alert('Se ha producido un error: ' +err);
        }
    });

}


function asignarEstiloControl(objSelector)
{
    // a través de los padres, obtenemos el id del div principal que es el del objeto de tipo etiqueta
    var idDiv = $(objSelector).closest('ul').parent().parent().attr('id');
    // este id lo utilizamos para concatenarlo y obtener los ids del estilo (oculto) y del label
    $("#Estilo-"+idDiv).val($(objSelector).attr('id'));
    $("#Etiqueta-"+idDiv).attr('style',($(objSelector).parent().attr('style')));
    $("#Campo-"+idDiv).attr('style',($(objSelector).parent().attr('style')));
}

/****************************************************
**
** VISTA MODAL: ESTILOS DE INFORME
**
****************************************************/
function editarEstiloInforme(accion, id)
{
    var token = document.getElementById('token').value;
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        dataType: "json",
        url:   'http://'+location.host+'/consultarEstiloInforme',
        data: {accion: accion, idEstiloInforme: id},
        type:  'post',
        beforeSend: function(){
            },
        success: function(data){
        	$("#accionEstiloInforme").attr('value', accion);

            for(var i=0; i < data.length; i++)
            { 

                $("#idEstiloInforme").attr('value', data[i].idEstiloInforme);
                $("#nombreEstiloInforme").attr('value', data[i].nombreEstiloInforme);

                $("#background-color").attr('value', data[i].colorFondoEstiloInforme);
                $("#CPbackground-color").colorpicker('setValue', data[i].colorFondoEstiloInforme); 

                $("#border").attr('value', data[i].colorBordeEstiloInforme);
                $("#CPborder").colorpicker('setValue', data[i].colorBordeEstiloInforme); 

                $("#color").attr('value', data[i].colorTextoEstiloInforme);
                $("#CPcolor").colorpicker('setValue', data[i].colorTextoEstiloInforme); 

                $('#font-family option[value=\''+data[i].fuenteTextoEstiloInforme+'\']').prop('selected', true);
                $('#font-size option[value=\''+data[i].tamañoTextoEstiloInforme+'\']').prop('selected', true);

                $("#font-weight").attr('checked', (data[i].negrillaEstiloInforme == 1 ? true : false));
                $("#font-style").attr('checked', (data[i].italicaEstiloInforme == 1 ? true : false));
                $("#text-decoration").attr('checked', (data[i].subrayadoEstiloInforme == 1 ? true : false));

            }
            
            $("#guardarEstilo").html(accion.charAt(0).toUpperCase() + accion.slice(1));
            $("#guardarEstilo").attr('class', (accion == 'insertar' ? 'btn btn-success' : (accion == 'modificar' ? 'btn btn-warning' : 'btn btn-danger')));
            
            $("#ModalEstilo").modal('show');
        },
        error:    function(xhr,err){
            alert('Se ha producido un error al cargar los estilos');
        }
    });

}



/****************************************************
**
** ALMACENAMIENTO: GUARDAR ESTILO Y OCULTAR MODAL
**
****************************************************/
function OcultarEstilo(accion)
{
	var valores = new Array();
	valores[0] = $('#idEstiloInforme').val();
	valores[1] = $('#nombreEstiloInforme').val();
	valores[2] = $('#background-color').val();
	valores[3] = $('#border').val();
	valores[4] = $('#color').val();
	valores[5] = $('#font-family').val();
	valores[6] = $('#font-size').val();
	valores[7] = ($('#font-weight').is(':checked') ? 1 : 0);
	valores[8] = ($('#font-style').is(':checked') ? 1 : 0);
	valores[9] = ($('#text-decoration').is(':checked') ? 1 : 0);

	var accion = (accion) ? accion : $("#accionEstiloInforme").val();
	id = 0;
	if(accion != 'cancelar')
	{
		var token = document.getElementById('token').value;
	    $.ajax({
	        headers: {'X-CSRF-TOKEN': token},
	        dataType: "json",
	        url:   'http://'+location.host+'/guardarEstiloInforme',
	        data: {accion: accion, idEstiloInforme: $("#idEstiloInforme").val(), valores: valores},
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
	cargarEstiloInforme();
	$("#ModalEstilo").modal('hide');


}



/****************************************************
**
** VISTA MODAL: SISTEMAS DE INFORMACION
**
****************************************************/
function editarSistemaInformacion(accion, id)
{
    var token = document.getElementById('token').value;
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        dataType: "json",
        url:   'http://'+location.host+'/consultarSistemaInformacion',
        data: {accion: accion, idSistemaInformacion: id},
        type:  'post',
        beforeSend: function(){
            },
        success: function(data){
        	
        	$("#accionSistemaInformacion").attr('value', accion);

            for(var i=0; i < data.length; i++)
            { 

                $("#idSistemaInformacion").attr('value', data[i].idSistemaInformacion);
                $("#codigoSistemaInformacion").attr('value', data[i].codigoSistemaInformacion);
                $("#nombreSistemaInformacion").attr('value', data[i].nombreSistemaInformacion);
                $("#webSistemaInformacion").attr('value', data[i].webSistemaInformacion);
                $("#ipSistemaInformacion").attr('value', data[i].ipSistemaInformacion);
                $("#puertoSistemaInformacion").attr('value', data[i].puertoSistemaInformacion);
                $("#usuarioSistemaInformacion").attr('value', data[i].usuarioSistemaInformacion);
                $("#claveSistemaInformacion").attr('value', data[i].claveSistemaInformacion);
                $("#bdSistemaInformacion").attr('value', data[i].bdSistemaInformacion);
                $("#motorbdSistemaInformacion").attr('value', data[i].motorbdSistemaInformacion);
               
            }
            $("#guardarSistema").html(accion.charAt(0).toUpperCase() + accion.slice(1));
            $("#guardarSistema").attr('class', (accion == 'insertar' ? 'btn btn-success' : (accion == 'modificar' ? 'btn btn-warning' : 'btn btn-danger')));

            $("#ModalSistema").modal('show');
        },
        error:    function(xhr,err){
            alert('Se ha producido un error al cargar los estilos');
        }
    });

}



/****************************************************
**
** ALMACENAMIENTO: GUARDAR SISTEMAS DE INFORMACION Y OCULTAR MODAL
**
****************************************************/
function OcultarSistema(accion)
{

	var valores = new Array();
	valores[0] = $('#idSistemaInformacion').val();
    valores[1] = $('#codigoSistemaInformacion').val();
	valores[2] = $('#nombreSistemaInformacion').val();
	valores[3] = ($('#webSistemaInformacion').is(':checked') ? 1 : 0);
	valores[4] = $('#ipSistemaInformacion').val();
	valores[5] = $('#puertoSistemaInformacion').val();
	valores[6] = $('#usuarioSistemaInformacion').val();
	valores[7] = $('#claveSistemaInformacion').val();
	valores[8] = $('#bdSistemaInformacion').val();
	valores[9] = $('#motorbdSistemaInformacion').val();
	

	var accion = (accion) ? accion : $("#accionSistemaInformacion").val();
	id = 0;
	if(accion != 'cancelar')
	{
		var token = document.getElementById('token').value;
	    $.ajax({
	        headers: {'X-CSRF-TOKEN': token},
	        dataType: "json",
	        url:   'http://'+location.host+'/guardarSistemaInformacion',
	        data: {accion: accion, idSistemaInformacion: $("#idSistemaInformacion").val(), valores: valores},
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
	cargarSistemaInformacion();
	$("#ModalSistema").modal('hide');

}



/****************************************************
**
** CARGAR ENCABEZADO DE INFORME
**
****************************************************/
function cargarInforme(idInforme, accion)
{
    if(idInforme != 0)
    {
        var token = document.getElementById('token').value;
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            url:   'http://'+location.host+'/consultarInforme',
            data: {accion: accion, idInforme: idInforme},
            type:  'post',
            beforeSend: function(){
                },
            success: function(data){
                // con el id de informe consultamos los datos de encabezado y los llenamos en la vista
                $("#idInforme").val(data[0].idInforme); 
                $("#nombreInforme").val(data[0].nombreInforme); 
                $("#descripcionInforme").val(data[0].descripcionInforme); 

                // dependiendo de la accion se habilitan o inhabilitan los campos
                if(accion == 'adicionar' || accion == 'modificar')
                {
                    $("#idInforme").prop('readonly', false); 
                    $("#nombreInforme").prop('readonly', false);
                    $("#descripcionInforme").prop('readonly', false);
                }   
                else 
                {
                    $("#idInforme").prop('readonly', true); 
                    $("#nombreInforme").prop('readonly', true);
                    $("#descripcionInforme").prop('readonly', true);
                }         



            },
            error:    function(xhr,err){
                alert('Se ha generado un error: ' +err);
            }
        });
    }
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
                for(var i=0; i < data.length; i++)
                {
                    adicionarCapa(data[i].tipoInformeCapa, data[i].idInformeCapa, 
                        accion, data[i].SistemaInformacion_idSistemaInformacion,
                        data[i].tablaInformeCapa);
                }

            },
            error:    function(xhr,err){
                alert('Se ha producido un error: ' +err);
            }
        });
    }
}


function adicionarCapa(tipo, idInformeCapa, accion, idSistema, tabla)
{
    totalCapas++;
    //
    // $('#tabcapa').append($('<li><a href="#tab' + totalCapas + '" role="tab" data-toggle="tab">Tab ' + totalCapas + '<button class="close" type="button" title="Remove this page">×</button></a></li>'));
    $("#tabcapa").append('<li id="hojacapa'+totalCapas+'" >'+
                                '<input type="hidden" id="idInformeCapa'+totalCapas+'" value="'+idInformeCapa+'">'+
                                '<input type="hidden" id="tipoInformeCapa'+totalCapas+'" value="'+tipo+'">'+
                                '<input type="hidden" id="idSistemaInformacion'+totalCapas+'" value="">'+
                                '<input type="hidden" id="nombreTabla'+totalCapas+'" value="">'+
                                '<a data-toggle="tab" href="#capa'+totalCapas+'" onclick="seleccionarCapa('+totalCapas+');">'+
                                        'Capa '+totalCapas+
                                        '&nbsp;&nbsp;&nbsp;'+
                                        '<button class="close" type="button" title="Eliminar esta Capa" onclick="eliminarCapa('+totalCapas+');">×</button>'+
                                '</a>'+
                        '</li>');
    
    // Luego de crear la pestaña de la capa llenamos los campos de idSistemaInformacion y nombreTabla con la
    // informacion del panel de conexion a la base de datos 
    $("#idSistemaInformacion"+totalCapas).val(idSistema);
    $("#nombreTabla"+totalCapas).val(tabla);    


    // con los datos del sistema y tabla de la capa, ejecutamos la funcion que selecciona la capa
    // con el fin de que se consulte la Base de datos y la tabla asociada a ésta
    seleccionarCapa(totalCapas);

    $("#contentcapa").append(
                    '<div  class="tab-pane" id="capa'+totalCapas+'">'+
                    '   <div id="layoutEncabezado'+totalCapas+'" class="panel panel-primary panelLayout" >'+
                    '     <div class="panel-heading">Encabezado de Página'+
                    '           <div style="float: right;">'+
                    '               <span class="glyphicon glyphicon-chevron-up" title="Reducir Altura" onclick="cambiarTamañoPanel(\'layoutEncabezado\','+totalCapas+', \'R\');"></span>'+
                    '               <span class="glyphicon glyphicon-chevron-down" title="Aumentar Altura" onclick="cambiarTamañoPanel(\'layoutEncabezado\','+totalCapas+', \'A\');"></span>'+
                    '           </div>'+
                    '     </div>'+
                    '     <div id="layoutEncabezadoContenedor'+totalCapas+'" class="panel-body contenedorCampos Banda" style="height: 113px; ">'+
                    '     </div>'+
                    '   </div>'+
                (tipo == 1 
                ?
                    '   <div id="layoutDetalle'+totalCapas+'" class="panel panel-primary panelLayout" style="height: 100px;">'+
                    '     <div class="panel-heading">Detalle'+
                    '           <div style="float: right;">'+
                    '               <span class="glyphicon glyphicon-chevron-up" title="Reducir Altura" onclick="cambiarTamañoPanel(\'layoutDetalle\','+totalCapas+', \'R\');"></span>'+
                    '               <span class="glyphicon glyphicon-chevron-down" title="Aumentar Altura" onclick="cambiarTamañoPanel(\'layoutDetalle\','+totalCapas+', \'A\');"></span>'+
                    '           </div>'+
                    '     </div>'+
                    '     <div id="layoutDetalleContenedor'+totalCapas+'" class="panel-body contenedorCampos Banda" style="height: 58px; overflow: auto;">'+
                    '     </div>'+
                    '   </div>'
                :
                    '   <div id="layoutDetalle'+totalCapas+'" class="panel panel-primary panelLayout" style="height: 345px;">'+
                    '     <div class="panel-heading">Conceptos del Informe'+
                    '           <div style="float: right;">'+
                    '               <span class="glyphicon glyphicon-plus" title="Adicionar Concepto" onclick="MostrarConcepto(\'insertar\');"></span>'+
                    '               <span class="glyphicon glyphicon-chevron-up" title="Reducir Altura" onclick="cambiarTamañoPanel(\'layoutDetalle\','+totalCapas+', \'R\');"></span>'+
                    '               <span class="glyphicon glyphicon-chevron-down" title="Aumentar Altura" onclick="cambiarTamañoPanel(\'layoutDetalle\','+totalCapas+', \'A\');"></span>'+
                    '           </div>'+
                    '     </div>'+
                    '     <div id="layoutDetalleContenedor'+totalCapas+'" class="panel-body Banda" style="height: 300px; overflow: auto;">'+
                    '       <ul id="sortable'+totalCapas+'">'+
                    '       </ul>'+
                    '     </div>'+
                    '   </div>'
                )+
                    '   <div id="layoutPie'+totalCapas+'" class="panel panel-primary panelLayout">'+
                    '     <div class="panel-heading">Pié de Página'+
                    '           <div style="float: right;">'+
                    '               <span class="glyphicon glyphicon-chevron-up" title="Reducir Altura" onclick="cambiarTamañoPanel(\'layoutPie\','+totalCapas+', \'R\');"></span>'+
                    '               <span class="glyphicon glyphicon-chevron-down" title="Aumentar Altura" onclick="cambiarTamañoPanel(\'layoutPie\','+totalCapas+', \'A\');"></span>'+
                    '           </div>'+
                    '     </div>'+
                    '     <div id="layoutPieContenedor'+totalCapas+'" class="panel-body contenedorCampos Banda" style="height: 113px;">'+
                    '     </div>'+
                    '   </div>'+
                    '</div>');


    $(".contenedorCampos")
    .droppable(
    {
       drop: function(e, ui)
       {
            if(ui.draggable.hasClass("campoClonable")) 
            {
                // Lo primero es adicionarle al div contenedor un clon del objeto arrastrado
                $(this).append($(ui.helper).clone());

                // buscamos en la ruta de el objeto con clase contenedorCampos y clase etiquetaClonable, para adicionarles 
                // las clases item (le da una clase con nombre unico) y campoTamaño
                $(".contenedorCampos .campoClonable").addClass("item-"+counts[0]);
                $(".contenedorCampos .campoClonable").addClass("campoTamaño-"+counts[0]);

                // luego al objeto de clase item-# le asociamos un ID con el mismo nombre y le quitamos los tootltip para que no estorben en el modo de diseño
                $(".contenedorCampos .item-"+counts[0]).attr("id", "item-"+counts[0]); 
                $(".contenedorCampos .item-"+counts[0]).attr("title", ""); 

                // al objeto clonado le adicionamos etiquetas de HTML necesarias apara la funcionalidad requerida, en este caso de las etiquetas, seria:
                // un input oculto para cuando el usuario desee cambiar el texto de la etiqueta               
                // un input oculto para guardar el id de estilo que le asocie el usuario       
                // un div con el texto de la etiqueta y el estilo seleccionado por el usuario
                // un div con el selector de estilos y el boton para eliminar el objeto del diseñador        
                $( "#item-"+counts[0] ).html( 
                    '<input type="hidden" id="Contenido-item-'+counts[0]+'" value="'+$( "#item-"+counts[0]+" input").val( )+'" >'+
                    '<input type="hidden" id="Estilo-item-'+counts[0]+'" value="" >'+
                    '<input type="hidden" id="Tipo-item-'+counts[0]+'" value="CampoClon" >'+
                    '<div  style="width: 100%; height: 100%;" id="Campo-item-'+counts[0]+'">'+$( "#item-"+counts[0] ).html( )+'</div>'+
                    '<div id="opcionesitem-'+counts[0]+'" style="display:block; float: right;">'+
                    '       <ul class="dropgroup">'+
                    '         <li class="dropdown">'+
                    '           <a href="#"  class="dropbtn"><span  class="fa fa-paint-brush" ></span></a>'+
                    '           <div class="dropdown-content selectorEstilo">'+
                    '           </div>'+
                    '         </li>'+
                    '       </ul>'+
                    '   <span class="fa fa-times-circle" title="Eliminar" onclick="$(\'#item-'+counts[0]+'\').remove();" ></span>'+
                    '</div>'
                );

                // a nuestro objeto le habilitamos los eventos:
                // Mouseover para que al pasar el mouse se muestre el div de selector de estilos y boton de eliminar
                // Mouseleave para que se oculten al quitar el mouse
                $("#item-"+counts[0]).attr("onmouseover", "$(this).find(\'#opcionesitem-"+counts[0]+"\').show();"); 
                $("#item-"+counts[0]).attr("onmouseleave", "$(this).find(\'#opcionesitem-"+counts[0]+"\').hide();");


                // en el evento Doble Click habilitamos la funcionalisis propia del objeto de etiquetas que es permitirle al 
                // usuario digitar el texto y que el sistema lo ponga fijo en el div
                $("#item-"+counts[0]).dblclick(function() 
                {
                    
                });

                // Quitamos las clases del objeto de etiquetas original (de antes de arrastrarlo) (ui-draggable, EtiquetaBase y etiquetaClonable)
                $("#item-"+counts[0]).removeClass("campoClonable ui-draggable ui-draggable-dragging");
                $("#item-"+counts[0]).removeClass("CampoBase");
                // le adicionamos la clase de Etiqueta clon para que le de la nueva apariencia de objeto de etiqueta en el diseñador
                $("#item-"+counts[0]).addClass("CampoClon");
                
                   
                // al soltar el objeto sobre el contenedor, calculamos la distancia para ubicarlo
                izq = $(".item-"+counts[0]).position().left - $(this).position().left;
                sup = $(".item-"+counts[0]).position().top - $(this).position().top;
                
                // asignamos el estilo de posicion relativa y las ubicaciones top y left calculadas
                $("#item-"+counts[0]).css({position: 'relative'});
                $("#item-"+counts[0]).css({top: sup, left: izq});

                // ejecutamos la funcion que lo convierte en objeto arrastrable y le damos propiedades de cambio de tamaño (resizable)
                make_draggable($(".item-"+counts[0])); 
                $("#item-"+counts[0]).resizable(tamañoCampo);   

            }
            else if(ui.draggable.hasClass("imagenClonable")) 
            {
                
                // Lo primero es adicionarle al div contenedor un clon del objeto arrastrado
                $(this).append($(ui.helper).clone());

                // buscamos en la ruta de el objeto con clase contenedorCampos y clase imagenClonable, para adicionarles 
                // las clases item (le da una clase con nombre unico) y campoTamaño
                $(".contenedorCampos .imagenClonable").addClass("item-"+counts[0]);
                $(".contenedorCampos .imagenClonable").addClass("campoTamaño-"+counts[0]);

                // luego al objeto de clase item-# le asociamos un ID con el mismo nombre y le quitamos los tootltip para que no estorben en el modo de diseño
                $(".contenedorCampos .item-"+counts[0]).attr("id", "item-"+counts[0]); 
                $(".contenedorCampos .item-"+counts[0]).attr("title", ""); 

                // al objeto clonado le adicionamos etiquetas de HTML necesarias apara la funcionalidad requerida, en este caso de las imagenes, seria:
                $( "#item-"+counts[0] ).append( 
                    '<input type="hidden" id="Contenido-item-'+counts[0]+'" value="" placeholder="Texto" style="width: 100%; height: 100%;">'+
                    '<input type="hidden" id="Estilo-item-'+counts[0]+'" value="" >'+
                    '<div  style="width: 100%; height: 100%;" id="Imagen-item-'+counts[0]+'">Imagen</div>'+
                    '<div id="opcionesitem-'+counts[0]+'" style="display:block; float: right;">'+
                    '       <ul class="dropgroup">'+
                    '         <li class="dropdown">'+
                    '           <a href="#"  class="dropbtn"><span  class="fa fa-paint-brush" ></span></a>'+
                    '           <div class="dropdown-content selectorEstilo">'+
                    '           </div>'+
                    '         </li>'+
                    '       </ul>'+
                    '   <span class="fa fa-times-circle" title="Eliminar" onclick="$(\'#item-'+counts[0]+'\').remove();" ></span>'+
                    '</div>'
                );

                // a nuestro objeto le habilitamos los eventos:
                // Mouseover para que al pasar el mouse se muestre el div de selector de estilos y boton de eliminar
                // Mouseleave para que se oculten al quitar el mouse
                $("#item-"+counts[0]).attr("onmouseover", "$(this).find(\'#opcionesitem-"+counts[0]+"\').show();"); 
                $("#item-"+counts[0]).attr("onmouseleave", "$(this).find(\'#opcionesitem-"+counts[0]+"\').hide();");


                // en el evento Doble Click habilitamos la funcionalidad propia del objeto de imagenes que es permitirle al 
                // usuario cargar una imagen y que el sistema lo ponga fijo en el div
                $("#item-"+counts[0]).dblclick(function() 
                {
                    // lo que hacemos es ocultar el texto del div y mostrar el campo input para que se digite el nuevo texto
                    $("#Imagen-"+$(this).prop('id')).css('display',  'none');
                    $("#Contenido-"+$(this).prop('id')).prop('style', 'width: 100%; height: 100%;');
                    $("#Contenido-"+$(this).prop('id')).prop('type','text');
                    $("#Contenido-"+$(this).prop('id')).prop('title','Arrastre un archivo de image');
                    $("#Contenido-"+$(this).prop('id')).focus(); 

                    // luego de que el usuario digite el nuevo texto, deberá salir del campo para que se ejecute el evento BLUR
                    $("#Contenido-"+$(this).prop('id')).blur(function() 
                    {
                        // en este hacemos lo contrario, ocultamos el input y mostramos el div y le asignamos el texto del input al div fijo
                        $(this).prop('type','hidden');
                        idImagen = "#"+$(this).prop('id').replace('Contenido','Imagen');
                        $(idImagen).prop('innerHTML', $(this).val());
                        $(idImagen).css('display',  'block');
                    });
                });

                // Quitamos las clases del objeto de imagenes original (de antes de arrastrarlo) (ui-draggable, ImagenBase y imagenClonable)
                $("#item-"+counts[0]).removeClass("imagenClonable ui-draggable ui-draggable-dragging");
                $("#item-"+counts[0]).removeClass("ImagenBase");
                // le adicionamos la clase de Imagen clon para que le de la nueva apariencia de objeto de imagen en el diseñador
                $("#item-"+counts[0]).addClass("ImagenClon");
                
                   
                // al soltar el objeto sobre el contenedor, calculamos la distancia para ubicarlo
                izq = $(".item-"+counts[0]).position().left - $(this).position().left;
                sup = $(".item-"+counts[0]).position().top - $(this).position().top;
                
                // asignamos el estilo de posicion relativa y las ubicaciones top y left calculadas
                $("#item-"+counts[0]).css({position: 'relative'});
                $("#item-"+counts[0]).css({top: sup, left: izq});

                // ejecutamos la funcion que lo convierte en objeto arrastrable y le damos propiedades de cambio de tamaño (resizable)
                make_draggable($(".item-"+counts[0])); 
                $("#item-"+counts[0]).resizable(tamañoCampo);      
            }
            else if(ui.draggable.hasClass("editorClonable")) 
            {
                
                $(this).append($(ui.helper).clone());

                //Pointing to the editorClonable class in contenedorCampos and add new class.
                $(".contenedorCampos .editorClonable").addClass("item-"+counts[0]);
                $(".contenedorCampos .editorClonable").addClass("campoTamaño-"+counts[0]);
                            
                //Remove the current class (ui-draggable and editorClonable)
                $(".contenedorCampos .item-"+counts[0]).removeClass("editorClonable ui-draggable ui-draggable-dragging");
                $(".contenedorCampos .item-"+counts[0]).removeClass("editorBase");
                $(".contenedorCampos .item-"+counts[0]).addClass("EditorClon");
                
                $(".item-"+counts[0]).dblclick(function() 
                {
                    $(this).remove();
                });     


                izq = $(".item-"+counts[0]).position().left - $(this).position().left;
                sup = $(".item-"+counts[0]).position().top - $(this).position().top;
                
                $(".item-"+counts[0]).css({position: 'relative'});
                $(".item-"+counts[0]).css({top: sup, left: izq});

                make_draggable($(".item-"+counts[0])); 
                $(".item-"+counts[0]).resizable(tamañoImagen);     
            }
            else if(ui.draggable.hasClass("etiquetaClonable")) 
            {
                // Lo primero es adicionarle al div contenedor un clon del objeto arrastrado
                $(this).append($(ui.helper).clone());

                // buscamos en la ruta de el objeto con clase contenedorCampos y clase etiquetaClonable, para adicionarles 
                // las clases item (le da una clase con nombre unico) y campoTamaño
                $(".contenedorCampos .etiquetaClonable").addClass("item-"+counts[0]);
                $(".contenedorCampos .etiquetaClonable").addClass("campoTamaño-"+counts[0]);

                // luego al objeto de clase item-# le asociamos un ID con el mismo nombre y le quitamos los tootltip para que no estorben en el modo de diseño
                $(".contenedorCampos .item-"+counts[0]).attr("id", "item-"+counts[0]); 
                $(".contenedorCampos .item-"+counts[0]).attr("title", ""); 

                // al objeto clonado le adicionamos etiquetas de HTML necesarias apara la funcionalidad requerida, en este caso de las etiquetas, seria:
                // un input oculto para cuando el usuario desee cambiar el texto de la etiqueta               
                // un input oculto para guardar el id de estilo que le asocie el usuario       
                // un div con el texto de la etiqueta y el estilo seleccionado por el usuario
                // un div con el selector de estilos y el boton para eliminar el objeto del diseñador        
                $( "#item-"+counts[0] ).append( 
                    '<input type="hidden" id="Contenido-item-'+counts[0]+'" value="" placeholder="Texto" style="width: 100%; height: 100%;">'+
                    '<input type="hidden" id="Estilo-item-'+counts[0]+'" value="" >'+
                    '<input type="hidden" id="Tipo-item-'+counts[0]+'" value="EtiquetaClon" >'+
                    '<div  style="width: 100%; height: 100%;" id="Etiqueta-item-'+counts[0]+'"><span class="fa fa-tag">&nbsp;</span>Texto</div>'+
                    '<div id="opcionesitem-'+counts[0]+'" style="display:block; float: right;">'+
                    '       <ul class="dropgroup">'+
                    '         <li class="dropdown">'+
                    '           <a href="#"  class="dropbtn"><span  class="fa fa-paint-brush" ></span></a>'+
                    '           <div class="dropdown-content selectorEstilo">'+
                    '           </div>'+
                    '         </li>'+
                    '       </ul>'+
                    '   <span class="fa fa-times-circle" title="Eliminar" onclick="$(\'#item-'+counts[0]+'\').remove();" ></span>'+
                    '</div>'
                );

                // a nuestro objeto le habilitamos los eventos:
                // Mouseover para que al pasar el mouse se muestre el div de selector de estilos y boton de eliminar
                // Mouseleave para que se oculten al quitar el mouse
                $("#item-"+counts[0]).attr("onmouseover", "$(this).find(\'#opcionesitem-"+counts[0]+"\').show();"); 
                $("#item-"+counts[0]).attr("onmouseleave", "$(this).find(\'#opcionesitem-"+counts[0]+"\').hide();");


                // en el evento Doble Click habilitamos la funcionalisis propia del objeto de etiquetas que es permitirle al 
                // usuario digitar el texto y que el sistema lo ponga fijo en el div
                $("#item-"+counts[0]).dblclick(function() 
                {
                    // lo que hacemos es ocultar el texto del div y mostrar el campo input para que se digite el nuevo texto
                    $("#Etiqueta-"+$(this).prop('id')).css('display',  'none');
                    $("#Contenido-"+$(this).prop('id')).prop('style', 'width: 100%; height: 100%;');
                    $("#Contenido-"+$(this).prop('id')).prop('type','text');
                    $("#Contenido-"+$(this).prop('id')).prop('title','Ingrese el texto de la etiqueta');
                    $("#Contenido-"+$(this).prop('id')).focus(); 

                    // luego de que el usuario digite el nuevo texto, deberá salir del campo para que se ejecute el evento BLUR
                    $("#Contenido-"+$(this).prop('id')).blur(function() 
                    {
                        // en este hacemos lo contrario, ocultamos el input y mostramos el div y le asignamos el texto del input al div fijo
                        $(this).prop('type','hidden');
                        idEtiqueta = "#"+$(this).prop('id').replace('Contenido','Etiqueta');
                        $(idEtiqueta).prop('innerHTML', $(this).val());
                        $(idEtiqueta).css('display',  'block');
                    });
                });

                // Quitamos las clases del objeto de etiquetas original (de antes de arrastrarlo) (ui-draggable, EtiquetaBase y etiquetaClonable)
                $("#item-"+counts[0]).removeClass("etiquetaClonable ui-draggable ui-draggable-dragging");
                $("#item-"+counts[0]).removeClass("EtiquetaBase");
                // le adicionamos la clase de Etiqueta clon para que le de la nueva apariencia de objeto de etiqueta en el diseñador
                $("#item-"+counts[0]).addClass("EtiquetaClon");
                
                   
                // al soltar el objeto sobre el contenedor, calculamos la distancia para ubicarlo
                izq = $(".item-"+counts[0]).position().left - $(this).position().left;
                sup = $(".item-"+counts[0]).position().top - $(this).position().top;
                
                // asignamos el estilo de posicion relativa y las ubicaciones top y left calculadas
                $("#item-"+counts[0]).css({position: 'relative'});
                $("#item-"+counts[0]).css({top: 0, left: izq});

                // ejecutamos la funcion que lo convierte en objeto arrastrable y le damos propiedades de cambio de tamaño (resizable)
                make_draggable($(".item-"+counts[0])); 
                $("#item-"+counts[0]).resizable(tamañoCampo);   


            }

            // Cada que adicionemos un campo, debemos ejecutar el proceso que les actualiza el selector de estilos a todos
            cargarEstiloInforme();

        }
    });


    // ejecutamos la carga de objetos en la capa creada
    cargarInformeObjeto(idInformeCapa, accion);

    // ejecutamos la carga de conceptos contables en la capa creada
    cargarInformeConcepto(idInformeCapa, accion);

    // seleccionamos la ultima capa
    $('#tabcapa a:last').tab('show'); 
}


/****************************************************
**
** SELECCIONAR LA CAPA
**
****************************************************/
function seleccionarCapa(numeroCapa)
{
    // cuando el usuario hace click sobre la capa, el sistema debe actualizar el sistema de informacion y 
    // el nombre de la tabla de la BD en el panel de conexion a  la base de datos, ya que cada capa puede 
    // tener una diferente conexion

    // si el id de sistema o la tabla de la capa son diferentes al del panel de conexion
    if(($("#idSistemaInformacion"+numeroCapa).val() != $("#sistemaInformacion").val()) ||
        ($("#nombreTabla"+numeroCapa).val() != $("#tablaDocumento").val()))
    {
        // Si la capa tiene id de sistema y no es el mismo del panel de conexion
        if($("#idSistemaInformacion"+numeroCapa).val() > 0 &&
            $("#idSistemaInformacion"+numeroCapa).val() != $("#sistemaInformacion").val())
        {
            // Consultamos las tablas del sistema de informacion
            consultarTablaVista($("#idSistemaInformacion"+numeroCapa).val(), $("#nombreTabla"+numeroCapa).val());
            // selecciona el sistema en la lista desplegable
            $('#sistemaInformacion option[value="'+$("#idSistemaInformacion"+numeroCapa).val()+'"]').prop('selected', true);
        
        } 
        

        // consultamos los campos de la tabla o vista
        if($("#nombreTabla"+numeroCapa).val() != '' && $("#nombreTabla"+numeroCapa).val() != null)
        {
            
            // despues de que consultemos la base de datos, debemos reasignarle a la lista de tablas el 
            // nombre de la tabla que maneja esta capa
            
            consultarCampos($("#idSistemaInformacion"+numeroCapa).val(), $("#nombreTabla"+numeroCapa).val());

        }
        
    }

} 


/****************************************************
**
** ELIMINAR LA INFORMACION DE LA CAPA
**
****************************************************/
function eliminarCapa(numeroCapa)
{
    if(confirm("Esta seguro de eliminar todo el contenido de la capa "+numeroCapa+"?"))
    {
        $("#capa"+numeroCapa).remove();
        $("#hojacapa"+numeroCapa).remove();
    }

}


function cargarInformeConcepto(idInformeCapa, accion)
{
    var token = document.getElementById('token').value;
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        dataType: "json",
        url:   'http://'+location.host+'/consultarInformeConcepto',
        data: {accion: accion, idInformeCapa: idInformeCapa},
        type:  'post',
        beforeSend: function(){
            },
        error:    function(xhr,err){
            alert('se genero error: ' + err);
            },
        success: function(data){

            var valores = Array();
            for(var i=0; i < data.length; i++)
            {
                
                var estilo =    'background-color:'+data[i].colorFondoEstiloInforme+';'+
                                'border: solid 1px '+data[i].colorBordeEstiloInforme+';'+
                                'color: '+data[i].colorTextoEstiloInforme+';'+
                                'font-family: '+data[i].fuenteTextoEstiloInforme+';'+
                                'font-size: '+data[i].tamañoTextoEstiloInforme+';'+
                                'font-weight: '+(data[i].negrillaEstiloInforme == 1 ? 'bold' : 'normal')+';'+
                                'font-style: '+(data[i].italicaEstiloInforme == 1 ? 'italic' : '')+';'+
                                'text-decoration: '+(data[i].subrayadoEstiloInforme == 1 ? 'underline' : '')+';';

                valores[0] = data[i].nombreInformeConcepto;
                valores[1] = data[i].tipoMovimientoInformeConcepto;
                valores[2] = data[i].tipoValorInformeConcepto;
                valores[3] = data[i].valorInformeConcepto;
                valores[4] = estilo;
                valores[5] = data[i].detalleInformeConcepto;
                valores[6] = data[i].resumenInformeConcepto;
                valores[7] = data[i].graficoInformeConcepto;
                valores[8] = data[i].EstiloInforme_idEstiloInforme;
                valores[9] = data[i].idInformeConcepto;
                valores[10] = data[i].excluirTerceroInformeConcepto;

                adicionarConcepto(data[i].nombreInformeCapa.replace('capa',''), valores);
            }


        }
    });
}



function cargarInformeObjeto(idInformeCapa, accion)
{
    var token = document.getElementById('token').value;
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        dataType: "json",
        url:   'http://'+location.host+'/consultarInformeObjeto',
        data: {accion: accion, idInformeCapa: idInformeCapa},
        type:  'post',
        beforeSend: function(){
            },
        error:    function(xhr,err){
            alert('se genero error: ' + err);
            },
        success: function(data){
            
            for(var i=0; i < data.length; i++)
            {

                var estilo =    data[i].estiloInformeObjeto +
                                ' background-color:'+data[i].colorFondoEstiloInforme+';'+
                                'border: solid 1px '+data[i].colorBordeEstiloInforme+';'+
                                'color: '+data[i].colorTextoEstiloInforme+';'+
                                'font-family: '+data[i].fuenteTextoEstiloInforme+';'+
                                'font-size: '+data[i].tamañoTextoEstiloInforme+';'+
                                'font-weight: '+(data[i].negrillaEstiloInforme == 1 ? 'bold' : 'normal')+';'+
                                'font-style: '+(data[i].italicaEstiloInforme == 1 ? 'italic' : '')+';'+
                                'text-decoration: '+(data[i].subrayadoEstiloInforme == 1 ? 'underline' : '')+';';


                if(data[i].tipoInformeObjeto == "CampoClon") 
                {
                    // las clases item (le da una clase con nombre unico) y campoTamaño
                    objeto = '<div id="item-'+counts[0]+'" class="item-'+counts[0]+' campoTamaño-'+counts[0]+' '+data[i].tipoInformeObjeto+'" style="'+estilo+'" title="" '+
                                '<input type="hidden" id="Contenido-item-'+counts[0]+'" value="'+data[i].campoInformeObjeto+'" >'+
                                '<input type="hidden" id="Estilo-item-'+counts[0]+'" value="'+data[i].EstiloInforme_idEstiloInforme+'" >'+
                                '<input type="hidden" id="Tipo-item-'+counts[0]+'" value="'+data[i].tipoInformeObjeto+'" >'+
                                '<div  style="width: 100%; height: 100%;" id="Campo-item-'+counts[0]+'">'+data[i].campoInformeObjeto+'</div>'+
                                '<div id="opcionesitem-'+counts[0]+'" style="display:none; float: right;">'+
                                '       <ul class="dropgroup">'+
                                '         <li class="dropdown">'+
                                '           <a href="#"  class="dropbtn"><span  class="fa fa-paint-brush" ></span></a>'+
                                '           <div class="dropdown-content selectorEstilo">'+
                                '           </div>'+
                                '         </li>'+
                                '       </ul>'+
                                '   <span class="fa fa-times-circle" title="Eliminar" onclick="$(\'#item-'+counts[0]+'\').remove();" ></span>'+
                                '</div>'+
                            '</div>';
                    
                    // creamos el objeto en el div contenedor
                    $("#"+data[i].bandaInformeObjeto).append(objeto);

                    // a nuestro objeto le habilitamos los eventos:
                    // Mouseover para que al pasar el mouse se muestre el div de selector de estilos y boton de eliminar
                    // Mouseleave para que se oculten al quitar el mouse
                    $("#item-"+counts[0]).attr("onmouseover", "$(this).find(\'#opcionesitem-"+counts[0]+"\').show();"); 
                    $("#item-"+counts[0]).attr("onmouseleave", "$(this).find(\'#opcionesitem-"+counts[0]+"\').hide();");


                    // en el evento Doble Click habilitamos la funcionalidad propia del objeto de etiquetas que es permitirle al 
                    // usuario digitar el texto y que el sistema lo ponga fijo en el div
                    $("#item-"+counts[0]).dblclick(function() 
                    {
                        
                    });

                    // ejecutamos la funcion que lo convierte en objeto arrastrable y le damos propiedades de cambio de tamaño (resizable)
                    make_draggable($(".item-"+counts[0])); 
                    $("#item-"+counts[0]).resizable(tamañoCampo);
                    counts[0]++;   

                }
                else if(data[i].tipoInformeObjeto == "EtiquetaClon") 
                {
                     // las clases item (le da una clase con nombre unico) y campoTamaño
                    objeto = '<div id="item-'+counts[0]+'" class="item-'+counts[0]+' campoTamaño-'+counts[0]+' '+data[i].tipoInformeObjeto+'" style="'+estilo+'" title="" '+
                                '<input type="hidden" id="Contenido-item-'+counts[0]+'" value="'+data[i].campoInformeObjeto+'" >'+
                                '<input type="hidden" id="Estilo-item-'+counts[0]+'" value="'+data[i].EstiloInforme_idEstiloInforme+'" >'+
                                '<input type="hidden" id="Tipo-item-'+counts[0]+'" value="'+data[i].tipoInformeObjeto+'" >'+
                                '<div  style="width: 100%; height: 100%;" id="Campo-item-'+counts[0]+'">'+data[i].campoInformeObjeto+'</div>'+
                                '<div id="opcionesitem-'+counts[0]+'" style="display:none; float: right;">'+
                                '       <ul class="dropgroup">'+
                                '         <li class="dropdown">'+
                                '           <a href="#"  class="dropbtn"><span  class="fa fa-paint-brush" ></span></a>'+
                                '           <div class="dropdown-content selectorEstilo">'+
                                '           </div>'+
                                '         </li>'+
                                '       </ul>'+
                                '   <span class="fa fa-times-circle" title="Eliminar" onclick="$(\'#item-'+counts[0]+'\').remove();" ></span>'+
                                '</div>'+
                            '</div>';
                    
                    // creamos el objeto en el div contenedor
                    $("#"+data[i].bandaInformeObjeto).append(objeto);

                    // a nuestro objeto le habilitamos los eventos:
                    // Mouseover para que al pasar el mouse se muestre el div de selector de estilos y boton de eliminar
                    // Mouseleave para que se oculten al quitar el mouse
                    $("#item-"+counts[0]).attr("onmouseover", "$(this).find(\'#opcionesitem-"+counts[0]+"\').show();"); 
                    $("#item-"+counts[0]).attr("onmouseleave", "$(this).find(\'#opcionesitem-"+counts[0]+"\').hide();");


                    // en el evento Doble Click habilitamos la funcionalidad propia del objeto de etiquetas que es permitirle al 
                    // usuario digitar el texto y que el sistema lo ponga fijo en el div
                    $("#item-"+counts[0]).dblclick(function() 
                    {
                        
                    });

                    // ejecutamos la funcion que lo convierte en objeto arrastrable y le damos propiedades de cambio de tamaño (resizable)
                    make_draggable($(".item-"+counts[0])); 
                    $("#item-"+counts[0]).resizable(tamañoCampo);
                    counts[0]++;   

                }
            }

        }
    });
}


/****************************************************
**
** ADICIONAR REGISTRO DE CONCEPTO EN LA CAPA ACTUAL
**
****************************************************/
function adicionarConcepto(numeroCapa, valores)
{
    // boton de tipo Movimiento
    if(valores[1] == 1)
        iconoTipoMov = 'src="http://'+location.host+'/imagenes/movimientocontable.png"';
    else
        iconoTipoMov = 'src="http://'+location.host+'/imagenes/saldocontable.png"';


    // boton de tipo Valor
    switch(valores[2])
    {
        case 'Puc':
            iconoTipoValor = 'src="http://'+location.host+'/imagenes/abaco.png"';
            break;
        case 'Formula':
            iconoTipoValor = 'src="http://'+location.host+'/imagenes/funcion.png"';
            break;
        case 'Valor':
            iconoTipoValor = 'src="http://'+location.host+'/imagenes/moneda.png"';
            break;
        case 'Porcentaje':
            iconoTipoValor = 'src="http://'+location.host+'/imagenes/porcentaje.png"';
            break;      
        default:
            iconoTipoValor = 'src="http://'+location.host+'/imagenes/abaco.png"';
            break;


    }
    
    if(valores[5] == 1)
        colorDetalle = 'style="background-color: #A9E2F3;"';
    else
        colorDetalle = 'style="background-color: white;"';

    if(valores[6] == 1)
        colorResumen = 'style="background-color: #A9E2F3;"';
    else
        colorResumen = 'style="background-color: white;"';

    if(valores[7] == 1)
        colorGrafico = 'style="background-color: #A9E2F3;"';
    else
        colorGrafico = 'style="background-color: white;"';
    $("#sortable"+numeroCapa).append(
            '  <li id="concepto_'+numeroCapa+'_'+totalConceptos+'" class="ui-state-default">'+
            '       <img src="http://'+location.host+'/imagenes/ordenar.png" class="botonOrden">'+
            '       <a href="#" onclick="MostrarConcepto(\'modificar\',\''+numeroCapa+'_'+totalConceptos+'\');" title="Modificar"><img src="http://'+location.host+'/imagenes/modificar.png" class="botonAccion" ></a>'+
            '       <a href="#" onclick="MostrarConcepto(\'eliminar\',\''+numeroCapa+'_'+totalConceptos+'\');" title="Eliminar"><img src="http://'+location.host+'/imagenes/eliminar.png" class="botonAccion" ></a>'+
            '       <input type="text" id="nombreConcepto_'+numeroCapa+'_'+totalConceptos+'" name="nombreConcepto[]" value="'+valores[0]+'" title="Descripción del Concepto" class="campoConcepto" style="'+valores[4]+'" readonly="readonly">'+
            '       <a href="#"  title="Origen de Datos"><input type="hidden" id="tipoMovimientoConcepto_'+numeroCapa+'_'+totalConceptos+'" value="'+valores[1]+'" ><img id="iconoTipoMovimientoConcepto_'+numeroCapa+'_'+totalConceptos+'" '+iconoTipoMov+' class="botonOpciones" style="background-color: #A9E2F3;"></a>'+
            '       <a href="#"  title="Contenido del Concepto"><input type="hidden" id="tipoValorConcepto_'+numeroCapa+'_'+totalConceptos+'" value="'+valores[2]+'"><img id="iconoTipoValorConcepto_'+numeroCapa+'_'+totalConceptos+'" '+iconoTipoValor+' class="botonOpciones"  style="background-color: #A9E2F3;"></a>'+
            '       <input type="text" id="valorConcepto_'+numeroCapa+'_'+totalConceptos+'" name="valorConcepto[]" value="'+valores[3]+'" title="Contenido del Concepto" class="campoConcepto"  style="'+valores[4]+'" readonly="readonly">'+
            '       <input type="text" id="excluirTerceroConcepto_'+numeroCapa+'_'+totalConceptos+'" name="excluirTerceroConcepto[]" value="'+valores[10]+'" title="Terceros Excluidos" class="campoConcepto"  style="'+valores[4]+'" readonly="readonly">'+
            '       <input type="hidden" id="detalleConcepto_'+numeroCapa+'_'+totalConceptos+'" value="'+valores[5]+'" ><img id="iconoDetalle_'+numeroCapa+'_'+totalConceptos+'" src="http://'+location.host+'/imagenes/vistadetalle.png" class="botonOpciones" '+colorDetalle+'>'+
            '       <input type="hidden" id="resumenConcepto_'+numeroCapa+'_'+totalConceptos+'" value="'+valores[6]+'" ><img id="iconoResumen_'+numeroCapa+'_'+totalConceptos+'" src="http://'+location.host+'/imagenes/vistaresumen.png" class="botonOpciones" '+colorResumen+'>'+
            '       <input type="hidden" id="graficoConcepto_'+numeroCapa+'_'+totalConceptos+'" value="'+valores[7]+'" ><img id="iconoGrafico_'+numeroCapa+'_'+totalConceptos+'" src="http://'+location.host+'/imagenes/grafico.png" class="botonOpciones" '+colorGrafico+'>'+
            '       <input type="hidden" id="EstiloConcepto_'+numeroCapa+'_'+totalConceptos+'" value="'+valores[8]+'" >'+
            '       <input type="hidden" id="idInformeConcepto_'+numeroCapa+'_'+totalConceptos+'" value="'+valores[9]+'" >'+
            '  </li>');

                        /*<ul class="dropgroup">'+
                        '         <li class="dropdown">'+
                        '           <a href="#"  title="Contenido del Concepto" class="dropbtn"><img src="http://'+location.host+'/imagenes/abaco.png" class="botonOpciones"></a>'+
                        '           <div class="dropdown-content">'+
                        '             <a href="#" title="Plan de cuentas"><img src="http://'+location.host+'/imagenes/abaco.png" class="botonOpciones"></a>'+
                        '             <a href="#" title="Fórmula"><img src="http://'+location.host+'/imagenes/funcion.png" class="botonOpciones"></a>'+
                        '             <a href="#" title="Valor fijo"><img src="http://'+location.host+'/imagenes/moneda.png" class="botonOpciones"></a>'+
                        '             <a href="#" title="Porcentaje"><img src="http://'+location.host+'/imagenes/porcentaje.png" class="botonOpciones"></a>'+
                        '           </div>'+
                        '         </li>'+
                        '       </ul>'+
                        */
    totalConceptos++;

    //********************************************
    // Campos Ordenables
    //********************************************
    $( "#sortable"+numeroCapa ).sortable({
      placeholder: "ui-state-highlight"
    });
    $( "#sortable"+numeroCapa ).disableSelection();
}  


/****************************************************
**
** CARGAR INFORMACION DE CONCEPTO CONTABLE EN MODAL
**
****************************************************/
function MostrarConcepto(accion, numeroConcepto)
{

    $("#accionConcepto").attr('value', accion);
    $("#numeroConcepto").attr('value', numeroConcepto);

    $('#nombreConcepto').prop('readonly', false);
    $('#valorConcepto').prop('readonly', false);
    $('#excluirTerceroConcepto').prop('readonly', false);
    $('#conceptoEstilo option:not(:selected)').attr('disabled',false);
    $("#iconoMovimientoConcepto").prop('disabled', false);

    if(accion == 'insertar')
    {
        $('#nombreConcepto').val('');
        $('#tipoMovimientoConcepto').val(1);
        $('#tipoValorConcepto').val('Puc');
        $('#valorConcepto').val('');
        $('#excluirTerceroConcepto').val('');
        $('#font-family option[value=\'6px\']').prop('selected', true);
        $('#detalleConcepto').val(1);
        $('#resumenConcepto').val(0);
        $('#graficoConcepto').val(0);
    
    }
    else
    {
        $('#nombreConcepto').val($("#nombreConcepto_"+numeroConcepto).val());
        $('#tipoMovimientoConcepto').val($("#tipoMovimientoConcepto_"+numeroConcepto).val());
        $('#tipoValorConcepto').val($("#tipoValorConcepto_"+numeroConcepto).val());
        $('#valorConcepto').val($("#valorConcepto_"+numeroConcepto).val());
        $('#excluirTerceroConcepto').val($("#excluirTerceroConcepto_"+numeroConcepto).val());
        $('#conceptoEstilo option[value=\''+$("#EstiloConcepto_"+numeroConcepto).val()+'\']').prop('selected', true);
        $('#detalleConcepto').val($("#detalleConcepto_"+numeroConcepto).val());
        $('#resumenConcepto').val($("#resumenConcepto_"+numeroConcepto).val());
        $('#graficoConcepto').val($("#graficoConcepto_"+numeroConcepto).val());

        // Si es accion eliminar, inhabilitamos todos los campos
        if(accion == 'eliminar')
        {
            $('#nombreConcepto').prop('readonly', true);
            $('#valorConcepto').prop('readonly', true);
            $('#excluirTerceroConcepto').prop('readonly', true);
            $('#conceptoEstilo option:not(:selected)').attr('disabled',true);
            $("#iconoMovimientoConcepto").prop('disabled', true);
        }
        
    }

    // habilitamos en resaltado el icono de tipo de movimiento
    if($('#tipoMovimientoConcepto').val() == 1)
    {
        $("#iconoMovimientoConcepto").attr('style','background-color: #A9E2F3;');  
        $("#iconoSaldoConcepto").attr('style','background-color: white;');  
    }
    else
    {
        $("#iconoMovimientoConcepto").attr('style','background-color: white;');  
        $("#iconoSaldoConcepto").attr('style','background-color: #A9E2F3;');  
    }

    // habilitamos en resaltado el icono de tipo de valor
    // primero los apagamos todos, luego seleccionamos uno de ellos
    $("#iconoPucConcepto").attr('style','background-color: white;'); 
    $("#iconoFormulaConcepto").attr('style','background-color: white;'); 
    $("#iconoValorConcepto").attr('style','background-color: white;'); 
    $("#iconoPorcentajeConcepto").attr('style','background-color: white;'); 
    
    switch($('#tipoValorConcepto').val())
    {
        case 'Puc':
            $("#iconoPucConcepto").attr('style','background-color: #A9E2F3;'); 
            break;
        case 'Formula':
            $("#iconoFormulaConcepto").attr('style','background-color: #A9E2F3;'); 
            break;
        case 'Valor':
            $("#iconoValorConcepto").attr('style','background-color: #A9E2F3;'); 
            break;
        case 'Porcentaje':
            $("#iconoPorcentajeConcepto").attr('style','background-color: #A9E2F3;'); 
            break;

    }

    // habilitamos en resaltado el icono  de detalle
    if($('#detalleConcepto').val() == 1)
        $("#detalle").attr('style','background-color: #A9E2F3;');  
    else
        $("#detalle").attr('style','background-color: white;');

    // habilitamos en resaltado el icono  de Resumen
    if($('#resumenConcepto').val() == 1)
        $("#resumen").attr('style','background-color: #A9E2F3;');  
    else
        $("#resumen").attr('style','background-color: white;');

    // habilitamos en resaltado el icono  de Grafico
    if($('#graficoConcepto').val() == 1)
        $("#grafico").attr('style','background-color: #A9E2F3;');  
    else
        $("#grafico").attr('style','background-color: white;');

    $("#guardarConcepto").html(accion.charAt(0).toUpperCase() + accion.slice(1));
    $("#guardarConcepto").attr('class', (accion == 'insertar' ? 'btn btn-success' : (accion == 'modificar' ? 'btn btn-warning' : 'btn btn-danger')));


    $('#ModalConcepto').modal('show');
}

/****************************************************
**
** MODIFICAR LA INFORMACION DE CONCEPTO CONTABLE EN LA CAPA
**
****************************************************/
function modificarConcepto()
{
    var numeroCapa = $("#numeroConcepto").val();

    $("#nombreConcepto_"+numeroCapa).val($('#nombreConcepto').val());
    $("#tipoMovimientoConcepto_"+numeroCapa).val($('#tipoMovimientoConcepto').val());
    $("#tipoValorConcepto_"+numeroCapa).val($('#tipoValorConcepto').val());
    $("#valorConcepto_"+numeroCapa).val($('#valorConcepto').val());
    $("#excluirTerceroConcepto_"+numeroCapa).val($('#excluirTerceroConcepto').val());
    $("#EstiloConcepto_"+numeroCapa).val($('#conceptoEstilo option:selected').val());
    $("#detalleConcepto_"+numeroCapa).val($('#detalleConcepto').val());
    $("#resumenConcepto_"+numeroCapa).val($('#resumenConcepto').val());
    $("#graficoConcepto_"+numeroCapa).val($('#graficoConcepto').val());

    // cambiamos el estilo del nombre y el valor del concepto
    $("#nombreConcepto_"+numeroCapa).attr('style', $('#conceptoEstilo option:selected').attr('style'));
    $("#valorConcepto_"+numeroCapa).attr('style', $('#conceptoEstilo option:selected').attr('style'));

    // boton de tipo Movimiento
    if($("#tipoMovimientoConcepto_"+numeroCapa).val())
        $("#iconoTipoMovimientoConcepto_"+numeroCapa).attr('src', 'http://'+location.host+'/imagenes/movimientocontable.png');
    else
        $("#iconoTipoMovimientoConcepto_"+numeroCapa).attr('src', 'http://'+location.host+'/imagenes/saldocontable.png');

    // boton de tipo Movimiento
    switch($("#tipoValorConcepto_"+numeroCapa).val())
    {
        case 'Puc':
            $("#iconoTipoValorConcepto_"+numeroCapa).attr('src', 'http://'+location.host+'/imagenes/abaco.png');
            break;
        case 'Formula':
            $("#iconoTipoValorConcepto_"+numeroCapa).attr('src', 'http://'+location.host+'/imagenes/funcion.png');
            break;
        case 'Valor':
            $("#iconoTipoValorConcepto_"+numeroCapa).attr('src', 'http://'+location.host+'/imagenes/moneda.png');
            break;
        case 'Porcentaje':
            $("#iconoTipoValorConcepto_"+numeroCapa).attr('src', 'http://'+location.host+'/imagenes/porcentaje.png');
            break;
    }
    
    if($("#detalleConcepto_"+numeroCapa).val() == 1)
        $("#iconoDetalle_"+numeroCapa).attr('style', 'background-color: #A9E2F3;');
    else
        $("#iconoDetalle_"+numeroCapa).attr('style', 'background-color: white;');

    if($("#resumenConcepto_"+numeroCapa).val() == 1)
        $("#iconoResumen_"+numeroCapa).attr('style', 'background-color: #A9E2F3;');
    else
        $("#iconoResumen_"+numeroCapa).attr('style', 'background-color: white;');

    if($("#graficoConcepto_"+numeroCapa).val() == 1)
        $("#iconoGrafico_"+numeroCapa).attr('style', 'background-color: #A9E2F3;');
    else
        $("#iconoGrafico_"+numeroCapa).attr('style', 'background-color: white;');

   
}


/****************************************************
**
** ELIMINAR LA INFORMACION DE CONCEPTO CONTABLE EN LA CAPA
**
****************************************************/
function eliminarConcepto()
{
    var numeroCapa = $("#numeroConcepto").val();

    if(confirm("Esta seguro de eliminar el concepto "+$('#nombreConcepto').val()+"?"))
        $("#concepto_"+numeroCapa).remove();

}


/****************************************************
**
** INSERTAR CONCEPTO EN BANDA DE DETALLE CONTABLE
**
****************************************************/
function OcultarConcepto(accion)
{

    var valores = new Array();
    valores[0] = $('#nombreConcepto').val();
    valores[1] = $('#tipoMovimientoConcepto').val();
    valores[2] = $('#tipoValorConcepto').val();
    valores[3] = $('#valorConcepto').val();
    valores[4] = $('#conceptoEstilo option:selected').attr('style');
    valores[5] = $('#detalleConcepto').val();
    valores[6] = $('#resumenConcepto').val();
    valores[7] = $('#graficoConcepto').val();
    valores[8] = $('#conceptoEstilo option:selected').val();
    valores[9] = 0;
    valores[10] = $('#excluirTerceroConcepto').val();
    

    var accion = (accion) ? accion : $("#accionConcepto").val();

    // obtenemos el nombre de la capa activa
        numeroCapa = $("li.active a").attr("href").replace('#capa','');
    switch(accion)
    {
        case  'insertar':
            adicionarConcepto(numeroCapa, valores);
            break;
        case  'modificar':
            modificarConcepto();
            break;
        case  'eliminar':
            eliminarConcepto();
            break;
    }
    
    $("#ModalConcepto").modal('hide');

}





/****************************************************
**
** MOSTRAR MODAL DE FORMULA
**
****************************************************/
function MostrarFormula(accion)
{
    // Limpiamos la ultima formula ingresada
    borrarTodo();
    
    // luego de borrada la formula, debemos tomar la formula actual (si estamos modificando algun concepto)
    // y volverla a formar en el modal de formulas, para esto recorremos el campo de valorConcepto letra a letra
    // y verificamos si es un signo +,-,*,/,(,) o si es fin del texto y asi lo vamos partiendo para enviarlo 
    // a la funciona de concatenarFormula
    var formulaActual = $("#valorConcepto").val();
    var signos = '+-*/()';
    var k = -1;
    var total = formulaActual.length;
    var contenido = '';
    for(var pos = 0; pos < total; pos++)
    {
        k++;
        // si el caracter de la POSicion actual es uno de los signos de la formula, debemos extraer el 
        // contenido hasta ese punto
        if(signos.indexOf($("#valorConcepto").val().substring(pos, pos+1)) >= 0)
        {
            contenido = formulaActual.substring(0, k);
            tipo = (isNaN(parseFloat(contenido)) ? 'Concepto' : 'Constante');
            concatenarDatos(tipo, contenido);

            contenido = formulaActual.substring(k,k+1);
            concatenarDatos('Operador', contenido);
 
            formulaActual = formulaActual.substring(k+1);
            
            k = -1;
        }
    }
    // despues del ultimo signo queda un dato mas que enviar a la formula
    tipo = (isNaN(parseFloat(formulaActual)) ? 'Concepto' : 'Constante');
    concatenarDatos(tipo,  formulaActual);

    $(".tab-pane").each(function(){
        var numeroCapa = $(this).attr('id').replace('capa','');

        // con los mismos datos, cambiamos la lista de seleccion de estilos del modal de conceptos
        var select = document.getElementById('conceptoFormula');
        
        select.options.length = 0;
        var option = '';

        option = document.createElement('option');
        option.value = '';
        option.text = 'Seleccione Concepto...';
        select.appendChild(option);

        $("#sortable"+numeroCapa+" li").each(function(){
            var numeroConcepto = $(this).attr('id').replace('concepto','');

            option = document.createElement('option');
            option.value = $("#nombreConcepto"+numeroConcepto).val();
            option.text = $("#nombreConcepto"+numeroConcepto).val();
            
            // option.selected = (document.getElementById("Ausentismo_idAusentismo").value == data[j].idEstiloInforme ? true : false);
            select.appendChild(option);

        });
        
    });

    $('#ModalFormula').modal('show');
}

/****************************************************
**
** INSERTAR FORMULA EN MODAL DE CONCEPTO
**
****************************************************/
function OcultarFormula(accion)
{
    var accion = (accion) ? accion : $("#accionFormula").val();


    // obtenemos el nombre de la capa activa
    numeroCapa = $("li.active a").attr("href").replace('#capa','');
    if(accion != 'cancelar')
        $("#valorConcepto").val($("#formulaconcatenada").val());
    
    $("#ModalFormula").modal('hide');

}



function concatenarDatos(div, dato)
{
    if(dato == '')
        return;

    componente =  parseFloat(document.getElementById("contadorFormula").value)+1;
    
    // limpiamos el campo de valor constante
    document.getElementById('valorConstante').value = '0';
  
    // incrementamos el contador de componentes de la formula antes de crearlo
    document.getElementById("contadorFormula").value++;

    

    switch(div)
    {
        case  'Constante':
        clase = 'class="btn btn-warning"';
        estilo = 'style="width: 300px; text-align: left;"';
        break;

        case 'Operador':
        clase = 'class="btn btn-success"';
        estilo = '';
        break;

        case 'Concepto':
        clase = 'class="btn btn-primary"';
        estilo = 'style="width: 300px; text-align: left;"';
        break;
    }

    if( div != 'Constante' || (div == 'Constante' && dato > 0))
    {
        // adicionamos a la formula el nombre del indicador/constante/variable
        document.getElementById('formulaconcatenada').value += dato;

        document.getElementById("contenedorFormula").innerHTML += '<div id="'+document.getElementById("contadorFormula").value+
                                                            '" '+clase+' '+estilo+'>'+dato+'</div>'+((div == 'Operador' && dato != '(' && dato != ')') ? '<br>' : '');
    }

    // luego de insertado el componente de la formula, ponemos la lista de seleccion en el titulo  inicial (Seleccione Concepto...)
    $('#conceptoFormula option[value=\'\']').prop('selected', true);


}


function borrarTodo()
{
    // limpiamos la formula concatenada (string)
    document.getElementById('formulaconcatenada').value = '';
    
    // limpiamos la formula en forma de botones
    document.getElementById("contenedorFormula").innerHTML = '';
}


function borrarUltimo(id)
{
    // tomamos el nombre del componente antes de eliminarlo
    valor = document.getElementById(id).innerHTML;

    // eliminamos el ultimo componente (div) de la formula en forma de botones
    document.getElementById("contenedorFormula").removeChild(document.getElementById(id)); 
   
    
    // limpiamos de la formula concatenada el componente eliminado (string)
    // verificamos la longitud del componente para quitar esta parte del final de la formula

    formula = document.getElementById('formulaconcatenada').value;
    document.getElementById('formulaconcatenada').value = formula.substring(0, (formula.length - valor.length));

    // restamos 1 al contador de componentes de la formula
    document.getElementById('contadorFormula').value--;

}


/****************************************************
**
** ALMACENAMIENTO: GUARDAR INFORME
**
****************************************************/
function guardarInforme(accion)
{

    // capturamos los datos del informe en un array
    var datoInforme = new Array();
    datoInforme[0] = $('#idInforme').val();
    datoInforme[1] = $('#nombreInforme').val();
    datoInforme[2] = $('#descripcionInforme').val();
    
    // capturamos los datos de la configuracion Regional en un array
    var datoPropiedad = new Array();
    datoPropiedad[0] = $('#idInformePropiedad').val(); 
    datoPropiedad[1] = $('#colorFondoParInformePropiedad').val(); 
    datoPropiedad[2] = $('#colorFondoImparInformePropiedad').val();
    datoPropiedad[3] = $('#colorBordeParInformePropiedad').val();
    datoPropiedad[4] = $('#colorBordeImparInformePropiedad').val(); 
    datoPropiedad[5] = $('#colorTextoParInformePropiedad').val(); 
    datoPropiedad[6] = $('#colorTextoImparInformePropiedad').val(); 
    datoPropiedad[7] = $('#fuenteTextoParInformePropiedad').val();
    datoPropiedad[8] = $('#fuenteTextoImparInformePropiedad').val();
    datoPropiedad[9] = $('#tamañoTextoParInformePropiedad').val();
    datoPropiedad[10] = $('#tamañoTextoImparInformePropiedad').val();
    datoPropiedad[11] = $('#negrillaParInformePropiedad').val();
    datoPropiedad[12] = $('#negrillaImparInformePropiedad').val();
    datoPropiedad[13] = $('#italicaParInformePropiedad').val();
    datoPropiedad[14] = $('#italicaImparInformePropiedad').val();
    datoPropiedad[15] = $('#subrayadoParInformePropiedad').val();
    datoPropiedad[16] = $('#subrayadoImparInformePropiedad').val();

    
    var datoObjeto = '';
   

    $(".tab-pane").each(function()
    {
        
        var numeroCapa = $(this).attr('id').replace('capa','');
        var idCapa = $("#idInformeCapa"+numeroCapa).val();
        
        datoObjeto += 'capa|'+
                        idCapa+'|'+
                        $(this).attr('id')+'|'+
                        $("#idSistemaInformacion"+numeroCapa).val()+'|'+
                        $("#nombreTabla"+numeroCapa).val()+'|'+
                        $("#tipoInformeCapa"+numeroCapa).val()+'¬';

        // dentro de cada capa, consultamos las bandas de encabezado, detalle (tienen clase panelLayout) y pie para obtener sus objetos
        $(this).find( "div[class*='Banda']" ).each(function()
        {

            $( this ).contents().find( "[id^='concepto']" ).each(function()
            {
               var numeroConcepto = $(this).attr('id').replace('concepto','');
               //var numeroConcepto = $(this).attr('id').replace('concepto','');
               datoObjeto +=    'concepto|'+
                                $(this).parent().attr('id')+'|'+
                                $("#nombreConcepto"+numeroConcepto).val() + '|' +
                                $("#tipoMovimientoConcepto"+numeroConcepto).val() + '|' +
                                $("#tipoValorConcepto"+numeroConcepto).val() + '|' +
                                $("#valorConcepto"+numeroConcepto).val() + '|' +
                                ($("#EstiloConcepto"+numeroConcepto).val() == 0 ? null : $("#EstiloConcepto"+numeroConcepto).val()) + '|' +
                                $("#detalleConcepto"+numeroConcepto).val() + '|' +
                                $("#resumenConcepto"+numeroConcepto).val() + '|' +
                                $("#graficoConcepto"+numeroConcepto).val() + '|' +
                                $("#excluirTerceroConcepto"+numeroConcepto).val() + '|' +
                                $("#idInformeConcepto"+numeroConcepto).val() + '|' +
                                idCapa+'¬';
            });
            

            // dentro de cada Banda,consultamos los objetos (itenen clase que contiene la palabra item-)
            $( this ).find( "[id^='item-']" ).each(function()
            {
                datoObjeto +=   'objeto|'+
                                '0' + '|' +
                                $(this).parent().attr('id')+'|'+
                                $(this).prop('id') + '|' +
                                $(this).attr('style') + '|' +
                                $("#Estilo-"+$(this).prop('id')).val() + '|' +
                                $("#Tipo-"+$(this).prop('id')).val() + '|' +
                                $("#Contenido-"+$(this).prop('id')).val() + '|' +
                                idCapa+'¬';
                
            });
        });                
        console.log(datoObjeto);

    });

     
    var accion = (accion) ? accion : $("#accionInforme").val();
    id = 0;
    if(accion != 'cancelar')
    {
        var token = document.getElementById('token').value;
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            url:   'http://'+location.host+'/guardarInforme',
            data: {
                accion: accion, 
                idInforme: $("#idInforme").val(), 
                informe: datoInforme,
                propiedad: datoPropiedad,
                objetos: datoObjeto
                },
            type:  'post',
            beforeSend: function(){
                },
            success: function(data){
                alert('Datos Guardados');
            },
            error:    function(xhr,err){
                alert('Se genero un error: ' +err);
            }
        });
    }
    
}


function consultarTablaVista(idSistema, tablaDocumento)
{
    var token = document.getElementById('token').value;
    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            data: {idSistema: idSistema},
            url:   'http://'+location.host+'/conexionDocumento/',
            type:  'post',
            beforeSend: function(){
                },
            success: function(respuesta){
                var tablas = respuesta[0];
                var nombreDB = respuesta[1];
                
                var select = document.getElementById('tablaDocumento');
                   
                select.options.length = 0;
                var option = '';

                option = document.createElement('option');
                option.value = null;
                option.text = 'Seleccione la tabla';
                select.appendChild(option);


                for (var i = 0; i < tablas.length ; i++)
                {
                    option = document.createElement('option');
                    option.value = tablas[i]["Tables_in_"+nombreDB];
                    option.text = tablas[i]["Tables_in_"+nombreDB];
                    option.selected = (tablaDocumento ==  tablas[i]["Tables_in_"+nombreDB] ? true : false);
                    select.appendChild(option);                    
                }

                // Cada que cambiemos de sistema de informacion o de tabla, llenamos los campos de idSistemaInformacion y nombreTabla con la
                // informacion del panel de conexion a la base de datos 
                //Obtenemos la capa que esta activa
                numeroCapa = $("ul#tabcapa li.active").attr('id').replace('hojacapa','');
                $("#idSistemaInformacion"+numeroCapa).val($("#sistemaInformacion").val());


            },
            error:    function(xhr,err){ 
                alert("No se ha podido conectar a la base de datos");
            }
        });
}


function consultarCampos(idSistema, nombreTabla)
{
    var token = document.getElementById('token').value;
    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            data: {idSistema: idSistema, nombreTabla: nombreTabla},
            url:   'http://'+location.host+'/conexionDocumentoCampos/',
            type:  'post',
            beforeSend: function(){
                },
            success: function(respuesta){
               
                $("#camposTabla").html('');
                for (var i = 0; i < respuesta.length ; i++)
                {
                    
                    $("#camposTabla").append('<div class="CampoBase campoClonable" '+(respuesta[i]["Comentario"] == '' ? '' : ' title="'+respuesta[i]["Comentario"]+'" ')+'><span class="fa fa-database">&nbsp;</span><input type="hidden" value="'+respuesta[i]["Campo"]+'">'+(respuesta[i]["Comentario"] == '' ? respuesta[i]["Campo"] : respuesta[i]["Comentario"])+'</div>');
                            // // para la clase imagenClonable, ponemos propiedades de arrastrable
            
                }
                // para la clase campoClonable, ponemos propiedades de arrastrable
                $(".campoClonable").draggable({
                    helper: "clone",
                    cursor: "move",
                    //grid: [ 10, 10 ],
                    //Create counter
                    start: function() { counts[0]++; }
                });

        

                // Cada que cambiemos de sistema de informacion o de tabla, llenamos los campos de idSistemaInformacion y nombreTabla con la
                // informacion del panel de conexion a la base de datos 
                // Obtenemos la capa que esta activa
                numeroCapa = $("ul#tabcapa li.active").attr('id').replace('hojacapa','');
                $("#nombreTabla"+numeroCapa).val($("#tablaDocumento").val());    
                
 
            },
            error:    function(xhr,err){ 
                alert("No se pudieron consultar los campos de la tabla/vista");
            }
        });
}



$(function()
{
    // llevamos un contador para la adicion de CAPAS
    var totalCapas = 0;
    
    //Make every clone image unique.  
    counts = [0];
    tamañoCampo = { 
        handles: "all" ,
        //containment: ".contenedorCampos",
        minHeight: 25,
        minWidth: 100
    };

    tamañoImagen = { 
        handles: "all" ,
        minHeight: 48,
        minWidth: 48
    };    



    // para la clase imagenClonable, ponemos propiedades de arrastrable
    $(".imagenClonable").draggable({
        helper: "clone",
        cursor: "move",
        // grid: [ 10, 10 ],
        //Create counter
        start: function() { counts[0]++; }
    });

    // para la clase imagenClonable, ponemos propiedades de arrastrable
    $(".editorClonable").draggable({
        helper: "clone",
        cursor: "move",
        // grid: [ 10, 10 ],
        //Create counter
        start: function() { counts[0]++; }
    });

    // para la clase imagenClonable, ponemos propiedades de arrastrable
    $(".etiquetaClonable").draggable({
        helper: "clone",
        cursor: "move",
        //grid: [ 10, 10 ],
        //Create counter
        start: function() { counts[0]++; }
    });
    // $(".panel-primary").resizable({
    //  handles: "s",
    //  minHeight: 82,
    //  resize: function( event, ui ) 
    //      {
    //          $("#"+this.id+"Contenedor").height($("#"+this.id).height() - 55) ;
    //      }
    //  });



    //********************************************
    // Barras de division de paneles movibles
    //********************************************
    var i = 0;
    var dragging = false;
    $('#barraDrag').mousedown(function(e){
        e.preventDefault();

        dragging = true;
        var panelDerecho = $('#panelDerecho');
        var barraGhost = $('<div>',
                        {id:'barraGhost',
                         css: {
                                height: panelDerecho.outerHeight(),
                                top: panelDerecho.offset().top,
                                left: panelDerecho.offset().left
                               }
                        }).appendTo('body');

        $(document).mousemove(function(e){
          barraGhost.css("left",e.pageX+2);
        });
    });


    $(document).mouseup(function(e){
       if (dragging) 
       {
           var percentage = (e.pageX / window.innerWidth) * 100;
           var mainPercentage = 100-percentage;
           
           
           $('#panelIzquierdo').css("width",percentage + "%");
           $('#panelDerecho').css("width",mainPercentage + "%");
           $('#barraGhost').remove();
           $(document).unbind('mousemove');
           dragging = false;
       }
    });
});

