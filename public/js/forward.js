//Obtener ip del servidor
var ip = ((location.href.split('/'))[0])+'//'+((location.href.split('/'))[2]);

$(document).ready( function () {

    $("#fechaNegociacionForward, #fechaVencimientoForward").datetimepicker
    (
        ({
           format: "YYYY-MM-DD"
         })
    );
});

function consultarValorPesos(tasa)
{
    valorPeso = parseFloat($("#valorDolarForward").val()) * parseFloat(tasa);

    $("#valorPesosForward").val(valorPeso);
}

function validarFormulario(event)
{
    var route = "http://"+location.host+"/forward";
    var token = $("#token").val();
    var dato0 = document.getElementById('idForward').value;
    var dato1 = document.getElementById('fechaVencimientoForward').value;
    var dato2 = document.getElementById('valorDolarForward').value;
    var dato3 = document.getElementById('Tercero_idBanco').value;
    var dato4 = document.getElementById('numeroForward').value;
    var dato5 = document.getElementById('totalForward').value;

    var valor = '';
    var sw = true;

    $.ajax({
        async: false,
        url:route,
        headers: {'X-CSRF-TOKEN': token},
        type: 'POST',
        dataType: 'json',
        data: {respuesta: 'falso',
                idForward: dato0,
                fechaVencimientoForward: dato1,
                valorDolarForward: dato2,
                Tercero_idBanco: dato3,
                numeroForward: dato4,
                totalForward: dato5
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

                (typeof msj.responseJSON.fechaVencimientoForward === "undefined" ? document.getElementById('fechaVencimientoForward').style.borderColor = '' : document.getElementById('fechaVencimientoForward').style.borderColor = '#a94442');

                (typeof msj.responseJSON.Tercero_idBanco === "undefined" ? document.getElementById('Tercero_idBanco').style.borderColor = '' : document.getElementById('Tercero_idBanco').style.borderColor = '#a94442');

                (typeof msj.responseJSON.numeroForward === "undefined" ? document.getElementById('numeroForward').style.borderColor = '' : document.getElementById('numeroForward').style.borderColor = '#a94442');

                (typeof msj.responseJSON.valorDolarForward === "undefined" ? document.getElementById('totalForward').style.borderColor = '' : document.getElementById('totalForward').style.borderColor = '#a94442');

                var mensaje = 'Por favor verifique los siguientes valores <br><ul>';
                $.each(respuesta,function(index, value){
                    mensaje +='<li>' +value+'</li>';
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
                enviarDatosListaTercero(datos[0][0], datos[0][1], datos[0][2], datos[0][3], datos[0][4]);
            }

        window.parent.$("#ListaSelectTercero").modal("hide");

    } );
}

function enviarDatosListaTercero(id, nombre, nombreComercial, cod, objeto)
{
    if (objeto == 'bancoForward') 
    {
        $("input[name='bancoForward']").each(function() 
        {
            $(this).val(nombre);
        });

        $("input[id='Tercero_idBanco']").each(function() 
        {
            $(this).val(id);
        });
    }  
}

function abrirModalTemporada(objeto)
{
    var lastIdx = null;
    window.parent.$("#ttemporada").DataTable().ajax.url(ip+"/datosTemporadaForward?reg="+objeto.id).load();
     // Abrir modal
    window.parent.$("#modalTemporada").modal()

    $("a.toggle-vis").on( "click", function (e) {
        e.preventDefault();
 
        // Get the column API object
        var column = table.column( $(this).attr("data-column") );
 
        // Toggle the visibility
        column.visible( ! column.visible() );
    } );

    window.parent.$("#ttemporada tbody").on( "mouseover", "td", function () 
    {
        var colIdx = table.cell(this).index().column;

        if ( colIdx !== lastIdx ) {
            $( table.cells().nodes() ).removeClass( "highlight" );
            $( table.column( colIdx ).nodes() ).addClass( "highlight" );
        }
    }).on( "mouseleave", function () {
        $( table.cells().nodes() ).removeClass( "highlight" );
    });


    // Setup - add a text input to each footer cell
    window.parent.$("#ttemporada tfoot th").each( function () 
    {
        var title = window.parent.$("#ttemporada thead th").eq( $(this).index() ).text();
        $(this).html( "<input type='text' placeholder='Buscar por "+title+"'/>" );
    });
 
    // DataTable
    var table = window.parent.$("#ttemporada").DataTable();
 
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

    $('#ttemporada tbody').on("dblclick", "tr", function ()
    {
        if ( $(this).hasClass("selected") ) 
        {
            $(this).removeClass("selected");
        }
        else 
        {
            table.$("tr.selected").removeClass("selected");
            $(this).addClass("selected");
        }

        var datos = table.rows('.selected').data();

        if (datos.length > 0) 
        {
            llenarDatosTemporada(datos[0][4], datos[0][0], datos[0][1], datos[0][5]);
            calcularTotales();
        }
        window.parent.$("#modalTemporada").modal("hide");
    });
}

function llenarDatosTemporada(id, nombre, valorfob, objeto)
{
    reg = objeto.replace("botonTemporada","");
    $("#nombreTemporadaForwardDetalle"+reg).val(nombre);
    $("#Temporada_idTemporada"+reg).val(id);
    $("#valorForwardDetalle"+reg).val(valorfob);
    $("#valorRealForwardDetalle"+reg).val(valorfob);

    //Limpio los campos en la compra
    $("#Compra_idCompra"+reg).val("");
    $("#numeroCompraForwardDetalle"+reg).val("");
    $("#proveedorCompraForwardDetalle"+reg).val("");
    
}

function abrirModalCompra(objeto)
{
    var lastIdx = null;
    window.parent.$("#tcompra").DataTable().ajax.url(ip+"/datosCompraForward?reg="+objeto.id).load();
     // Abrir modal
    window.parent.$("#modalCompras").modal()

    $("a.toggle-vis").on( "click", function (e) {
        e.preventDefault();
 
        // Get the column API object
        var column = table.column( $(this).attr("data-column") );
 
        // Toggle the visibility
        column.visible( ! column.visible() );
    } );

    window.parent.$("#tcompra tbody").on( "mouseover", "td", function () 
    {
        var colIdx = table.cell(this).index().column;

        if ( colIdx !== lastIdx ) {
            $( table.cells().nodes() ).removeClass( "highlight" );
            $( table.column( colIdx ).nodes() ).addClass( "highlight" );
        }
    }).on( "mouseleave", function () {
        $( table.cells().nodes() ).removeClass( "highlight" );
    });


    // Setup - add a text input to each footer cell
    window.parent.$("#tcompra tfoot th").each( function () 
    {
        var title = window.parent.$("#tcompra thead th").eq( $(this).index() ).text();
        $(this).html( "<input type='text' placeholder='Buscar por "+title+"'/>" );
    });
 
    // DataTable
    var table = window.parent.$("#tcompra").DataTable();
 
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

    // Remover el evento doble click para que no quede acumulado
    $('#tcompra tbody').unbind("dblclick");

    $('#tcompra tbody').on("dblclick", "tr", function ()
    {
        if ( $(this).hasClass("selected") ) 
        {
            $(this).removeClass("selected");
        }
        else 
        {
            table.$("tr.selected").removeClass("selected");
            $(this).addClass("selected");
        }

        var datos = table.rows('.selected').data();

        if (datos.length > 0) 
        {
            temp = 0;

            for (var i = 0; i < forwards.contador; i++) 
            {
                if ($("#Temporada_idTemporada"+i).val() == datos[0][8]) 
                {
                    temp = 1;
                }
            }
            if (temp == 1) 
            {
                alert('La compra no puede ser agregada porque ya hay una temporada que la contiene.');
            }
            else
            {
                llenarCompra(datos[0][6], datos[0][1], datos[0][2], datos[0][5], datos[0][8]);
            }
            calcularTotales();
        }
        window.parent.$("#modalCompras").modal("hide");
    });
}

function llenarCompra(id, nombre, proveedor, valorfob, objeto)
{
    reg = objeto.replace("botonCompra","");
    $("#Compra_idCompra"+reg).val(id);
    $("#numeroCompraForwardDetalle"+reg).val(nombre);
    $("#proveedorCompraForwardDetalle"+reg).val(proveedor);
    $("#valorForwardDetalle"+reg).val(valorfob);
    $("#valorRealForwardDetalle"+reg).val(valorfob);

    //Limpio los campos en temporada
    $("#nombreTemporadaForwardDetalle"+reg).val("");
    $("#Temporada_idTemporada"+reg).val("");
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

                $('#proveedorCompraForwardDetalle'+reg).val(respuesta['nombreProveedorCompra']);
            },
            error: function(xhr,err)
            { 
                alert("Error");
            }
        });
}

function calcularTotales()
{
    valor = 0;
   
    for (var i = 0; i < window.parent.forwards.contador; i++) 
    {
        if(typeof $("#valorRealForwardDetalle"+i, window.parent.document).val() != 'undefined' &&
            $("#valorRealForwardDetalle"+i, window.parent.document).val() > 0)
        {
            valor += parseFloat($("#valorRealForwardDetalle"+i, window.parent.document).val());
        }    

        if (parseFloat($("#valorRealForwardDetalle"+i, window.parent.document).val()) > $("#valorForwardDetalle"+i, window.parent.document).val()) 
        {
            $("#valorRealForwardDetalle"+i, window.parent.document).css('background-color', '#F5A9A9');
            $("#Modificar").prop("disabled",true);
            $("#Adicionar").prop("disabled",true);
        }
        else
        {   
            $("#valorRealForwardDetalle"+i, window.parent.document).css('background-color', '');
            $("#Modificar").prop("disabled",false);
            $("#Adicionar").prop("disabled",false);
        }
    }

    $('#totalForward', window.parent.document).val(valor);

    if (parseFloat($("#valorDolarForward").val()) < parseFloat($('#totalForward').val())) 
    {
        alert('Está excediendo la cantidad del forward.');
        $('#totalForward').css('background-color', '#F5A9A9');
    }
    else
    {   
        $('#totalForward').css('background-color', '');
    }
}

function cargarForwardPadre(idForwardPadre)
{
    var token = document.getElementById('token').value;
    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            data: {'idForwardPadre': idForwardPadre},
            url:   ip+'/consultarComprasForwardPadre/',
            type:  'post',
            beforeSend: function(){
                },
            success: function(respuesta)
            {
                document.getElementById("contenedor_forward").innerHTML = '';
                var idTemporada = new Array();
                var nombreTemporada = new Array();
                var idCompra = new Array();
                var numeroCompra = new Array();
                var nombreProveedor = new Array();
                var valorFinal = new Array();

                for (var i = 0; i < respuesta.length; i++) 
                {
                    idTemporada[i] = respuesta[i]["Temporada_idTemporada"];
                    nombreTemporada[i] = respuesta[i]["nombreTemporadaForwardDetalle"];
                    idCompra[i] = respuesta[i]["Compra_idCompra"];
                    numeroCompra[i] = respuesta[i]["numeroCompraForwardDetalle"];
                    nombreProveedor[i] = respuesta[i]["nombreProveedorCompra"];
                    valorFinal[i] = respuesta[i]["saldoFinalCarteraForward"];
                    
                    var valores = new Array(0,idTemporada[i], nombreTemporada[i], '', idCompra[i], numeroCompra[i], '', nombreProveedor[i], valorFinal[i], valorFinal[i], 0);
                    window.parent.forwards.agregarCampos(valores,'A'); 
                }  

                calcularTotales();
            },
            error: function(xhr,err)
            { 
                alert("Error");
                document.getElementById("contenedor_forward").innerHTML = '';
            }
        });
}