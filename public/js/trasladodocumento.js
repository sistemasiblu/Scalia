//Obtener ip del servidor
var ip = ((location.href.split('/'))[0])+'//'+((location.href.split('/'))[2]);

$(document).ready( function () {

	$("#fechaElaboracionTrasladoDocumento").datetimepicker
	(
		({
           format: "YYYY-MM-DD"
         })
	);
});

function validarFormulario(event)
{
    var route = "http://"+location.host+"/trasladodocumento";
    var token = $("#token").val();
    var dato0 = document.getElementById('idTrasladoDocumento').value;
    var dato1 = document.getElementById('numeroTrasladoDocumento').value;
    var dato2 = document.getElementById('descripcionTrasladoDocumento').value;
    var dato3 = document.getElementById('fechaElaboracionTrasladoDocumento').value;
    var dato4 = document.getElementById('estadoTrasladoDocumento').value;
    var dato5 = document.getElementById('SistemaInformacion_idOrigen').value;
    var dato6 = document.getElementById('SistemaInformacion_idDestino').value;
    var documento = document.querySelectorAll("[name='documentoDestinoTrasladoDocumentoDetalle[]']");
    var concepto = document.querySelectorAll("[name='documentoConceptoDestinoTrasladoDocumentoDetalle[]']");
    var tercero = document.querySelectorAll("[name='terceroDestinoTrasladoDocumentoDetalle[]']");
    var dato7 = [];
    var dato8 = [];
    var dato9 = [];

    var valor = '';
    var sw = true;

    for(var j=0,i=documento.length; j<i;j++)
    {
        dato7[j] = documento[j].value;
    }

    for(var j=0,i=concepto.length; j<i;j++)
    {
        dato8[j] = concepto[j].value;
    }

    for(var j=0,i=tercero.length; j<i;j++)
    {
        dato9[j] = tercero[j].value;
    }

    $.ajax({
        async: false,
        url:route,
        headers: {'X-CSRF-TOKEN': token},
        type: 'POST',
        dataType: 'json',
        data: {respuesta: 'falso',
                idTrasladoDocumento: dato0,
                numeroTrasladoDocumento: dato1,
                descripcionTrasladoDocumento: dato2,
                fechaElaboracionTrasladoDocumento: dato3,
                estadoTrasladoDocumento: dato4,
                SistemaInformacion_idOrigen: dato5,
                SistemaInformacion_idDestino: dato6,
                documentoDestinoTrasladoDocumentoDetalle: dato7,
                documentoConceptoDestinoTrasladoDocumentoDetalle: dato8,
                terceroDestinoTrasladoDocumentoDetalle: dato9

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

                (typeof msj.responseJSON.numeroTrasladoDocumento === "undefined" ? document.getElementById('numeroTrasladoDocumento').style.borderColor = '' : document.getElementById('numeroTrasladoDocumento').style.borderColor = '#a94442');

                (typeof msj.responseJSON.descripcionTrasladoDocumento === "undefined" ? document.getElementById('descripcionTrasladoDocumento').style.borderColor = '' : document.getElementById('descripcionTrasladoDocumento').style.borderColor = '#a94442');

                (typeof msj.responseJSON.fechaElaboracionTrasladoDocumento === "undefined" ? document.getElementById('fechaElaboracionTrasladoDocumento').style.borderColor = '' : document.getElementById('fechaElaboracionTrasladoDocumento').style.borderColor = '#a94442');

                (typeof msj.responseJSON.estadoTrasladoDocumento === "undefined" ? document.getElementById('estadoTrasladoDocumento').style.borderColor = '' : document.getElementById('estadoTrasladoDocumento').style.borderColor = '#a94442');

                (typeof msj.responseJSON.SistemaInformacion_idOrigen === "undefined" ? document.getElementById('SistemaInformacion_idOrigen').style.borderColor = '' : document.getElementById('SistemaInformacion_idOrigen').style.borderColor = '#a94442');

                (typeof msj.responseJSON.SistemaInformacion_idDestino === "undefined" ? document.getElementById('SistemaInformacion_idDestino').style.borderColor = '' : document.getElementById('SistemaInformacion_idDestino').style.borderColor = '#a94442');

                for(var j=0,i=documento.length; j<i;j++)
                {
                    (typeof respuesta['documentoDestinoTrasladoDocumentoDetalle'+j] === "undefined" ? document.getElementById('documentoDestinoTrasladoDocumentoDetalle'+j).style.borderColor = '' : document.getElementById('documentoDestinoTrasladoDocumentoDetalle'+j).style.borderColor = '#a94442');
                }

                for(var j=0,i=concepto.length; j<i;j++)
                {
                    (typeof respuesta['documentoConceptoDestinoTrasladoDocumentoDetalle'+j] === "undefined" ? document.getElementById('documentoConceptoDestinoTrasladoDocumentoDetalle'+j).style.borderColor = '' : document.getElementById('documentoConceptoDestinoTrasladoDocumentoDetalle'+j).style.borderColor = '#a94442');
                }

                for(var j=0,i=tercero.length; j<i;j++)
                {
                    (typeof respuesta['terceroDestinoTrasladoDocumentoDetalle'+j] === "undefined" ? document.getElementById('terceroDestinoTrasladoDocumentoDetalle'+j).style.borderColor = '' : document.getElementById('terceroDestinoTrasladoDocumentoDetalle'+j).style.borderColor = '#a94442');
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

function fechaInterface(estado)
{
    var fecha_actual = new Date();
    
    fecha = fecha_actual.getFullYear()+'-'+(fecha_actual.getMonth()+1)+'-'+fecha_actual.getDate();

    if (estado == 'EnProceso') 
        $("#fechaTrasladoDocumento").val('');
    else
        $("#fechaTrasladoDocumento").val(fecha);
}

function abrirModalDocumentos(idBd, documento, concepto, tercero)
{   
    if(idBd == 0) 
    {
        alert('Debe seleccionar la base de datos de origen.');
    }
    else
    {
        var lastIdx = null;
        window.parent.$("#tmodalInterface").DataTable().ajax.url("http://"+location.host+"/datosInterface?idBd="+idBd+"&documento="+documento+"&concepto="+concepto+"&tercero="+tercero).load();
         // Abrir modal
        window.parent.$("#modalInterface").modal()

        $("a.toggle-vis").on( "click", function (e) {
            e.preventDefault();
     
            // Get the column API object
            var column = table.column( $(this).attr("data-column") );
     
            // Toggle the visibility
            column.visible( ! column.visible() );
        } );

        window.parent.$("#tmodalInterface tbody").on( "mouseover", "td", function () 
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
        window.parent.$("#tmodalInterface tfoot th").each( function () 
        {
            var title = window.parent.$("#tmodalInterface thead th").eq( $(this).index() ).text();
            $(this).html( "<input type='text' placeholder='Buscar por "+title+"'/>" );
        });
     
        // DataTable
        var table = window.parent.$("#tmodalInterface").DataTable();
     
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

        $('#tmodalInterface tbody').on('click', 'tr', function () {
            $(this).toggleClass('selected');
        } );

        $('#btnInterface').click(function() {
            var datos = table.rows('.selected').data();

            for (var i = 0; i < datos.length; i++) 
            {
                var valores = new Array(datos[i][5],datos[i][0],datos[i][6],datos[i][1],datos[i][7],datos[i][2],datos[i][8],datos[i][3],datos[i][4],'','','','','','','','','','',0,0);
                window.parent.traslado.agregarCampos(valores,'A');  
            }
            window.parent.$("#modalInterface").modal("hide");
        });
    }
}

function abrirModalInterfaceDestino(tabla,idCampo,nombreCampo,id)
{
    idBd = $("#SistemaInformacion_idDestino option:selected").val();

    if (idBd == 0) 
    {
        alert('Debe seleccionar la base de datos de destino.')
    }
    else
    {
        var lastIdx = null;
        window.parent.$("#tinterfacedestinoselect").DataTable().ajax.url("http://"+location.host+"/datosInterfaceDestino?idBd="+idBd+"&tabla="+tabla+"&idCampo="+idCampo+"&nombreCampo="+nombreCampo).load();
         // Abrir modal
        window.parent.$("#modalInterfaceDestino").modal()

        $("a.toggle-vis").on( "click", function (e) {
            e.preventDefault();
     
            // Get the column API object
            var column = table.column( $(this).attr("data-column") );
     
            // Toggle the visibility
            column.visible( ! column.visible() );
        } );

        window.parent.$("#tinterfacedestinoselect tbody").on( "mouseover", "td", function () 
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
        window.parent.$("#tinterfacedestinoselect tfoot th").each( function () 
        {
            var title = window.parent.$("#tinterfacedestinoselect thead th").eq( $(this).index() ).text();
            $(this).html( "<input type='text' placeholder='Buscar por "+title+"'/>" );
        });
     
        // DataTable
        var table = window.parent.$("#tinterfacedestinoselect").DataTable();
     
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

        $('#tinterfacedestinoselect tbody').on("dblclick", "tr", function ()
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
                llenarDatos(datos[0][0], datos[0][1], datos[0][2], id);
            }
            window.parent.$("#modalInterfaceDestino").modal("hide");
        });
    }
}

function llenarDatos(nombre, id, tabla, registro)
{
    switch(tabla) 
    {
        case "Documento":
            reg = registro.replace("botonDocumento","");
            $("#documentoDestinoTrasladoDocumentoDetalle"+reg).val(nombre);
            $("#Documento_idDestino"+reg).val(id);
            break;

        case "DocumentoConcepto":
            reg = registro.replace("botonConceptoDestino","");
            $("#documentoConceptoDestinoTrasladoDocumentoDetalle"+reg).val(nombre);
            $("#DocumentoConcepto_idDestino"+reg).val(id);
            break;

        case "Tercero":
            reg = registro.replace("botonTerceroDestino","");
            $("#terceroDestinoTrasladoDocumentoDetalle"+reg).val(nombre);
            $("#Tercero_idDestino"+reg).val(id);
            break;
    }     
}

function llenarDestinoMasivo(tipo, valor)
{
    if (tipo == "Documento") 
    {
        if (valor == 0) 
        {
            for (var i = 0; i < traslado.contador; i++) 
            {
                $("#Documento_idDestino"+i).val(0)
                $("#documentoDestinoTrasladoDocumentoDetalle"+i).val('')
            }
        }
        else
        {
            for (var i = 0; i < traslado.contador; i++) 
            {
                $("#Documento_idDestino"+i).val($( "#documentoDestino option:selected" ).val())
                $("#documentoDestinoTrasladoDocumentoDetalle"+i).val($( "#documentoDestino option:selected" ).text())
            }
        }
    }

    else if (tipo == "Concepto") 
    {
        if (valor == 0) 
        {
            for (var i = 0; i < traslado.contador; i++) 
            {
                $("#DocumentoConcepto_idDestino"+i).val(0)
                $("#documentoConceptoDestinoTrasladoDocumentoDetalle"+i).val('')
            }
        }
        else
        {
            for (var i = 0; i < traslado.contador; i++) 
            {
                $("#DocumentoConcepto_idDestino"+i).val($( "#conceptoDestino option:selected" ).val())
                $("#documentoConceptoDestinoTrasladoDocumentoDetalle"+i).val($( "#conceptoDestino option:selected" ).text())
            }
        }
    }

    else if (tipo == "Tercero") 
    {
        if (valor == 0) 
        {
            for (var i = 0; i < traslado.contador; i++) 
            {
                $("#Tercero_idDestino"+i).val(0)
                $("#terceroDestinoTrasladoDocumentoDetalle"+i).val('')
            }
        }
        else
        {
            for (var i = 0; i < traslado.contador; i++) 
            {
                $("#Tercero_idDestino"+i).val($( "#terceroDestino option:selected" ).val())
                $("#terceroDestinoTrasladoDocumentoDetalle"+i).val($( "#terceroDestino option:selected" ).text())
            }
        }
    }
}
