
var valorDetalle = [0,0,'','',''];

$(document).ready(function(){

    $("#fechaInicialConciliacionComercial, #fechaFinalConciliacionComercial").datetimepicker
    (
        ({
           format: "YYYY-MM-DD"
         })
    );

    if(arrayDocumentos.length > 0)
    {
        $("#Documento_idDocumento").val(arrayDocumentos).trigger("chosen:updated");        
    }

    idConCom = $("#Documento_idDocumento").val();

    if(idConCom > 0)
    {
        consultarInformacion(0,3);
    }

});

function validarProceso()
{
    var token = $("#token").val();

    idConciliacionComercial = $("#idConciliacionComercial").val();
    Documento = $('#Documento_idDocumento').val();
    fechaInicialConciliacionComercial = $('#fechaInicialConciliacionComercial').val();
    fechaFinalConciliacionComercial = $('#fechaFinalConciliacionComercial').val();

    idUsuario = $('#Users_idCrea').val();
    fechaElaboracionConciliacionComercial = $('#fechaElaboracionConciliacionComercial').val();

    condicionDocumento = "";
    condicionFechas = '';
    documentos = "";

    if(Documento != null)
    {
        documentos = Documento;
        condicionDocumento = ' documentoconciliacion.Documento_idDocumento IN('+Documento+') ';
    }

    if(fechaInicialConciliacionComercial != '' && fechaFinalConciliacionComercial != '')
    {
        condicionFechas = ' (fechaElaboracionMovimiento >= "'+fechaInicialConciliacionComercial+'" AND fechaElaboracionMovimiento <= "'+fechaFinalConciliacionComercial+'") ';
    }
    else
    {
        alert('Debe seleccionar el rango de fechas de los documentos a conciliar');
        return;
    }

    $(".loader").css("display","block");
    $('#btnGuardar').html('Procesando...'); 

    $.ajax({
        async: true,
        url:'/guardarConciliacionComercial',
        headers: {'X-CSRF-TOKEN': token},
        type: 'POST',
        dataType: 'json',
        data: {
            "idConciliacionComercial": idConciliacionComercial,
            "condicionFechas": condicionFechas,
            "condicionDocumento": condicionDocumento,
            "documentos": documentos,
            "idUsuario": idUsuario,
            "fechaElaboracionConciliacionComercial": fechaElaboracionConciliacionComercial,
            "fechaInicialConciliacionComercial": fechaInicialConciliacionComercial,
            "fechaFinalConciliacionComercial": fechaFinalConciliacionComercial
        },
        beforeSend: function(){
            //Lo que se hace antes de enviar el formulario
            },
        success: function(respuesta){
            //lo que se si el destino devuelve algo
            $(".loader").css("display","none");
            $('#btnGuardar').html('Conciliar');

            if(respuesta.valid === false)
            {
                $("#idConciliacionComercial").val(0);
                alert(respuesta.informacion);
                $("#resultadoConciliacion").html('');
                return;
            }

            $("#idConciliacionComercial").val(respuesta.idConciliacionComercial);
            $("#resultadoConciliacion").html(respuesta.tabla);

            clasificartabla('tconciliacioncomercialdocumento');

        },
        error:    function(xhr,err){ 
            alert("Error");
        }
    });  

    // window.open('consultarInformacion/?condicionGeneral='+condicionGeneral+'&condicionDocumento='+condicionDocumento,'_blank','width=2500px, height=700px, scrollbars=yes');
    
}

function clasificartabla(nombreTabla)
{

    var lastIdx = null;

    window.parent.$("#"+nombreTabla).DataTable();
    
    $("a.toggle-vis").on( "click", function (e) {
        e.preventDefault();
 
        // Get the column API object
        var column = table.column( $(this).attr("data-column") );
 
        // Toggle the visibility
        column.visible( ! column.visible() );
    } );

    window.parent.$("#"+nombreTabla+" tbody").on( "mouseover", "td", function () 
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
    window.parent.$("#"+nombreTabla+" tfoot th").each( function () 
    {
        var title = window.parent.$("#"+nombreTabla+" thead th").eq( $(this).index() ).text();
        $(this).html( "<input type='text' placeholder='Buscar por "+title+"'/>" );
    });
 
    // DataTable
    var table = window.parent.$("#"+nombreTabla).DataTable();
 
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

}

function guardarObservacion(idDoc,idConCom,observacion,tipo)
{     
    var token = $("#token").val();

    $.ajax({
        async: true,
        url:'/guardarObservacionConciliacionComercial',
        headers: {'X-CSRF-TOKEN': token},
        type: 'POST',
        dataType: 'json',
        data: {
            "idDoc": idDoc,
            "idConCom": idConCom,
            "observacion": observacion,
            "tipo": tipo
        },
        beforeSend: function(){
            //Lo que se hace antes de enviar el formulario
            },
        success: function(respuesta){
            //lo que se si el destino devuelve algo
            
            if(respuesta.valid === true)
            {
               alert('Se actualizo la observacion.');
            }
            else
            {
                alert('Error. No se guardo la observacion.');
            }
        },
        error:    function(xhr,err){ 
            alert("Error");
        }
    });      
}

function consultarInformacion(idDoc,tipo)
{     
    var token = $("#token").val();
    idConCom = $("#idConciliacionComercial").val();

    if(tipo == 1)
    {
        tipoConsulta = 'Documento';
        nombreTabla = 'tconciliacioncomercialmovimiento';
        $("#ModalResultado"+tipoConsulta).modal('show');
    }
    else if(tipo == 2)
    {
        tipoConsulta = 'Movimiento';
        nombreTabla = 'tconciliacioncomercialdetalle';
        $("#ModalResultado"+tipoConsulta).modal('show');
    }
    else if(tipo == 3)
    {
        tipoConsulta = 'Conciliacion';
        nombreTabla = 'tconciliacioncomercialdocumento';
    }

    $.ajax({
        async: true,
        url:'/consultarInformacionConciliacionComercial',
        headers: {'X-CSRF-TOKEN': token},
        type: 'POST',
        dataType: 'json',
        data: {
            "idConCom": idConCom,
            "idDoc": idDoc,
            "tipo": tipo
        },
        beforeSend: function(){
            //Lo que se hace antes de enviar el formulario
            },
        success: function(respuesta){
            //lo que se si el destino devuelve algo
            
            if(respuesta.valid === false)
            {
                alert(respuesta.informacion);
                $("#resultado"+tipoConsulta).html('');
                return;
            }

            $("#resultado"+tipoConsulta).html(respuesta.tabla);

            clasificartabla(nombreTabla);
        },
        error:    function(xhr,err){ 
            alert("Error");
        }
    });      
}
