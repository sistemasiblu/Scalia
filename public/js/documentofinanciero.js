//Obtener ip del servidor
var ip = ((location.href.split('/'))[0])+'//'+((location.href.split('/'))[2]);

function validarFormulario(event)
{
    var route = "http://"+location.host+"/documentofinanciero";
    var token = $("#token").val();
    var dato0 = document.getElementById('idDocumentoFinanciero').value;
    var dato1 = document.getElementById('ListaFinanciacion_idListaFinanciacion').value;
    var dato2 = document.getElementById('numeroDocumentoFinanciero').value;
    var fechaProrroga = document.querySelectorAll("[name='fechaProrrogaDocumentoFinancieroProrroga[]']");
    var dato3 = [];    
    
    var valor = '';
    var sw = true;
    
    for(var j=0,i=fechaProrroga.length; j<i;j++)
    {
        dato3[j] = fechaProrroga[j].value;
    }

    $.ajax({
        async: false,
        url:route,
        headers: {'X-CSRF-TOKEN': token},
        type: 'POST',
        dataType: 'json',
        data: {respuesta: 'falso',
                idDocumentoFinanciero: dato0,
                ListaFinanciacion_idListaFinanciacion: dato1,
                numeroDocumentoFinanciero: dato2,
                fechaProrrogaDocumentoFinancieroProrroga: dato3,
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

                (typeof msj.responseJSON.ListaFinanciacion_idListaFinanciacion === "undefined" ? document.getElementById('ListaFinanciacion_idListaFinanciacion').style.borderColor = '' : document.getElementById('ListaFinanciacion_idListaFinanciacion').style.borderColor = '#a94442');

                (typeof msj.responseJSON.numeroDocumentoFinanciero === "undefined" ? document.getElementById('numeroDocumentoFinanciero').style.borderColor = '' : document.getElementById('numeroDocumentoFinanciero').style.borderColor = '#a94442');


                for(var j=0,i=fechaProrroga.length; j<i;j++)
                {
                    (typeof respuesta['fechaProrrogaDocumentoFinancieroProrroga'+j] === "undefined" 
                        ? document.getElementById('fechaProrrogaDocumentoFinancieroProrroga'+j).style.borderColor = '' 
                        : document.getElementById('fechaProrrogaDocumentoFinancieroProrroga'+j).style.borderColor = '#a94442');
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


function validarListaFinanciacion()
{
    listaFinanciacion = $("#ListaFinanciacion_idListaFinanciacion").val();
    if (listaFinanciacion == '' || listaFinanciacion == 0) 
    {
        alert('Debe seleccionar un tipo de financiación.');
    }
}

function consultarDocumentoFinanciero(numeroDocumento)
{
    listaFinanciacion = $("#ListaFinanciacion_idListaFinanciacion").val();
    if (listaFinanciacion == '' || listaFinanciacion == 0) 
    {
        alert('Debe seleccionar un tipo de financiación.')
    }
    else
    {
        var token = document.getElementById('token').value;

        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            data: {'listaFinanciacion': listaFinanciacion,'numeroDocumento': numeroDocumento},
            url:   'http://'+location.host+'/consultarDocumentoFinanciero/',
            type:  'post',
            beforeSend: function(){
                //Lo que se hace antes de enviar el formulario
                },
            success: function(respuesta){
                if (respuesta != '') 
                {
                    $("#fechaNegociacionDocumentoFinanciero").val(respuesta["fechaElaboracionMovimiento"]);
                    $("#fechaVencimientoDocumentoFinanciero").val(respuesta["fechaVencimientoMovimiento"]);
                    $("#nombreEntidadDocumentoFinanciero").val(respuesta["nombre1Tercero"]);
                    $("#valorDocumentoFinanciero").val(respuesta["valorTotalMovimiento"]);    
                }
                else
                {
                    alert('No existe el número de movimiento digitado.');
                    $("#fechaNegociacionDocumentoFinanciero").val('');
                    $("#fechaVencimientoDocumentoFinanciero").val('');
                    $("#nombreEntidadDocumentoFinanciero").val('');
                    $("#valorDocumentoFinanciero").val('');       
                }
            },
            error:    function(xhr,err){ 
                alert("Error");
            }
        });    
    }

    
}

function agregarCompras()
{
    $('#modalCompras').modal('show');

    if ($("#estadoModalCompra").val() == 0) 
    {
        var lastIdx = null;
        window.parent.$("#tcompradocumentofinanciero").DataTable().ajax.url(ip+"/datosCompraDocumentoFinanciero").load();
         // Abrir modal
        window.parent.$("#modalCompras").modal()

        $("a.toggle-vis").on( "click", function (e) {
            e.preventDefault();
     
            // Get the column API object
            var column = table.column( $(this).attr("data-column") );
     
            // Toggle the visibility
            column.visible( ! column.visible() );
        } );

        window.parent.$("#tcompradocumentofinanciero tbody").on( "mouseover", "td", function () 
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
        window.parent.$("#tcompradocumentofinanciero tfoot th").each( function () 
        {
            var title = window.parent.$("#tcompradocumentofinanciero thead th").eq( $(this).index() ).text();
            $(this).html( "<input type='text' placeholder='Buscar por "+title+"'/>" );
        });
     
        // DataTable
        var table = window.parent.$("#tcompradocumentofinanciero").DataTable();
     
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

        $('#tcompradocumentofinanciero tbody').on( 'click', 'tr', function () {
            $(this).toggleClass('selected');
        } );

        $('#botonCompra').click(function() {
            var datos = table.rows('.selected').data();

            for (var i = 0; i < datos.length; i++) 
            {
                var valores = new Array(0, datos[i][5], datos[i][0], 0, datos[i][2], datos[i][3], datos[i][4]);
                window.parent.documentofinanciero.agregarCampos(valores,'A');    
            }
            calcularTotales();
            $("#estadoModalCompra").val(1)
            window.parent.$("#modalCompras").modal("hide");
        });

        $('#botonCloseCompra').click(function() {
            $("#estadoModalCompra").val(1)
        });
    }
    else
    {
        window.parent.$("#tcompradocumentofinanciero tbody tr").each( function () 
        {
            $(this).removeClass('selected');
        });

        window.parent.$("#modalCompras").modal()
    }
}

function calcularTotales()
{
    totalfob = 0;
    totalprogramado = 0;


    for (var i = 0; i < window.parent.documentofinanciero.contador; i++) 
    {
        if(typeof $("#valorFobDocumentoFinancieroDetalle"+i, window.parent.document).val() != 'undefined' &&
            $("#valorFobDocumentoFinancieroDetalle"+i, window.parent.document).val() > 0)
        {
            totalfob += parseFloat($("#valorFobDocumentoFinancieroDetalle"+i, window.parent.document).val());
        }    

        if(typeof $("#valorPagoDocumentoFinancieroDetalle"+i, window.parent.document).val() != 'undefined' &&
            $("#valorPagoDocumentoFinancieroDetalle"+i, window.parent.document).val() > 0)
        {
            totalprogramado += parseFloat($("#valorPagoDocumentoFinancieroDetalle"+i, window.parent.document).val());
        }    

        if (parseFloat($("#valorFobDocumentoFinancieroDetalle"+i).val()) < parseFloat($('#valorPagoDocumentoFinancieroDetalle'+i).val())) 
        {
            alert('Está excediendo la cantidad del forward.');
            $('#valorPagoDocumentoFinancieroDetalle'+i).css('background-color', '#F5A9A9');
        }
        else
        {   
            $('#valorPagoDocumentoFinancieroDetalle'+i).css('background-color', '');
        }
    }

    $("#totalFobDocumentoFinanciero").val(totalfob);
    $("#totalProgramadoDocumentoFinanciero").val(totalprogramado);
}

function validarFecha(id, fechaActa)
{
    reg = id.replace('fechaProrrogaDocumentoFinancieroProrroga',' ');

    regAnt = reg-1;

    fechaAnt = $("#fechaProrrogaDocumentoFinancieroProrroga"+regAnt).val();
    fechaAct = $("#fechaProrrogaDocumentoFinancieroProrroga"+reg).val();

    var fecha1 = new   Date(fechaAnt.substring(0,4),fechaAnt.substring(5,7)-1,fechaAnt.substring(8,10));
    var fecha2 = new Date(fechaActa.substring(0,4),fechaActa.substring(5,7)-1,fechaActa.substring(8,10));

    fechaAnterior = fecha1.getTime()
    fechaActual = fecha2.getTime()

    if(fechaAnterior >= fechaActual)
    {
        alert('Esta fecha no puede ser menor o igual que la fecha del registro anterior.');
        $("#"+id).css('background-color', '#F5A9A9');
    }
    else
    {
        $("#"+id).css('background-color', '');   
    }
    
}