function activarDiv()
{
    document.getElementById("preview").style.display = "block";
}

//Obtener ip del servidor
var ip = ((location.href.split('/'))[0])+'//'+((location.href.split('/'))[2]);

function llamarPreview(idRadicado,idDoc,version)
{
    // alert(idRadicado);
    // alert(idDoc);
    // alert(version);
	var token = document.getElementById('token').value;
	$.ajax({
		async: true,
        headers: {'X-CSRF-TOKEN': token},
        dataType: "json",
        data: {'Radicado_idRadicado': idRadicado, 'version': version},
        url:   ip+'/llamarPreview',
        type:  'post',
        beforeSend: function(){
            },
        success: function(respuesta)
        {
            var preview = document.getElementById("archivoRadicado").value = respuesta;
            preview = preview.substring(28);
            ext = preview.substring(preview.indexOf('.') + 0);

            if (ext == '.pdf') 
            {
                $('#vistaPrevia').html('<embed width="100%" height="100%" src="'+ preview +'"/>'); 
            }
            else
            {
                $('#vistaPrevia').html('<embed width="100%" src="'+ preview +'"/>');        
            };
        },
        error:  function(xhr,err){ 
            alert("Error");
        }
    });
}

function cuerpoGrid(Documento_idDocumento, condicion)
{
    $('#formulario').attr('src',ip+'/gridMetadatos?idDoc='+Documento_idDocumento+'&consulta='+condicion);
}

function accionArchivo(tipo, radicado)
{
    var archivo = document.getElementById("archivoRadicado").value;
    var radicado = document.getElementById("Radicado_idRadicado").value;

    if (tipo == 'descargar')
    {
        $('#descargar').attr('href',ip+"/download?archivo="+archivo);
    }
    else if (tipo == 'imprimir')
    {
            archivo = archivo.substring(28);
            myWindow=window.open(ip+"/imprimir?archivo="+archivo);
            myWindow.close; 
     }
    else if (tipo == 'eliminar')
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
                parent.formulario.location.reload()
            },
            error: function(xhr,err)
            { 
                alert("Error");
            }
        });
        }
    }
}

function llamarMetadatos(Radicado_idRadicado, version)
{
    var token = document.getElementById('token').value;
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        dataType: "json",
        data: {'Radicado_idRadicado': Radicado_idRadicado, 'version': version},
        url:   ip+'/armarMetadatosConsultaRadicado/',
        type:  'post',
        beforeSend: function(){
            },
        success: function(respuesta){
            $("#metadatos").html(respuesta.estructura);

            var controlVersion = document.getElementById('controlVersion').value;

            if (controlVersion == 1)
            {
                document.getElementById("cargar").style.display = "block";
            }
            else 
            {
                document.getElementById("cargar").style.display = "none";
            }

            // if (document.getElementById('versionMaxima').value  != version) 
            // {
            //     document.getElementById("cargar").style.display = "block";   
            // }
            // else
            // {
            //     document.getElementById("cargar").style.display = "none";
            // }
            document.getElementById("Radicado").value = document.getElementById("Radicado_idRadicado").value;
        },
        error:    function(xhr,err){
            alert("Error");
        }
    });
}

function activarEdit()
{
var controlVersion = document.getElementById('controlVersion').value;
document.getElementById("tipo").value = 'updateVersion';

if (controlVersion == 1)
{
    document.getElementById("enviarEdit").style.display = "block";
    document.getElementById("Dependencia_idDependencia").disabled = false;
    document.getElementById("Serie_idSerie").disabled = false;
    document.getElementById("SubSerie_idSubSerie").disabled = false;
    var valores = document.getElementById("campos").value.substring(0,document.getElementById("campos").value.length-1);
    var valores = valores.split(',');
}
else
{
    document.getElementById("enviarEdit").style.display = "block";
    document.getElementById("Dependencia_idDependencia").disabled = false;
    document.getElementById("Serie_idSerie").disabled = false;
    document.getElementById("SubSerie_idSubSerie").disabled = false;
    var valores = document.getElementById("campos").value.substring(0,document.getElementById("campos").value.length-1);
    var valores = valores.split(',');

    for (var i = 0; i < valores.length ; i++) 
    {
        switch(document.getElementById(valores[i]).type) 
        {
            case 'text':
            document.getElementById(valores[i]).readOnly = false;
            case 'number':
            document.getElementById(valores[i]).readOnly = false;
                break;
            case 'select-one':
            document.getElementById(valores[i]).disabled = false;
                break;
            case 'textarea':
            document.getElementById(valores[i]).readOnly = false;
                break;
            case 'checkbox':
            // for (var j = 0; j < .length; j++)
            // {
                document.getElementById(valores[i]).disabled = false;
            // }
                break;
            case 'radio':
                document.getElementById(valores[j]).disabled = false;
                break;
            default:
                 alert('default');
        } 
    }
}

}

function buscarDependencia(idDependencia, idDocumento){

var token = document.getElementById('token').value;
$.ajax({
        async: true,
        headers: {'X-CSRF-TOKEN': token},
        url: ip+'/radicado/'+idDependencia,
        type: 'POST',
        dataType: 'JSON',
        method: 'GET',
        data: {dependenciaClasificacionDocumental: idDependencia,
                Documento: idDocumento},
        success: function(data){
            var valoresd = data[0];      
            var select = document.getElementById('Serie_idSerie');
            select.options.length = 0;
            var option = '';

            option = document.createElement('option');
            option.value = '';
            option.text = 'Seleccione la serie';
            select.appendChild(option);
            
            for(var j=0,k=valoresd.length;j<k;j++)
            {
                option = document.createElement('option');
                option.value = valoresd[j].idSerie;
                option.text = valoresd[j].nombreSerie;
                select.appendChild(option);
            }
        }
    });
}



function buscarSubSerie(idSerie, idDocumento){

var token = document.getElementById('token').value;

$.ajax({
        async: true,
        headers: {'X-CSRF-TOKEN': token},
        url: ip+'/radicado/'+idSerie,
        type: 'POST',
        dataType: 'JSON',
        method: 'GET',
        data: {Serie_idSerie: idSerie,
        Documento: idDocumento},
        success: function(data){
            var valores = data[0];
                      
            var select = document.getElementById('SubSerie_idSubSerie');
           
            select.options.length = 0;
            var option = '';

            option = document.createElement('option');
            option.value = '';
            option.text = 'Seleccione la sub serie';
            select.appendChild(option);
            
            for(var j=0,k=valores.length;j<k;j++)
            {
                option = document.createElement('option');
                option.value = valores[j].idSubSerie;
                option.text = valores[j].nombreSubSerie;
                select.appendChild(option);
            }
        }
    });
}


function actualizarDatos()
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
            document.getElementById("preview").style.display = "none";
            parent.formulario.location.reload() // Recargar la grid despues de ejecutar el AJAX
        },
        error: function(){
            alert('No se ha actualizado el documento.');
        }
    });
};  

function mostrarModalEtiquetaConsulta()
{
    $('#myModalEtiqueta').modal('show');
}  

function etiquetaSelect(ids,nombetiqueta)
{
    document.getElementById("etiquetaRadicado").value=ids;
    document.getElementById("nombreEtiqueta").value=nombetiqueta;
}

function activarEmail()
{
    document.getElementById("email").style.display = "block";   
}

function cerrarEmail()
{
    document.getElementById("email").style.display = "none";
}

function enviarMail(correo, asunto, mensaje, adjunto)
{
    var token = document.getElementById('token').value;
    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            data: {correo: correo, asunto: asunto, mensaje: mensaje, adjunto: adjunto},
            url:   ip+'/enviarEmail/',
            type:  'post',
            beforeSend: function(){
                },
            success: function(respuesta){
                alert(respuesta);
                document.getElementById("correo").value = '';
                document.getElementById("asunto").value = '';
                document.getElementById("mensaje").value = '';
                document.getElementById("adjunto").value = '';
                document.getElementById("email").style.display = "none";
            },
            error:    function(xhr,err){ 
                alert("Error");
            }
        });
}

function accionArchivoMasivo(tipo, radicado)
{
switch(tipo)
{
    case 'descargarMasivo':
        var token = document.getElementById('token').value;
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            data: {'Radicado_idRadicado': radicado},
            url:   ip+'/descargaMasiva/',
            type:  'post',
            beforeSend: function(){

                },
            success: function(respuesta){

                 location.href = ip+"/download?archivo="+respuesta;             
            },
            error: function(xhr,err){
                alert("Seleccione un documento");
            }
        });
    break;

    case 'enviarMasivo':
        var token = document.getElementById('token').value;
        $.ajax({
            async: true,
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            data: {'Radicado_idRadicado': radicado},
            url:   ip+'/emailMasivo',
            type:  'post',
            beforeSend: function(){
                },
            success: function(respuesta)
            {
                for (var i = 0; i < respuesta.length ; i++)
                 {  
                    document.getElementById("archivoRadicado").value += respuesta[i]['archivoRadicado']+'|';
                 }
                document.getElementById("archivoRadicado").value = document.getElementById("archivoRadicado").value.substring(0,document.getElementById("archivoRadicado").value.length-1);
            },
            error:  function(xhr,err){ 
                alert("Error");
            }
        });
    break;

   case 'imprimirMasivo':
    var token = document.getElementById('token').value;
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            data: {'Radicado_idRadicado': radicado},
            url:   ip+'/impresionMasiva/',
            type:  'post',
            beforeSend: function(){

                },
            success: function(respuesta){
            for (var i = 0; i < respuesta.length ; i++)
             {
                // respuesta = respuesta.substring(30);
                myWindow = window.open(ip+"/imprimir?archivo="+respuesta[i]["archivoRadicado"]);
                myWindow.close; 
             }
             
            },
            error: function(xhr,err){
                alert("Error");
            }
        });
    break;
    
    case 'eliminarMasivo':
    var borrar = confirm("¿Realmente desea eliminarlo?");
      if (borrar) 
      {
        var token = document.getElementById('token').value;
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            data: {'Radicado_idRadicado': radicado},
            url:   ip+'/eliminarMasivo',
            type:  'post',
            beforeSend: function(){
                },
            success: function(respuesta){
                alert(respuesta);
                parent.formulario.location.reload() 
                // Recargar la grid despues de ejecutar el AJAX
            },
            error: function(xhr,err){ 
                alert("Error");
            }
        });
      }
    break;
    }
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


function activarDivVersion()
{
    document.getElementById("version").style.display = "block";
    document.getElementById("tipo").value = 'storeVersion';
}

function llamarMetadatosVersion(version)
{
    var Radicado_idRadicado = document.getElementById("Radicado_idRadicado").value;
    var idDocumento = document.getElementById('idDocumento').value;

    var token = document.getElementById('token').value;
    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            data: {'Radicado_idRadicado': Radicado_idRadicado, 'idDocumento': idDocumento, 'version': version},
            url:   ip+'/armarMetadatosVersion/',
            type:  'post',
            beforeSend: function(){
                },
            success: function(respuesta){
                $("#divVersion").html(respuesta.estructura);
                document.getElementById('numeroVersion').value = document.getElementById('numeroRadicadoVersion').value;                    
                $('#V_Dependencia_idDependencia option:not(:selected)').attr('disabled',true);
                $('#V_Serie_idSerie option:not(:selected)').attr('disabled',true);
                $('#V_SubSerie_idSubSerie option:not(:selected)').attr('disabled',true);
                
            },
            error:    function(xhr,err){
                alert("Error");
            }
        });
}

function llenarMetadatos(value, tipo) 
{
    var consulta = document.getElementById('consulta').value;
    var idDocumento = document.getElementById('idDocumento').value;

    var token = document.getElementById('token').value;
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        dataType: "json",
        data: {'consulta': consulta, 'value': value, 'idDocumento': idDocumento, 'tipo': tipo},
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

function cambiarNumeroVersion(nivelVersion)
{
    var Radicado_idRadicado = document.getElementById("Radicado_idRadicado").value;

    var token = document.getElementById('token').value;
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        dataType: "json",
        data: {'nivelVersion': nivelVersion, 'Radicado_idRadicado': Radicado_idRadicado},
        url:   ip+'/numeroRadicadoVersion/',
        type:  'post',
        beforeSend: function(){
            },
        success: function(respuesta){
            document.getElementById('numeroVersion').value = respuesta;
            document.getElementById('numeroVersionGuardar').value = respuesta;
            },
        error:    function(xhr,err){
            alert("Error");
        }
    });
}


function guardarNuevaVersion()
{
    var formId = '#radicado';

    var token = document.getElementById('token').value;
    $.ajax({
        async: true,
        headers: {'X-CSRF-TOKEN': token},
        url:  "{{url ( 'storeVersion')}}",
        type:  'POST',
        data: $(formId).serialize(),
        dataType: 'html',
        success: function(result){
            $(formId)[0].reset();
            alert(result);
            document.getElementById("version").style.display = "none";
            document.getElementById("preview").style.display = "none";
            parent.formulario.location.reload() // Recargar la grid despues de ejecutar el AJAX
        },
        error: function(){
            alert('No se ha actualizado el documento.');
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
            $('#versionRadicadoMaxima').html(respuesta);
            },
        error:    function(xhr,err){
            alert("Error");
        }
    });
}