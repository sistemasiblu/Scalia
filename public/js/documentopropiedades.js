 function divValidar(reg)
 {
    document.getElementById("validacion").style.display = "block";
    document.getElementById('registro').value = reg.replace('validacionDocumentoPropiedad',"");
 }

 function cerrarValidar()
 {
    document.getElementById("validacion").style.display = "none";
 }

function mostrarModalMetadato()
{
    $("#myModalMetadato").modal();
}

function llenarDatosDocumento(Metadato)
{
    var token = document.getElementById('token').value;
    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            data: {'idMetadato': Metadato.value},
            url:   ip+'/consultarCamposMetadatoDocumento/',
            type:  'post',
            beforeSend: function(){
                },
            success: function(respuesta)
            {
                reg = Metadato.id.replace('Metadato_idMetadato','');

                $('#tituloDocumentoPropiedad'+reg).val(respuesta['tituloMetadato']);
                $('#tipoDocumentoPropiedad'+reg).val(respuesta['tipoMetadato']);
                $('#idListaDocumentoPropiedad'+reg).val(respuesta['idLista']);
                $('#nombreListaDocumentoPropiedad'+reg).val(respuesta['nombreLista']);
                $('#longitudDocumentoPropiedad'+reg).val(respuesta['longitudMetadato']);
                $('#valorBaseDocumentoPropiedad'+reg).val(respuesta['valorBaseMetadato']);
                $('#opcionDocumentoPropiedad'+reg).val(respuesta['opcionMetadato']);
            },
            error: function(xhr,err)
            { 
                alert("Error");
            }
        });
}