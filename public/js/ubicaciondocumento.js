
$(document).ready( function () {

    $("#fechaInicialUbicacionDocumento, #fechaFinalUbicacionDocumento").datetimepicker
    (
        ({
           format: "YYYY-MM-DD"
         })
    );
});

function mostrarCamposTipoUbicacion(tipo)
{
    if(tipo == 'Historias')
    {
        $("#documento").css('display','block');
        $("#nombre").css('display','block');

        $("#descripcion").css('display','none');
        $("#folio").css('display','none');
        $("#fechaInicial").css('display','none');
        $("#fechaFinal").css('display','none');

        $("#descripcionUbicacionDocumento").val('');
        $("#numeroFolioUbicacionDocumento").val('');
        $("#fechaInicialUbicacionDocumento").val('');
        $("#fechaFinalUbicacionDocumento").val('');
    }
    else if(tipo == 'Otros')
    {
        $("#descripcion").css('display','block');
        $("#folio").css('display','block');
        $("#fechaInicial").css('display','block');
        $("#fechaFinal").css('display','block');

        $("#documento").css('display','none');
        $("#nombre").css('display','none');

        $("#Tercero_idTercero").val('');
        $("#documentoTerceroUbicacionDocumento").val('');
        $("#nombreTerceroUbicacionDocumento").val('');
    }
    else
    {
        $("#documento").css('display','none');
        $("#nombre").css('display','none');
        $("#descripcion").css('display','none');
        $("#folio").css('display','none');
        $("#fechaInicial").css('display','none');
        $("#fechaFinal").css('display','none');

        $("#Tercero_idTercero").val('');
        $("#documentoTerceroUbicacionDocumento").val('');
        $("#nombreTerceroUbicacionDocumento").val('');
        $("#descripcionUbicacionDocumento").val('');
        $("#numeroFolioUbicacionDocumento").val('');
        $("#fechaInicialUbicacionDocumento").val('');
        $("#fechaFinalUbicacionDocumento").val('');
    }
}

function guardarDatos()
{
    var formId = '#ubicacion';
    var token = document.getElementById('token').value;
    $.ajax({
        async: true,
        headers: {'X-CSRF-TOKEN': token},
        url: $(formId).attr('action'),
        type: $(formId).attr('method'),
        data: $(formId).serialize(),
        dataType: 'html',
        success: function(result){
            alert(result);
            idIni = result.substring(40, result.length);
            idUbicacion = idIni.substring(0, idIni.length -1);
            window.parent.$("#myModalUbicacion").modal("hide");
            window.parent.window.parent.$("#myModalPL").modal("hide");
            asignarPL(idUbicacion);
            location.reload();
            $(formId)[0].reset();            
        },
        error: function(result){
            alert('No se ha podido guardar la ubicacion.');
        }
    });
}; 

function asignarPL(idUbicacion)
{
    var token = document.getElementById('token').value;

    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        dataType: "json",
        data: {'idUbicacion': idUbicacion},
        url:   'http://'+location.host+'/asignarPLRadicado/',
        type:  'post',
        beforeSend: function(){
            //Lo que se hace antes de enviar el formulario
            },
        success: function(respuesta){
            window.parent.window.parent.document.getElementById("ubicacionEstanteRadicado").value = respuesta[0]['puntoLocalizacion'];
        },
        error:    function(xhr,err){ 
            alert("Error");
        }
    });
}

function llenarCamposUbicacion(idUbicacion)
{
    var token = document.getElementById('token').value;

    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        dataType: "json",
        data: {'idUbicacion': idUbicacion},
        url:   'http://'+location.host+'/llenarCamposUbicacion/',
        type:  'post',
        beforeSend: function(){
            //Lo que se hace antes de enviar el formulario
            },
        success: function(respuesta){
            alert(respuesta.toSource());
            $("#idUbicacionDocumento").val(respuesta['idUbicacionDocumento']);
            $('#tipoUbicacionDocumento option[value='+respuesta['tipoUbicacionDocumento']+']').prop('selected', true);
            $("#DependenciaLocalizacion_idDependenciaLocalizacion").val(respuesta['DependenciaLocalizacion_idDependenciaLocalizacion']);
            $("#posicionUbicacionDocumento").val(respuesta['posicionUbicacionDocumento']);
            $("#numeroLegajoUbicacionDocumento").val(respuesta['numeroLegajoUbicacionDocumento']);
            $("#numeroFolioUbicacionDocumento").val(respuesta['numeroFolioUbicacionDocumento']);
            $("#descripcionUbicacionDocumento").val(respuesta['descripcionUbicacionDocumento']);
            $("#Tercero_idTercero").val(respuesta['Tercero_idTercero']);
            $("#fechaInicialUbicacionDocumento").val(respuesta['fechaInicialUbicacionDocumento']);
            $("#TipoSoporteDocumental_idTipoSoporteDocumental").val(respuesta['idTipoSoporteDocumental']);
            $("#Dependencia_idProductora").val(respuesta['idDependencia']);
            $("#Compania_idCompania").val(respuesta['idCompania']);
            $("#estadoUbicacionDocumento").val(respuesta['estadoUbicacionDocumento']);
            $("#observacionUbicacionDocumento").val(respuesta['observacionUbicacionDocumento']);
        },
        error:    function(xhr,err){ 
            alert("Error");
        }
    });
}

function eliminarDatos(idUbicacionDocumento)
{
    var borrar = confirm("¿Realmente desea cancelar la cita?");
    if (borrar) 
    {
        var token = document.getElementById('token').value;
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            data: {idUbicacionDocumento: idUbicacionDocumento},
            url:  'http://'+location.host+'/eliminarUbicacion/delete/'+idUbicacionDocumento,
            type:  'get',
            beforeSend: function(){
                console.log(idUbicacionDocumento);
                },
            success: function(respuesta){
                alert(respuesta);
                $("#myModalUbicacion").modal("hide");
            },
            error: function(xhr,err)
            { 
                alert("Error");
            }
        });
    }
}

function llenarMetadatos(value) 
{
    var token = document.getElementById('token').value;
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        dataType: "json",
        data: {'value': value},
        url:   'http://'+location.host+'/consultaMetadatosUbicacion/',
        type:  'post',
        success: function(respuesta){
            if (respuesta == '') 
            {
                alert('No se han encontrado datos.');
                $("#Tercero_idTercero").val('');
                $("#nombreTerceroUbicacionDocumento").val('');
            }
            else
            {
                $("#Tercero_idTercero").val(respuesta[0]['idTercero']);
                $("#nombreTerceroUbicacionDocumento").val(respuesta[0]['nombre1Tercero']);
            }
        },
        error: function(xhr,err){ 
            alert("Error");
        }
    });
}