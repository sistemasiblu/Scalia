
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
        async: false,
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
                $("#resultadoDocumento").html('');
                return;
            }

            $("#idConciliacionComercial").val(respuesta.idConciliacionComercial);
            $("#resultadoDocumento").html(respuesta.tabla);

            clasificartabla();

        },
        error:    function(xhr,err){ 
            alert("Error");
        }
    });  

    // window.open('consultarInformacion/?condicionGeneral='+condicionGeneral+'&condicionDocumento='+condicionDocumento,'_blank','width=2500px, height=700px, scrollbars=yes');
    
}

function clasificartabla()
{

    var lastIdx = null;

    window.parent.$("#tconciliacioncomercialdocumento").DataTable();
     // Abrir modal
    window.parent.$("#ModalValor").modal()

    $("a.toggle-vis").on( "click", function (e) {
        e.preventDefault();
 
        // Get the column API object
        var column = table.column( $(this).attr("data-column") );
 
        // Toggle the visibility
        column.visible( ! column.visible() );
    } );

    window.parent.$("#tconciliacioncomercialdocumento tbody").on( "mouseover", "td", function () 
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
    window.parent.$("#tconciliacioncomercialdocumento tfoot th").each( function () 
    {
        var title = window.parent.$("#tconciliacioncomercialdocumento thead th").eq( $(this).index() ).text();
        $(this).html( "<input type='text' placeholder='Buscar por "+title+"'/>" );
    });
 
    // DataTable
    var table = window.parent.$("#tconciliacioncomercialdocumento").DataTable();
 
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
        async: false,
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

function consultarInformacionDoc(idDoc,tipo)
{     
    var token = $("#token").val();
    $("#ModalResultadoConsulta").modal('show');
    alert('consultar informacion');
    // $.ajax({
    //     async: false,
    //     url:'/ConsultarInformacionConciliacionComercial',
    //     headers: {'X-CSRF-TOKEN': token},
    //     type: 'POST',
    //     dataType: 'json',
    //     data: {
    //         "idDoc": idDoc,
    //         "tipo": tipo
    //     },
    //     beforeSend: function(){
    //         //Lo que se hace antes de enviar el formulario
    //         },
    //     success: function(respuesta){
    //         //lo que se si el destino devuelve algo
            
    //         if(respuesta.valid === true)
    //         {
    //            alert('Se actualizo la observacion.');
    //         }
    //         else
    //         {
    //             alert('Error. No se guardo la observacion.');
    //         }
    //     },
    //     error:    function(xhr,err){ 
    //         alert("Error");
    //     }
    // });      
}

function abrirModalValor(tipo)
{
    $(this).removeClass("selected");
    
    $("#divTabla").html('');

    estructuraTabla = '<table id="tvalorSelect" name="tvalorSelect" class="display table-bordered" width="100%">'+
                          '<thead>'+
                              '<tr class="btn-default active">'+
                                  '<th><b>ID</b></th>'+
                                  '<th><b>Valor</b></th> '+       
                              '</tr>'+
                          '</thead>'+
                          '<tfoot>'+
                              '<tr class="btn-default active">'+

                                  '<th>ID</th>'+
                                  '<th>Valor</th> '+                            
                              '</tr>'+
                          '</tfoot>'+
                      '</table>';

    $("#divTabla").html(estructuraTabla);

	var lastIdx = null;
    window.parent.$("#tvalorSelect").DataTable().ajax.url('http://'+location.host+"/datosValorConciliacionSelect?tipo="+tipo).load();
     // Abrir modal
    window.parent.$("#ModalValor").modal()

    $("a.toggle-vis").on( "click", function (e) {
        e.preventDefault();
 
        // Get the column API object
        var column = table.column( $(this).attr("data-column") );
 
        // Toggle the visibility
        column.visible( ! column.visible() );
    } );

    window.parent.$("#tvalorSelect tbody").on( "mouseover", "td", function () 
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
    window.parent.$("#tvalorSelect tfoot th").each( function () 
    {
        var title = window.parent.$("#tvalorSelect thead th").eq( $(this).index() ).text();
        $(this).html( "<input type='text' placeholder='Buscar por "+title+"'/>" );
    });
 
    // DataTable
    var table = window.parent.$("#tvalorSelect").DataTable();
 
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

    window.parent.$('#tvalorSelect tbody').on( 'click', 'tr', function () {
        $(this).toggleClass('selected');

        var datos = table.rows('.selected').data();


    } );

    window.parent.$('#botonCampo').click(function() {
        var datos = table.rows('.selected').data();  

        for (var i = 0; i < datos.length; i++) 
        {
            var valores = new Array(0, datos[i][0],datos[i][1],'','');

            if(tipo == 'comercial')
            {
                resultCom = false;
                for(cont = 0; cont < comercial.contador; cont++)
                {
                    if($('#ValorConciliacion_idValorConciliacionCom'+cont).val() == datos[i][0])
                    {
                        resultCom = true;
                        cont = comercial.contador;
                    }
                }

                if(resultCom === false)
                {
                    window.parent.comercial.agregarCampos(valores,'A');
                }
            }
            else
            {
                resultCar = false;
                for(cont = 0; cont < cartera.contador; cont++)
                {
                  if($('#ValorConciliacion_idValorConciliacionCar'+cont).val() == datos[i][0])
                  {
                    resultCar = true;
                    cont = cartera.contador;
                  }
                }

                if(resultCar === false)
                {
                    window.parent.cartera.agregarCampos(valores,'A');
                }                  
            }
        }
        window.parent.$("#ModalValor").modal("hide");
    });

}