function eliminarDiv(idDiv)
{
    eliminar=confirm("¿Deseas eliminar este archivo?");
    if (eliminar)
    {
        $("#"+idDiv ).remove();  
        $("#eliminarArchivo").val( $("#eliminarArchivo").val() + idDiv + ",");  
    }
}


function mostrarModalInterface()
{
    $("#ModalImportacion").modal("show");
}


function ejecutarInterface(tipo)
{
    var token = document.getElementById('token').value;
    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            url:   'http://'+location.host+'/'+tipo,
            type:  'post',
            beforeSend: function(){
                $(".loader").css("display","block");
                },
            success: function(respuesta)
            {
                if(respuesta[0] == true)
                {
                    console.log(respuesta[1]);
                    $("#ModalImportacion").modal("hide");
                }
                else
                {
                    $("#reporteError").html(respuesta[1]);
                    $("#ModalErrores").modal("show");
                }
                $(".loader").css("display","none");
            },
            error: function(xhr,err)
            { 
                console.log(xhr);
                alert("Error "+xhr);
                $(".loader").css("display","none");
            }
        });
    $("#dropzoneVentaEDIArchivo .dz-preview").remove();
    $("#dropzoneVentaEDIArchivo .dz-message").html('Seleccione o arrastre los archivos a subir.');
}

function mostrarModalInterface()
{
    $("#ModalImportacion").modal("show");
}

