
//Obtener ip del servidor
var ip = ((location.href.split('/'))[0])+'//'+((location.href.split('/'))[2]);


// $(document).ready( function () {
// var lastIdx = null;
//     var table = window.parent.$("#tlistaselect").DataTable( {
//         "order": [[ 1, "asc" ]],
//         "aProcessing": true,
//         "aServerSide": true,
//         "paging": false,
//         "searching": false,
//         "retrieve": true,
//         "stateSave":true,
//         "ajax": ip+"/datosListaSelect",
//         "language": {
//                     "sProcessing":     "Procesando...",
//                     "sLengthMenu":     "Mostrar _MENU_ registros",
//                     "sZeroRecords":    "No se encontraron resultados",
//                     "sEmptyTable":     "Ning&uacute;n dato disponible en esta tabla",
//                     "sInfo":           "Registros del _START_ al _END_ de un total de _TOTAL_ ",
//                     "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
//                     "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
//                     "sInfoPostFix":    "",
//                     "sSearch":         "Buscar:",
//                     "sUrl":            "",
//                     "sInfoThousands":  ",",
//                     "sLoadingRecords": "Cargando...",
//                     "oPaginate": {
//                         "sFirst":    "Primero",
//                         "sLast":     "&Uacute;ltimo",
//                         "sNext":     "Siguiente",
//                         "sPrevious": "Anterior"
//                     },
//                     "oAria": {
//                         "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
//                         "sSortDescending": ": Activar para ordenar la columna de manera descendente"
//                     }
//                 }
//     });
             
//             $("a.toggle-vis").on( "click", function (e) {
//                 e.preventDefault();
         
//                 // Get the column API object
//                 var column = table.column( $(this).attr("data-column") );
         
//                 // Toggle the visibility
//                 column.visible( ! column.visible() );
//             } );

//             window.parent.$("#tlistaselect tbody")
//             .on( "mouseover", "td", function () {
//                 var colIdx = table.cell(this).index().column;
     
//                 if ( colIdx !== lastIdx ) {
//                     $( table.cells().nodes() ).removeClass( "highlight" );
//                     $( table.column( colIdx ).nodes() ).addClass( "highlight" );
//                 }
//             } )
//             .on( "mouseleave", function () {
//                 $( table.cells().nodes() ).removeClass( "highlight" );
//             } );


//             // Setup - add a text input to each footer cell
        

//         window.parent.$("#tlistaselect tfoot th").each( function () {
//             var title = window.parent.$("#tlistaselect thead th").eq( $(this).index() ).text();
//             $(this).html( "<input type='text' placeholder='Buscar por "+title+"'/>" );
//         } );
     
//         // DataTable
//         var table = window.parent.$("#tlistaselect").DataTable();
     
//         // Apply the search
//         table.columns().every( function () {
//             var that = this;
     
//             $( "input", this.footer() ).on( "blur change", function () {
//                 if ( that.search() !== this.value ) {
//                     that
//                         .search( this.value )
//                         .draw();
//                 }
//             } );
//         })

//          window.parent.$("#tlistaselect tbody").on( "click", "tr", function () {
//             if ( $(this).hasClass("selected") ) {
//                 $(this).removeClass("selected");
//             }
//             else {
//                 table.$("tr.selected").removeClass("selected");
//                 $(this).addClass("selected");
//             }
//         } );
// });

function cuerpoGridFormulario(Documento_idDocumento)
{
    $('#formularioTr').attr('src',ip+'/gridFormulario?idDoc=' + Documento_idDocumento);
}

function divFormulario()
{
	document.getElementById('formulario').style.display = "block";
    document.getElementById('tipoFormulario').value = 'formulario';
}

function armarMetadatosFormulario()
{	
    var idDocumento = document.getElementById('idDocumentoF').value;

	var token = document.getElementById('token').value;
	$.ajax({
                headers: {'X-CSRF-TOKEN': token},
                dataType: "json",
                data: {'Documento_idDocumento': idDocumento},
                url:   ip+'/armarMetadatosFormulario/',
                type:  'post',
                beforeSend: function(){
                    //Lo que se hace antes de enviar el formulario
                    },
                success: function(respuesta){
                    //lo que se si el destino devuelve algo
                    $("#metadatosFormulario").html(respuesta);
                    document.getElementById("versionInicialFormulario").value = document.getElementById("numeroVersionInicial").value;                    
                },
                error:    function(xhr,err){ 
                    alert("Error");
                }
            });
}


function guardarDatosFormulario(){

        var formId = '#radicado';

        var token = document.getElementById('token').value;
        $.ajax({
            async: true,
            headers: {'X-CSRF-TOKEN': token},
            url: $(formId).attr('action'),
            type: $(formId).attr('method'),
            data: $(formId).serialize(),
            dataType: 'html',
            success: function(result){
                $(formId)[0].reset();
                alert(result);
                document.getElementById("formulario").style.display = "none";
                parent.formularioTr.location.reload() // Recargar la grid despues de ejecutar el AJAX
            },
            error: function(){
                alert('No se pudo guardar el formulario.');
            }
        });
};

function divVersionFormulario()
{
	document.getElementById('editarVersion').style.display = "block";
    document.getElementById('tipoFormulario').value = 'formularioVersion';
}


function llamarMetadatosFormulario(Radicado_idRadicado, version)
{
    var idDocumento = document.getElementById('idDocumentoF').value;
	var token = document.getElementById('token').value;
	$.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            data: {'Radicado_idRadicado': Radicado_idRadicado, 'version': version, 'idDocumento': idDocumento},
            url:   ip+'/armarMetadatosConsultaFormulario/',
            type:  'post',
            beforeSend: function(){
                },
            success: function(respuesta){
                $("#consultaMetadatosFormulario").html(respuesta.estructura);
                document.getElementById("idRadicadoF").value = document.getElementById("F_Radicado_idRadicado").value;

                var controlVersion = document.getElementById('controlVersionDocumento').value;

                if (controlVersion == 1) 
                {
                    document.getElementById('btn_nuevaVersion').style.display = "block";
                    document.getElementById('actualizarF').style.display = "none";
                }
                else
                {
                    document.getElementById('btn_nuevaVersion').style.display = "none";
                    document.getElementById('actualizarF').style.display = "block";   
                }
            },
            error:    function(xhr,err){
                alert("Error");
            }
        });
}


function actualizarFormulario()
{
    var formId = '#radicado';

    var token = document.getElementById('token').value;
    $.ajax({
        async: true,
        headers: {'X-CSRF-TOKEN': token},
        url: $(formId).attr('action'),
        type: $(formId).attr('method'),
        data: $(formId).serialize(),
        dataType: 'html',
        success: function(result){
            $(formId)[0].reset();
            alert(result);
            document.getElementById("editarVersion").style.display = "none";
            parent.formularioTr.location.reload() // Recargar la grid despues de ejecutar el AJAX
        },
        error: function(){
            alert('No se ha actualizado el formulario.');
        }
    });
};  

function divNuevaVersionFormulario()
{
    document.getElementById('nuevaVersionFormulario').style.display = "block";
    document.getElementById('editarVersion').style.display = "none";
    document.getElementById('tipoFormulario').value = 'formularioNuevaVersion';
}

function llamarMetadatosVersionFormulario(version)
{
    var Radicado_idRadicado = document.getElementById("idRadicadoF").value;
    var idDocumento = document.getElementById('idDocumentoF').value;

    var token = document.getElementById('token').value;
    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            data: {'Radicado_idRadicado': Radicado_idRadicado, 'idDocumento': idDocumento, 'version': version},
            url:   ip+'/armarMetadatosVersionFormulario/',
            type:  'post',
            beforeSend: function(){
                },
            success: function(respuesta){
                $("#consultaMetadatosFormularioNV").html(respuesta.estructura);
                document.getElementById('numeroVersionFormulario').value = document.getElementById('FNV_numeroFormularioVersion').value;
                // $('#FNV_1 option:not(:selected)').attr('disabled',true);
                
            },
            error:    function(xhr,err){
                alert("Error");
            }
        });
}

function cambiarNumeroVersionFormulario(nivelVersion)
{
    var Radicado_idRadicado = document.getElementById("idRadicadoF").value;

    var token = document.getElementById('token').value;
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        dataType: "json",
        data: {'nivelVersion': nivelVersion, 'Radicado_idRadicado': Radicado_idRadicado},
        url:   ip+'/numeroFormularioVersion/',
        type:  'post',
        beforeSend: function(){
            },
        success: function(respuesta){
            document.getElementById('numeroVersionFormulario').value = respuesta;
            },
        error:    function(xhr,err){
            alert("Error");
        }
    });
}

function guardarFormularioNV()
{
    var formId = '#radicado';

    var token = document.getElementById('token').value;
    $.ajax({
        async: true,
        headers: {'X-CSRF-TOKEN': token},
        url: $(formId).attr('action'),
        type: $(formId).attr('method'),
        data: $(formId).serialize(),
        dataType: 'html',
        success: function(result){
            $(formId)[0].reset();
            alert(result);
            document.getElementById('nuevaVersionFormulario').style.display = "none";
            document.getElementById("editarVersion").style.display = "none";
            parent.formularioTr.location.reload() // Recargar la grid despues de ejecutar el AJAX
        },
        error: function(){
            alert('No se ha guardado la nueva versió del formulario.');
        }
    });
}; 

function listarVersiones(Radicado_idRadicado)
{
     var token = document.getElementById('token').value;
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        dataType: "json",
        data: {'Radicado_idRadicado': Radicado_idRadicado},
        url:   ip+'/listarVersiones/',
        type:  'post',
        beforeSend: function(){
            },
        success: function(respuesta){
            $('#versionMaximaFormulario').html(respuesta);
        },
        error:    function(xhr,err){
            alert("Error");
        }
    });
}

function eliminarFormulario(radicado)
{
    var borrar = confirm("¿Realmente desea eliminarlo?");
      if (borrar) 
        {
            var token = document.getElementById('token').value;
            $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                dataType: "json",
                data: {radicado: radicado},
                url:   ip+'/eliminarRadicado/delete/'+radicado,
                type:  'get',
                beforeSend: function(){
                    console.log(radicado);
                    },
                success: function(respuesta){
                    alert(respuesta);
                    parent.formularioTr.location.reload()
                },
                error: function(xhr,err)
                { 
                    alert("Error");
                }
            });
        }
}

function llenarMetadatos(value, tipo, idbd) 
{
    var consulta = document.getElementById('consulta').value;
    var idDocumento = document.getElementById('idDocumentoF').value;

    // var pos = 0;
    // var valores = Array();
    // $('.campoBusqueda').each(function(){        
    //     valores[pos] = document.getElementById($(this).attr('id')).value;
    //     pos ++;
    // });

    var token = document.getElementById('token').value;
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        dataType: "json",
        data: {'consulta': consulta, 'value': value, 'idDocumento': idDocumento, 'tipo': tipo, 'idbd': idbd},
        url:   ip+'/consultaMetadatos/',
        type:  'post',
        success: function(respuesta){
            for(i = 0; i < respuesta.length; i++)
            {
                document.getElementById(tipo+respuesta[i]["campo"]).value = respuesta[i]["valor"];
            }
        },
        error: function(xhr,err){ 
            alert("Índice incorrecto");
        }
    });
}

function validarCheckbox(check,idCheck)
{
    if(check.checked==true)
    {
        document.getElementById(idCheck).value = 1;
    }
    else
    {
        document.getElementById(idCheck).value = 0;   
    }
}

function imprimirFormato(tipo)
{
    var id = document.getElementById('idDocumentoF').value;
    window.open('documento/'+id+'?tipo='+tipo,'Formato','width=5000,height=5000');
}

function abrirModal(idLista,objeto)
{

    var lastIdx = null;
    window.parent.$("#tlistaselect"+idLista).DataTable().ajax.url(ip+"/datosListaSelect?idLista="+idLista+"&value="+objeto.value).load();
    //var lastIdx = null;
     // Abrir modal
    window.parent.$("#ListaSelect"+idLista).modal()

    $("a.toggle-vis").on( "click", function (e) {
        e.preventDefault();
 
        // Get the column API object
        var column = table.column( $(this).attr("data-column") );
 
        // Toggle the visibility
        column.visible( ! column.visible() );
    } );

    window.parent.$("#tlistaselect"+idLista+" tbody").on( "mouseover", "td", function () 
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
    window.parent.$("#tlistaselect"+idLista+" tfoot th").each( function () 
    {
        var title = window.parent.$("#tlistaselect"+idLista+" thead th").eq( $(this).index() ).text();
        $(this).html( "<input type='text' placeholder='Buscar por "+title+"'/>" );
    });
 
    // DataTable
    var table = window.parent.$("#tlistaselect"+idLista).DataTable();
 
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

    window.parent.$("#tlistaselect"+idLista+" tbody").on( "dblclick", "tr", function () 
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
                enviarDatosLista(datos[0][0], datos[0][1], datos[0][2], objeto);        
            }

        window.parent.$("#ListaSelect"+idLista).modal("hide");

    } );

}


function enviarDatosLista(cod,nombre,id,objeto)
{   

    $("input[id='"+objeto.id+"']").each(function() 
        {
            $(this).val(nombre);
        });

    $("input[id='cod"+objeto.id+"']").each(function() 
        {
            $(this).val(id);
        });
    
}
