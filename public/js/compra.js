
//Obtener ip del servidor
var ip = ((location.href.split('/'))[0])+'//'+((location.href.split('/'))[2]);

$(document).ready( function () {

	$("#fechaDeliveryCompra, #fechaForwardCompra, #fechaCompra, #fechaInicialTemporadaSAYA, #fechaFinalTemporadaSAYA, #fechaInicialEventoSAYA, #fechaFinalEventoSAYA, #fechaMaximaDespachoCompra").datetimepicker
	(
		({
           format: "YYYY-MM-DD"
         })
	);
});


function llenarMetadatos(objeto) 
{
	objeto = objeto.value;
	
    var token = document.getElementById('token').value;
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        dataType: "json",
        data: {'objeto': objeto},
        url:   ip+'/consultaMetadatosCompra/',
        type:  'post',
        success: function(respuesta){

        document.getElementById('Tercero_idProveedor').value = respuesta['idTercero'];
        document.getElementById('nombreProveedorCompra').value = respuesta['nombre1Tercero'];
        document.getElementById('fechaCompra').value = respuesta['fechaElaboracionMovimiento'];
        document.getElementById('Movimiento_idMovimiento').value = respuesta['idMovimiento'];
        document.getElementById('valorCompra').value = respuesta['valorTotalMovimiento']
        document.getElementById('FormaPago_idFormaPago').value = respuesta['FormaPago_idFormaPago'];
        document.getElementById('formaPagoProveedorCompra').value = respuesta['nombreFormaPago'];
        document.getElementById('cantidadCompra').value = respuesta['totalUnidadesMovimiento'];
        document.getElementById('fechaDeliveryCompra').value = respuesta['fechaMaximaMovimiento'];
        document.getElementById('diaPagoClienteCompra').value = respuesta['diasFormaPago'];
        document.getElementById('compradorVendedorCompra').value = respuesta['nombre1Vendedor'];
        document.getElementById('Tercero_idVendedor').value = respuesta['Tercero_idVendedor'];
        },
        error: function(xhr,err){ 
            alert("Este número de compra no existe en SAYA.");            
            $("#fechaCompra").attr("readonly", false);
            $("#nombreProveedorCompra").attr("readonly", false);
            $("#formaPagoClienteCompra").attr("readonly", false);
        }
    });
}


function abrirModal(nombreTabla,nombreCampo,codigoCampo,objeto, tipotercero)
{
    var lastIdx = null;
    window.parent.$("#tlistaselect").DataTable().ajax.url(ip+"/datosListaSelectImportacion?nombreTabla="+nombreTabla+"&campo="+nombreCampo+"&codigo="+codigoCampo+"&value="+objeto.value+"&tipotercero="+tipotercero+"&campoTabla="+objeto.name).load();
     // Abrir modal
    window.parent.$("#ListaSelect").modal()

    $("a.toggle-vis").on( "click", function (e) {
        e.preventDefault();
 
        // Get the column API object
        var column = table.column( $(this).attr("data-column") );
 
        // Toggle the visibility
        column.visible( ! column.visible() );
    } );

    window.parent.$("#tlistaselect tbody").on( "mouseover", "td", function () 
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
    window.parent.$("#tlistaselect tfoot th").each( function () 
    {
        var title = window.parent.$("#tlistaselect thead th").eq( $(this).index() ).text();
        $(this).html( "<input type='text' placeholder='Buscar por "+title+"'/>" );
    });
 
    // DataTable
    var table = window.parent.$("#tlistaselect").DataTable();
 
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

    window.parent.$("#tlistaselect tbody").on( "dblclick", "tr", function () 
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
                enviarDatosLista(datos[0][0], datos[0][1], datos[0][2], datos[0][3], datos[0][4]);
            }

        window.parent.$("#ListaSelect").modal("hide");

    } );

}

function enviarDatosLista(id,nombre,cod,objeto, pago)
{   
    if (objeto == 'nombreTemporadaCompra') 
    {
        $("input[name='nombreTemporadaCompra']").each(function() 
        {
            $(this).val(nombre);
        });

        $("input[id='Temporada_idTemporada']").each(function() 
        {
            $(this).val(id);
        });
    }
    else if(objeto == 'nombreCiudadCompra')
    {
        $("input[name='nombreCiudadCompra']").each(function() 
        {
            $(this).val(nombre);
        });

        $("input[id='Ciudad_idPuerto']").each(function() 
        {
            $(this).val(id);
        });
    }
    else if (objeto == 'eventoCompra') 
    {
        $("input[name='eventoCompra']").each(function() 
        {
            $(this).val(nombre);
        });
    }

    else if (objeto == 'formaPagoProveedorCompra') 
    {
        $("input[name='formaPagoProveedorCompra']").each(function() 
        {
            $(this).val(nombre);
        });
        
        $("input[id='FormaPago_idFormaPago']").each(function() 
        {
            $(this).val(id);
        });
    }

    if(objeto == 'nombreTemporadaCompra')
    {
        llenarMetadatosTemporada(id);
    }
}

function llenarMetadatosVersion(idDocImp, numeroVersion, numeroCompra)
{
    if (numeroVersion.indexOf('*') == -1) 
    {
        numVer = numeroVersion;
        var read = true;
        document.getElementById('Modificar').disabled = true;
    }
    else
    {
        var numVer = parseFloat(numeroVersion.replace('*', ''))-1;
        var read = false;
        document.getElementById('Modificar').disabled = false;
    }

    var token = document.getElementById('token').value;
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        dataType: "json",
        data: {'idDocImp': idDocImp, 'numeroVersion': numVer, 'numeroCompra': numeroCompra},
        url:   ip+'/llenarDatosVersionCompra/',
        type:  'post',
        beforeSend: function(){
            },
        success: function(respuesta){

        document.getElementById('fechaCompra').value = respuesta['fechaCompra'];
        document.getElementById('fechaCompra').readOnly = read;

        document.getElementById('nombreTemporadaCompra').value = respuesta['nombreTemporadaCompra'];
        document.getElementById('Temporada_idTemporada').value = respuesta['Temporada_idTemporada'];
        document.getElementById('nombreTemporadaCompra').readOnly = read;

        document.getElementById('nombreProveedorCompra').value = respuesta['nombreProveedorCompra'];
        document.getElementById('Tercero_idProveedor').value = respuesta['Tercero_idProveedor'];
        document.getElementById('nombreProveedorCompra').readOnly = read;

        document.getElementById('numeroCompra').value = respuesta['numeroCompra'];
        document.getElementById('Movimiento_idMovimiento').value = respuesta['Movimiento_idMovimiento'];
        if ($("#DocumentoImportacion_idDocumentoImportacion").val() == 2) 
            document.getElementById('numeroCompra').readOnly = read;

        document.getElementById('formaPagoProveedorCompra').value = respuesta['formaPagoProveedorCompra'];
        document.getElementById('FormaPago_idFormaPago').value = respuesta['FormaPago_idFormaPago'];
        document.getElementById('formaPagoProveedorCompra').readOnly = read;

        document.getElementById('nombreClienteCompra').value = respuesta['nombreClienteCompra'];
        document.getElementById('Tercero_idCliente').value = respuesta['Tercero_idCliente'];
        document.getElementById('nombreClienteCompra').readOnly = read;

        document.getElementById('formaPagoClienteCompra').value = respuesta['formaPagoClienteCompra'];
        document.getElementById('formaPagoClienteCompra').readOnly = read;

        document.getElementById('compradorVendedorCompra').value = respuesta['compradorVendedorCompra'];
        document.getElementById('compradorVendedorCompra').readOnly = read;

        document.getElementById('eventoCompra').value = respuesta['eventoCompra'];
        document.getElementById('eventoCompra').readOnly = read;

        document.getElementById('valorCompra').value = respuesta['valorCompra'];
        document.getElementById('valorCompra').readOnly = read;

        document.getElementById('cantidadCompra').value = respuesta['cantidadCompra'];
        document.getElementById('cantidadCompra').readOnly = read;

        $("#unidadMedida").val(respuesta['codigoUnidadMedidaCompra']).change();
        document.getElementById('unidadMedida').disabled = read;

        document.getElementById('pesoCompra').value = respuesta['pesoCompra'];
        document.getElementById('pesoCompra').readOnly = read;

        document.getElementById('volumenCompra').value = respuesta['volumenCompra'];
        document.getElementById('volumenCompra').readOnly = read;

        document.getElementById('bultoCompra').value = respuesta['bultoCompra'];
        document.getElementById('bultoCompra').readOnly = read;

        document.getElementById('nombreCiudadCompra').value = respuesta['nombreCiudadCompra'];
        document.getElementById('Ciudad_idPuerto').value = respuesta['Ciudad_idPuerto'];
        document.getElementById('nombreCiudadCompra').readOnly = read;

        document.getElementById('fechaDeliveryCompra').value = respuesta['fechaDeliveryCompra'];
        document.getElementById('fechaDeliveryCompra').readOnly = read;

        document.getElementById('fechaForwardCompra').value = respuesta['fechaForwardCompra'];
        document.getElementById('fechaForwardCompra').readOnly = read;

        document.getElementById('valorForwardCompra').value = respuesta['valorForwardCompra'];
        document.getElementById('valorForwardCompra').readOnly = read;

        document.getElementById('diaPagoClienteCompra').value = respuesta['diaPagoClienteCompra'];
        document.getElementById('diaPagoClienteCompra').readOnly = read;

        document.getElementById('tiempoBodegaCompra').value = respuesta['tiempoBodegaCompra'];
        document.getElementById('tiempoBodegaCompra').readOnly = read;

        document.getElementById('fechaMaximaDespachoCompra').value = respuesta['fechaMaximaDespachoCompra'];
        document.getElementById('fechaMaximaDespachoCompra').disabled = read;

        document.getElementById('observacionCompra').value = respuesta['observacionCompra'];
        document.getElementById('observacionCompra').disabled = read;
        },
        error:    function(xhr,err){
            alert("Error");
        }
    });
}


function listarVersiones(idDocImp, numeroCompra)
{
    var token = document.getElementById('token').value;
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        dataType: "json",
        data: {'idDocImp': idDocImp, 'numeroCompra': numeroCompra},
        url:   ip+'/listarVersionesCompra/',
        type:  'post',
        beforeSend: function(){
            },
        success: function(respuesta){
            $('#numeroVersionMaximaCompra').html(respuesta);
        },
        error:    function(xhr,err){
            alert("Error");
        }
    });
}

function listarUnidadMedida(unidadMedida)
{
    var token = document.getElementById('token').value;
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        dataType: "json",
        data: {'unidadMedida': unidadMedida},
        url:   ip+'/listarUnidadMedida/',
        type:  'post',
        beforeSend: function(){
            },
        success: function(respuesta){

            $('#unidadMedida').html(respuesta);
        },
        error:    function(xhr,err){
            alert("Error");
        }
    });
}

function actualizarEstado(compra)
{   
    var actualizar = confirm("¿Realmente desea actualizar el estado?");
    
    var observacion = prompt("Observación", "");

    if (actualizar) 
    {
        var token = document.getElementById('token').value;
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            data: {'compra': compra, 'observacion': observacion},
            url:   ip+'/actualizarEstadoCompra/',
            type:  'post',
            beforeSend: function(){
                },
            success: function(respuesta){

                alert(respuesta)
                location.reload();
            },
            error:    function(xhr,err){
                alert("Error");
            }
        });
        // $("#modalEstadoCompra").modal()
    }
}

function imprimirFormatoCompra(id, compra, documento, tipo)
{
    window.open(ip+'/embarque/'+id+'?numero='+compra+'&documento='+documento+'&tipo='+tipo,'_blank','width=2500px, height=700px, scrollbars=yes');
}

function abrirModalTercero(nombreTabla,nombreCampo,codigoCampo,objeto, tipotercero)
{
    var lastIdx = null;
    window.parent.$("#tlistaselecttercero").DataTable().ajax.url(ip+"/datosListaSelectImportacionTercero?nombreTabla="+nombreTabla+"&campo="+nombreCampo+"&codigo="+codigoCampo+"&value="+objeto.value+"&tipotercero="+tipotercero+"&campoTabla="+objeto.name).load();
     // Abrir modal
    window.parent.$("#ListaSelectTercero").modal()

    $("a.toggle-vis").on( "click", function (e) {
        e.preventDefault();
 
        // Get the column API object
        var column = table.column( $(this).attr("data-column") );
 
        // Toggle the visibility
        column.visible( ! column.visible() );
    } );

    window.parent.$("#tlistaselecttercero tbody").on( "mouseover", "td", function () 
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
    window.parent.$("#tlistaselecttercero tfoot th").each( function () 
    {
        var title = window.parent.$("#tlistaselecttercero thead th").eq( $(this).index() ).text();
        $(this).html( "<input type='text' placeholder='Buscar por "+title+"'/>" );
    });
 
    // DataTable
    var table = window.parent.$("#tlistaselecttercero").DataTable();
 
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

    window.parent.$("#tlistaselecttercero tbody").on( "dblclick", "tr", function () 
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
                enviarDatosListaTercero(datos[0][0], datos[0][1], datos[0][2], datos[0][3], datos[0][4], datos[0][5], datos[0][8]);
            }

        window.parent.$("#ListaSelectTercero").modal("hide");

    } );

}

function enviarDatosListaTercero(id, nombre, nombreComercial, cod, objeto, pago, idPago)
{
    if (objeto == 'nombreProveedorCompra') 
    {
        $("input[name='nombreProveedorCompra']").each(function() 
        {
            $(this).val(nombre);
        });

        $("input[name='formaPagoProveedorCompra']").each(function() 
        {
            $(this).val(pago);
        });

        $("input[id='FormaPago_idFormaPago']").each(function() 
        {
            $(this).val(idPago);
        });

        $("input[id='Tercero_idProveedor']").each(function() 
        {
            $(this).val(id);
        });
    }    
    else if (objeto == 'nombreClienteCompra') 
    {
        $("input[name='nombreClienteCompra']").each(function() 
        {
            $(this).val(nombre);
        });

        $("input[name='formaPagoClienteCompra']").each(function() 
        {
            $(this).val(pago);
        });

        $("input[id='Tercero_idCliente']").each(function() 
        {
            $(this).val(id);
        });
    }
    else if (objeto == 'compradorVendedorCompra') 
    {
        $("input[name='compradorVendedorCompra']").each(function() 
        {
            $(this).val(nombre);
        });

        $("input[id='Tercero_idVendedor']").each(function() 
        {
            $(this).val(id);
        });
    }
}

function validarCodigoAlterno(valor, campo, tabla)
{
    var token = document.getElementById('token').value;
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        dataType: "json",
        data: {'valor': valor, 'campo': campo, 'tabla': tabla},
        url:   ip+'/consultarCodigoTemporada/',
        type:  'post',
        success: function(respuesta){
            if (respuesta != "") 
            {
                alert(respuesta);
                $("#codigoAlternoTemporadaSAYA").val('');
                $("#codigoAlternoEventoSAYA").val('');
                $("#documentoTerceroSAYA").val('');
                $("#digitoVerificacionSAYA").val(0);
            }
        },
        error: function(xhr,err){ 
            alert('Error');
        }
    });
}

function crearTemporadaCompra()
{
    $("#modalTemporada").modal();
}

function guardarTemporada(codigo, temporada, fechaIni, fechaFin, tolerancia)
{
    if(codigo == '' || temporada == '' || fechaIni == '' || fechaFin == '')
    {
        alert('Todos los campos deben estar llenos.');
    }
    else
    {
        var token = document.getElementById('token').value;
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            data: {'codigo': codigo, 'temporada': temporada, 'fechaIni': fechaIni, 'fechaFin': fechaFin, 'tolerancia': tolerancia},
            url:   ip+'/guardarTemporada/',
            type:  'post',
            success: function(respuesta){
                alert(respuesta);
                $("#modalTemporada").modal("hide");
            },
            error: function(xhr,err){ 
                alert('Error');
            }
        });
    }
}

function crearEventoCompra()
{
    $("#modalEvento").modal();
}

function guardarEvento(tercero, codigo, evento, fechaIni, fechaFin, dias)
{
    if(codigo == '' || evento == '')
    {
        alert('El código y el evento no deben estar vacíos.');
    }
    else
    {
        var token = document.getElementById('token').value;
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            data: {'tercero': tercero, 'codigo': codigo, 'evento': evento, 'fechaIni': fechaIni, 'fechaFin': fechaFin, 'dias': dias},
            url:   ip+'/guardarEvento/',
            type:  'post',
            success: function(respuesta){
                alert(respuesta);
                $("#modalEvento").modal("hide");
            },
            error: function(xhr,err){ 
                alert('Error');
            }
        });
    }
}

function crearProveedorCompra()
{
    $("#modalTercero").modal();
}

function calcularDv(documento)
{
    if (isNaN(documento))
    {
        $("#digitoVerificacionSAYA").val('');
        alert('El valor digitado no es un numero valido, no se calculara digito de verificacion');
    }
    else
    {
        vpri = new Array(16);

        x = 0;
        y = 0;
        z = documento.length;

        vpri[1] = 3;
        vpri[2] = 7;
        vpri[3] = 13;
        vpri[4] = 17;
        vpri[5] = 19;
        vpri[6] = 23;
        vpri[7] = 29;
        vpri[8] = 37;
        vpri[9] = 41;
        vpri[10] = 43;
        vpri[11] = 47;
        vpri[12] = 53;
        vpri[13] = 59;
        vpri[14] = 67;
        vpri[15] = 71;

        for (i = 0; i < z; i++)
        {
            y = (documento.substr(i, 1));
            x += (y * vpri[z - i]);
        }

        y = x % 11;

        if (y > 1)
        {
            dv1 = 11 - y;
        }
        else
        {
            dv1 = y;
        }
        $("#digitoVerificacionSAYA").val(dv1);
    }
}

function guardarProveedor(tipodocumento, documento, digitoVerificacion, nombreA, nombreB, apellidoA, apellidoB)
{
    if(tipodocumento == 0 || documento == '' || nombreA == '' || apellidoA == '')
    {
        alert('Verifique los campos obligatorios por favor.');
    }
    else
    {
        var token = document.getElementById('token').value;
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            data: {'tipodocumento': tipodocumento, 'documento': documento, 'digitoVerificacion': digitoVerificacion, 'nombreA': nombreA, 'nombreB': nombreB, 'apellidoA': apellidoA, 'apellidoB': apellidoB},
            url:   ip+'/guardarProveedor/',
            type:  'post',
            success: function(respuesta){
                alert(respuesta);
                $("#modalTercero").modal("hide");
            },
            error: function(xhr,err){ 
                alert('Error');
            }
        });
    }
}

function mostrarDetalleTemporada(idTemporada)
{
    var token = document.getElementById('token').value;
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            data: {'idTemporada': idTemporada},
            url:   ip+'/mostrarDetalleTemporada/',
            type:  'post',
            success: function(respuesta){
                $("#modalDetalleTemporada").modal();
                $("#detalleTemporada").html(respuesta);
            },
            error: function(xhr,err){ 
                alert('Error');
            }
        });
}

function llenarMetadatosTemporada(idTemporada)
{
   var token = document.getElementById('token').value;
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            data: {'idTemporada': idTemporada},
            url:   ip+'/llenarMetadatosCompraTemporada/',
            type:  'post',
            success: function(respuesta){
                if(respuesta == '')
                {
                    alert('No se encontraron registros en las compras de esta temporada.');
                    $("#nombreClienteCompra").val('');
                    $("#Tercero_idCliente").val('');
                    $("#formaPagoClienteCompra").val('');
                    $("#eventoCompra").val('');
                    $("#diaPagoClienteCompra").val('');
                }
                else
                {
                    $("#nombreClienteCompra").val(respuesta[0]['nombreClienteCompra']);
                    $("#Tercero_idCliente").val(respuesta[0]['Tercero_idCliente']);
                    $("#formaPagoClienteCompra").val(respuesta[0]['formaPagoClienteCompra']);
                    $("#eventoCompra").val(respuesta[0]['eventoCompra']);
                    $("#diaPagoClienteCompra").val(respuesta[0]['diaPagoClienteCompra']);
                }
            },
            error: function(xhr,err){ 
                alert('Error');
            }
        }); 
}