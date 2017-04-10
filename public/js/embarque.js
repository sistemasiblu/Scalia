//Obtener ip del servidor
var ip = ((location.href.split('/'))[0])+'//'+((location.href.split('/'))[2]);

$(document).ready( function () {

	$("#fechaElaboracionEmbarque, #fechaRealEmbarque, #fechaLlegadaZFEmbarque, #fechaReservaE, #fechaRealE, #fechaMaximaE, #arriboPuertoE, #arriboPuertoEmbarque").datetimepicker
	(
		({
           format: "YYYY-MM-DD"
         })
	);
});

function validarFormulario(event)
{
    var route = "http://"+location.host+"/embarque";
    var token = $("#token").val();
    var dato0 = document.getElementById('idEmbarque').value;
    var dato1 = document.getElementById('numeroEmbarque').value;
    var dato2 = document.getElementById('tipoTransporteEmbarque').value;
    var dato3 = document.getElementById('puertoCargaEmbarque').value;
    var dato4 = document.getElementById('puertoDescargaEmbarque').value;
    var dato5 = document.getElementById('fechaRealEmbarque').value;
    var datoFechaRDetalle = document.querySelectorAll("[name='fechaRealEmbarqueDetalle[]']");
    var dato6 = [];

    var valor = '';
    var sw = true;

    for(var j=0,i=datoFechaRDetalle.length; j<i;j++)
    {
        dato6[j] = datoFechaRDetalle[j].value;
    }

    $.ajax({
        async: false,
        url:route,
        headers: {'X-CSRF-TOKEN': token},
        type: 'POST',
        dataType: 'json',
        data: {respuesta: 'falso',
                idEmbarque: dato0,
                numeroEmbarque: dato1,
                tipoTransporteEmbarque: dato2,
                puertoCargaEmbarque: dato3,
                puertoDescargaEmbarque: dato4,
                fechaRealEmbarque: dato5,
                fechaRealEmbarqueDetalle: dato6

                },
        success:function(){
            //$("#msj-success").fadeIn();
            //console.log(' sin errores');
        },
        error:function(msj){
            var mensaje = '';
            var respuesta = JSON.stringify(msj.responseJSON); 
            if(typeof respuesta === "undefined")
            {
                sw = false;
                $("#msj").html('');
                $("#msj-error").fadeOut();
            }
            else
            {
                sw = true;
                respuesta = JSON.parse(respuesta);

                (typeof msj.responseJSON.numeroEmbarque === "undefined" ? document.getElementById('numeroEmbarque').style.borderColor = '' : document.getElementById('numeroEmbarque').style.borderColor = '#a94442');

                (typeof msj.responseJSON.tipoTransporteEmbarque === "undefined" ? document.getElementById('tipoTransporteEmbarque').style.borderColor = '' : document.getElementById('tipoTransporteEmbarque').style.borderColor = '#a94442');

                (typeof msj.responseJSON.puertoCargaEmbarque === "undefined" ? document.getElementById('puertoCargaEmbarque').style.borderColor = '' : document.getElementById('puertoCargaEmbarque').style.borderColor = '#a94442');

                (typeof msj.responseJSON.puertoDescargaEmbarque === "undefined" ? document.getElementById('puertoDescargaEmbarque').style.borderColor = '' : document.getElementById('puertoDescargaEmbarque').style.borderColor = '#a94442');

                (typeof msj.responseJSON.fechaRealEmbarque === "undefined" ? document.getElementById('fechaRealEmbarque').style.borderColor = '' : document.getElementById('fechaRealEmbarque').style.borderColor = '#a94442');

                for(var j=0,i=datoFechaRDetalle.length; j<i;j++)
                {
                    (typeof respuesta['fechaRealEmbarqueDetalle'+j] === "undefined" ? document.getElementById('fechaRealEmbarqueDetalle'+j).style.borderColor = '' : document.getElementById('fechaRealEmbarqueDetalle'+j).style.borderColor = '#a94442');

                }

                var mensaje = 'Por favor verifique los siguientes valores <br><ul>';
                $.each(respuesta,function(index, value){
                    mensaje +='<li>' +value+'</li><br>';
                });
                mensaje +='</ul>';
               
                $("#msj").html(mensaje);
                $("#msj-error").fadeIn();
            }

        }
    });

    if(sw === true)
        event.preventDefault();
}

function abrirModal(nombreTabla,nombreCampo,codigoCampo,objeto, tipotercero)
{
    var lastIdx = null;
    window.parent.$("#tlistaselectemb").DataTable().ajax.url(ip+"/datosListaSelectImportacion?nombreTabla="+nombreTabla+"&campo="+nombreCampo+"&codigo="+codigoCampo+"&value="+objeto.value+"&tipotercero="+tipotercero+"&campoTabla="+objeto.id).load();
     // Abrir modal
    window.parent.$("#ListaSelect").modal()

    $("a.toggle-vis").on( "click", function (e) {
        e.preventDefault();
 
        // Get the column API object
        var column = table.column( $(this).attr("data-column") );
 
        // Toggle the visibility
        column.visible( ! column.visible() );
    } );

    window.parent.$("#tlistaselectemb tbody").on( "mouseover", "td", function () 
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
    window.parent.$("#tlistaselectemb tfoot th").each( function () 
    {
        var title = window.parent.$("#tlistaselectemb thead th").eq( $(this).index() ).text();
        $(this).html( "<input type='text' placeholder='Buscar por "+title+"'/>" );
    });
 
    // DataTable
    var table = window.parent.$("#tlistaselectemb").DataTable();
 
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

    window.parent.$("#tlistaselectemb tbody").on( "dblclick", "tr", function () 
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
                enviarDatosLista(datos[0][0], datos[0][1], datos[0][2], datos[0][3]);
            }

        window.parent.$("#ListaSelect").modal("hide");

    } );

}

function enviarDatosLista(id,nombre,cod,objeto)
{   
    if (objeto == 'tipoTransporteEmbarque') 
    {
        $("input[name='tipoTransporteEmbarque']").each(function() 
        {
            $(this).val(nombre);
        });

        $("input[id='TipoTransporte_idTipoTransporte']").each(function() 
        {
            $(this).val(id);
        });
    }  
    else if(objeto == 'puertoCargaEmbarque')
    {
        $("input[name='puertoCargaEmbarque']").each(function() 
        {
            $(this).val(nombre);
        });

        $("input[id='Ciudad_idPuerto_Carga']").each(function() 
        {
            $(this).val(id);
        });
    }
    else if (objeto == 'puertoDescargaEmbarque') 
    {
        $("input[name='puertoDescargaEmbarque']").each(function() 
        {
            $(this).val(nombre);
        });

        $("input[id='Ciudad_idPuerto_Descarga']").each(function() 
        {
            $(this).val(id);
        });
    }     
    else if (objeto == 'agenteCargaEmbarque') 
    {
        $("input[name='agenteCargaEmbarque']").each(function() 
        {
            $(this).val(nombre);
        });

        $("input[id='Tercero_idAgenteCarga']").each(function() 
        {
            $(this).val(id);
        });
    }     
    else if (objeto == 'navieraEmbarque') 
    {
        $("input[name='navieraEmbarque']").each(function() 
        {
            $(this).val(nombre);
        });

        $("input[id='Tercero_idNaviera']").each(function() 
        {
            $(this).val(id);
        });
    }
}

function abrirModalCompra()
{
	$('#myModalCompra').modal('show');

}

function calcularTotales()
{
    volumen = 0;
    valor = 0;
    unidades = 0;
    peso = 0;
    bulto = 0;

   
     for (var i = 0; i < window.parent.embarques.contador; i++) 
    {
        if(typeof $("#unidadFacturaEmbarqueDetalle"+i, window.parent.document).val() != 'undefined' &&
            $("#unidadFacturaEmbarqueDetalle"+i, window.parent.document).val() > 0)
        {
            volumen += parseFloat($("#volumenFacturaEmbarqueDetalle"+i, window.parent.document).val());
            valor += parseFloat($("#valorFacturaEmbarqueDetalle"+i, window.parent.document).val());
            unidades += parseFloat($("#unidadFacturaEmbarqueDetalle"+i, window.parent.document).val());
            peso += parseFloat($("#pesoFacturaEmbarqueDetalle"+i, window.parent.document).val());
            bulto += parseFloat($("#bultoFacturaEmbarqueDetalle"+i, window.parent.document).val());
        }    
        else
        {
            volumen += parseFloat($("#volumenEmbarqueDetalle"+i, window.parent.document).val());
            valor += parseFloat($("#valorEmbarqueDetalle"+i, window.parent.document).val());
            unidades += parseFloat($("#unidadEmbarqueDetalle"+i, window.parent.document).val());
            peso += parseFloat($("#pesoEmbarqueDetalle"+i, window.parent.document).val());
            bulto += parseFloat($("#bultoEmbarqueDetalle"+i, window.parent.document).val());
        }
    }
        
            
    $('#volumenTotalEmbarque', window.parent.document).val(volumen);
    $('#valorTotalEmbarque', window.parent.document).val(valor);
    $('#unidadTotalEmbarque', window.parent.document).val(unidades);
    $('#pesoTotalEmbarque', window.parent.document).val(peso);
    $('#bultoTotalEmbarque', window.parent.document).val(bulto);
}

function llenarDatosCompra(Compra)
{
    var token = document.getElementById('token').value;
    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            data: {'idCompra': Compra.value},
            url:   ip+'/consultarCamposCompraEmbarque/',
            type:  'post',
            beforeSend: function(){
                },
            success: function(respuesta)
            {
                reg = Compra.id.replace('Compra_idCompra','');

                $('#nombreTemporadaEmbarqueDetalle'+reg).val(respuesta['nombreTemporadaCompra']);
                $('#proveedorTemporadaEmbarqueDetalle'+reg).val(respuesta['nombreProveedorCompra']);
                $('#numeroCompraEmbarqueDetalle'+reg).val(respuesta['numeroCompra']);
                $('#eventoEmbarqueDetalle'+reg).val(respuesta['eventoCompra']);
                $('#fechaDeliveryEmbarqueDetalle'+reg).val(respuesta['fechaDeliveryCompra']);
            },
            error: function(xhr,err)
            { 
                alert("Error");
            }
        });
}

function validarNumeroEmbarque(numeroEmbarque,sufijo)
{
    sufijo = sufijo.toUpperCase();
    $("#sufijoEmbarque").val(sufijo);
    numeroEmbarque = numeroEmbarque+sufijo;
    
    var token = document.getElementById('token').value;
    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            data: {'numeroEmbarque': numeroEmbarque},
            url:   ip+'/validarNumeroEmbarque/',
            type:  'post',
            beforeSend: function(){
                },
            success: function(respuesta)
            {
                if (respuesta == 'El numero de embarque ya existe') 
                {
                    $('#numeroEmbarque').css('background-color', '#F5A9A9');
                    // $('#numeroEmbarque').prop('title', 'El numero de embarque ya existe');
                    // $('#numeroEmbarque').addClass('data-toggle="tooltip"');
                    // $('[data-toggle="tooltip"]').tooltip(); 
                }
                else
                {
                    $('#numeroEmbarque').css('background-color', '#A9F5A9');
                }
            },
            error: function(xhr,err)
            { 
                alert("Error");
            }
        });
}

function mostrarDatosCompra(compra,tipo)
{
    documento = $("#DocumentoImportacion_idDocumentoImportacion").val();
 
    var reg = compra.id
    reg = reg.replace("numeroCompraEmbarqueDetalle",'')

    id = ($('#Compra_idCompra'+reg).val());

    compra = $("#"+compra.id).val();

    window.open(ip+'/embarque/'+id+'?numero='+compra+'&documento='+documento+"&tipo="+tipo,'_blank','width=2500px, height=700px, scrollbars=yes');
}

function validarValorUnidad(unidad)
{
    var reg = unidad.id
    reg = reg.replace("unidadEmbarqueDetalle",'')

    validador = ($('#unidadEmbarque'+reg).val());
        
    if (validador < unidad.value) 
    {
        alert('Está excediendo el valor máximo de unidades de la compra. Verifique porque es mayor a '+validador+'.');
        $('#'+unidad.id).css('background-color', '#F5A9A9');
    }
    else
    {
        $('#'+unidad.id).css('background-color', '');   
    }
}

function abrirModalEmbarque(reg)
{
    // Envío a todos los campos del modal el valor que tienen en la multiregistro
    // dependiendo del numero de registro que sea, para esto el replace obteniendo el numero de reg
    var reg = reg.id;
    reg = reg.replace("modalEmbarque",'');
    
    $('#myModalEmbarque').modal('show');

    $('#compraEmbarque').val($('#nombreTemporadaEmbarqueDetalle'+reg).val())
    $('#proveedorEmbarque').val($('#proveedorTemporadaEmbarqueDetalle'+reg).val())  
    $('#numeroCompraEmbarque').val($('#numeroCompraEmbarqueDetalle'+reg).val())
    $('#deliveryEmbarque').val($('#fechaDeliveryEmbarqueDetalle'+reg).val())  
    $('#proformaEmbarque').val($('#proformaEmbarqueDetalle'+reg).val())   
    $('#volumenEmbarque').val($('#volumenEmbarqueDetalle'+reg).val())
    $('#valorEmbarque').val($('#valorEmbarqueDetalle'+reg).val())  
    $('#unidadEmbarque').val($('#unidadEmbarqueDetalle'+reg).val())
    $('#pesoEmbarque').val($('#pesoEmbarqueDetalle'+reg).val())
    $('#bultoEmbarque').val($('#bultoEmbarqueDetalle'+reg).val())
    $('#facturaEmbarque').val($('#facturaEmbarqueDetalle'+reg).val()) 
    $('#volumenFactura').val($('#volumenFacturaEmbarqueDetalle'+reg).val())
    $('#valorFactura').val($('#valorFacturaEmbarqueDetalle'+reg).val())  
    $('#unidadFactura').val($('#unidadFacturaEmbarqueDetalle'+reg).val())
    $('#pesoFactura').val($('#pesoFacturaEmbarqueDetalle'+reg).val())
    $('#bultoFactura').val($('#bultoFacturaEmbarqueDetalle'+reg).val())
    $('#fechaReservaE').val($('#fechaReservaEmbarqueDetalle'+reg).val())
    $('#fechaRealE').val($('#fechaRealEmbarqueDetalle'+reg).val())
    $('#fechaMaximaE').val($('#fechaMaximaEmbarqueDetalle'+reg).val())
    $('#fechaLlegadaZFEmbarque').val($('#fechaLlegadaZonaFrancaEmbarqueDetalle'+reg).val())
    $('#compradorEmbarque').val($('#compradorEmbarqueDetalle'+reg).val())
    $('#eventoEmbarque').val($('#eventoEmbarqueDetalle'+reg).val())
    $('#dolarEmbarque').val($('#dolarEmbarqueDetalle'+reg).val())
    $('#arriboPuertoEmbarque').val($('#fechaArriboPuertoEstimadaEmbarqueDetalle'+reg).val())
    $('#arriboPuertoE').val($('#fechaArriboPuertoEmbarqueDetalle'+reg).val())

    if ($('#soportePagoEmbarqueDetalle'+reg).val() == "1")
        $('#soportePagoEmbarque').prop("checked", true); 

    $('#compradorVendedorEmbarque').val($('#compradorVendedorEmbarqueDetalle'+reg).val())
    $('#cantidadContenedor').val($('#cantidadContenedorEmbarqueDetalle'+reg).val())
    $('#tipoContenedor option[value='+$('#tipoContenedorEmbarqueDetalle'+reg).val()+']').prop('selected', true);
    $('#contenedorEmbarque').val($('#numeroContenedorEmbarqueDetalle'+reg).val())
    $('#blEmbarque').val($('#blEmbarqueDetalle'+reg).val())
    $('#courrierEmbarque').val($('#numeroCourrierEmbarqueDetalle'+reg).val())

    if ($('#pagoEmbarqueDetalle'+reg).val() == "1")
        $('#pagoEmbarque').prop("checked", true); 

    if ($('#originalEmbarqueDetalle'+reg).val() == "1")
        $('#originalEmbarque').prop("checked", true); 

    $('#descripcionEmbarque').val($('#descripcionEmbarqueDetalle'+reg).val())
    $('#fileEmbarque').val($('#fileEmbarqueDetalle'+reg).val())
    $('#observacionEmbarque').val($('#observacionEmbarqueDetalle'+reg).val())
    $('#numeroRegistroEmbarque').val(reg)
}

function llenarRegistrosModal(reg)
{
    // Al presionar el boton OK en el modal envío a la multiregistro el valor que contiene el campo en el modal
    $('#proformaEmbarqueDetalle'+reg, window.parent.document).val($('#proformaEmbarque').val());
    $('#volumenEmbarqueDetalle'+reg, window.parent.document).val($('#volumenEmbarque').val());
    $('#valorEmbarqueDetalle'+reg, window.parent.document).val($('#valorEmbarque').val()); 
    $('#unidadEmbarqueDetalle'+reg, window.parent.document).val($('#unidadEmbarque').val());
    $('#pesoEmbarqueDetalle'+reg, window.parent.document).val($('#pesoEmbarque').val());
    $('#bultoEmbarqueDetalle'+reg, window.parent.document).val($('#bultoEmbarque').val());
    $('#facturaEmbarqueDetalle'+reg, window.parent.document).val($('#facturaEmbarque').val()); 
    $('#volumenFacturaEmbarqueDetalle'+reg, window.parent.document).val($('#volumenFactura').val()); 
    $('#valorFacturaEmbarqueDetalle'+reg, window.parent.document).val($('#valorFactura').val());   
    $('#unidadFacturaEmbarqueDetalle'+reg, window.parent.document).val($('#unidadFactura').val());
    $('#pesoFacturaEmbarqueDetalle'+reg, window.parent.document).val($('#pesoFactura').val()); 
    $('#bultoFacturaEmbarqueDetalle'+reg, window.parent.document).val($('#bultoFactura').val());
    $('#fechaReservaEmbarqueDetalle'+reg, window.parent.document).val($('#fechaReservaE').val());   
    $('#fechaRealEmbarqueDetalle'+reg, window.parent.document).val($('#fechaRealE').val());   
    $('#fechaMaximaEmbarqueDetalle'+reg, window.parent.document).val($('#fechaMaximaE').val());   
    $('#fechaLlegadaZonaFrancaEmbarqueDetalle'+reg, window.parent.document).val($('#fechaLlegadaZFEmbarque').val());   
    $('#compradorEmbarqueDetalle'+reg, window.parent.document).val($('#compradorEmbarque').val());
    $('#eventoEmbarqueDetalle'+reg, window.parent.document).val($('#eventoEmbarque').val());
    $('#dolarEmbarqueDetalle'+reg, window.parent.document).val($('#dolarEmbarque').val());
    $('#fechaArriboPuertoEstimadaEmbarqueDetalle'+reg, window.parent.document).val($('#arriboPuertoEmbarque').val());
    $('#fechaArriboPuertoEmbarqueDetalle'+reg, window.parent.document).val($('#arriboPuertoE').val());

    if ($('#soportePagoEmbarque').prop("checked") === true)
    {
        $('#soportePagoEmbarqueDetalle'+reg).val(1); 
        $('#soportePagoEmbarqueDetalleC'+reg).prop("checked", true); 
    }
    else
    {
        $('#soportePagoEmbarqueDetalle'+reg).val(0); 
        $('#soportePagoEmbarqueDetalleC'+reg).prop("checked", false);    
    }

    $('#cantidadContenedorEmbarqueDetalle'+reg, window.parent.document).val($('#cantidadContenedor').val());
    $('#tipoContenedorEmbarqueDetalle'+reg+' option[value='+$('#tipoContenedor').val()+']').prop('selected', true);
    $('#numeroContenedorEmbarqueDetalle'+reg, window.parent.document).val($('#contenedorEmbarque').val());
    $('#blEmbarqueDetalle'+reg, window.parent.document).val($('#blEmbarque').val());
    $('#numeroCourrierEmbarqueDetalle'+reg, window.parent.document).val($('#courrierEmbarque').val());


    if ($('#pagoEmbarque').prop("checked") == true)
    {
        $('#pagoEmbarqueDetalle'+reg).val(1); 
        $('#pagoEmbarqueDetalleC'+reg).prop("checked", true); 
    }
    else
    {
        $('#pagoEmbarqueDetalle'+reg).val(0); 
        $('#pagoEmbarqueDetalleC'+reg).prop("checked", false);   
    }

    if ($('#originalEmbarque').prop("checked") == true)
    {
        $('#originalEmbarqueDetalle'+reg).val(1); 
        $('#originalEmbarqueDetalleC'+reg).prop("checked", true);     
    }
    else
    {
        $('#originalEmbarqueDetalle'+reg).val(0); 
        $('#originalEmbarqueDetalleC'+reg).prop("checked", false); 
    }

    $('#descripcionEmbarqueDetalle'+reg, window.parent.document).val($('#descripcionEmbarque').val());
    $('#fileEmbarqueDetalle'+reg, window.parent.document).val($('#fileEmbarque').val());
    $('#observacionEmbarqueDetalle'+reg, window.parent.document).val($('#observacionEmbarque').val());

    window.parent.$("#myModalEmbarque").modal("hide");
}

function reenviarCorreoEmbarque(reg)
{
    var reg = $(reg).parent().attr('id');
    reg = reg.replace("embarquedetalles",'');

    $('#bodegaCorreoEmbarque').val(0);
    $('#otmCorreoEmbarque').val(0);
    $('#pagoCorreoEmbarqueDetalle'+reg).val(0);
}

function duplicarCompras()
{
    if (embarques.contador == 0 || embarques.contador == 1) 
    {
        alert('Debe tener al menos dos compras en el detalle.')
    }
    else
    {
        reg = embarques.contador;
        regAnt = reg-2;
        regAct = regAnt+1;

        $("#blEmbarqueDetalle"+regAct).val($("#blEmbarqueDetalle"+regAnt).val());
        $("#numeroContenedorEmbarqueDetalle"+regAct).val($("#numeroContenedorEmbarqueDetalle"+regAnt).val());
        $("#numeroCourrierEmbarqueDetalle"+regAct).val($("#numeroCourrierEmbarqueDetalle"+regAnt).val());
        $("#observacionEmbarqueDetalle"+regAct).val($("#observacionEmbarqueDetalle"+regAnt).val());
        $("#descripcionEmbarqueDetalle"+regAct).val($("#descripcionEmbarqueDetalle"+regAnt).val());
        $("#fechaReservaEmbarqueDetalle"+regAct).val($("#fechaReservaEmbarqueDetalle"+regAnt).val());
        $("#fechaRealEmbarqueDetalle"+regAct).val($("#fechaRealEmbarqueDetalle"+regAnt).val());
        $("#fechaMaximaEmbarqueDetalle"+regAct).val($("#fechaMaximaEmbarqueDetalle"+regAnt).val());
        $("#fechaLlegadaZonaFrancaEmbarqueDetalle"+regAct).val($("#fechaLlegadaZonaFrancaEmbarqueDetalle"+regAnt).val());
        $("#fechaArriboPuertoEstimadaEmbarqueDetalle"+regAct).val($("#fechaArriboPuertoEstimadaEmbarqueDetalle"+regAnt).val());
        $("#fechaArriboPuertoEmbarqueDetalle"+regAct).val($("#fechaArriboPuertoEmbarqueDetalle"+regAnt).val());
    }
    
}