//Saber ip del servidor
var ip = ((location.href.split('/'))[0])+'//'+((location.href.split('/'))[2]);

function asignarIdDependenciaSerie(idDependencia, idSerie)
{
    $("#Dependencia_idDependencia").val(idDependencia);
    $("#Serie_idSerie").val(idSerie);
    $("#SubSerie_idSubSerie").val('');
}

function asignarIdSubSerie(idSubSerie)
{
    $("#SubSerie_idSubSerie").val(idSubSerie);
    $("#Documento_idDocumento").val('');
}

function radicar(file, idDrop)
{
    // $('#openModal').modal('show');
    // $('#myModalRadicado').css('display','block');

    idDocumento = idDrop.substring(idDrop.indexOf("_")+1); //Le quito el nombre hasta el guion bajo para que me quede solo el numero (id)
    $("#Documento_idDocumento").val(idDocumento);
    
    // $('#pestanas').append('<input type="hidden" name="idDropzone" id="idDropzone" value=""/>');

    // $('#idDropzone').val(idDrop);


    if(file != '')
    {
        PreviewImage(file); //Vista previa en tamaño mayor
        document.getElementById("archivoRadicado").value = file["name"]; //Envio el nombre del archivo   
    }
    else
    {
      document.getElementById("archivoRadicado").value = ''; 
    }           
        

    document.getElementById('registro').value = idDocumento.substring( 0, idDrop.indexOf("_")); //envio el id del registro
    var token = document.getElementById('token').value;

    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            data: {'idDocumento': idDocumento},
            url:   ip+'/armarMetadatosDocumento/',
            type:  'post',
            beforeSend: function(){
                //Lo que se hace antes de enviar el formulario
                },
            success: function(respuesta){
                //lo que se si el destino devuelve algo
                $("#divMetadatos").html(respuesta);
            },
            error:    function(xhr,err){ 
                alert("Error");
            }
        });
}

function PreviewImage(archivo) 
{
    pdffile=archivo;
    pdffile_url=URL.createObjectURL(pdffile);
    $('#viewer').attr('src',pdffile_url);
}

function mostrarModalPL()
{
    $('#myModalPL').modal('show');
}

function mostrarModalEtiqueta()
{
    $('#myModalEtiqueta').modal('show');

}

function etiquetaSelect(ids,nombetiqueta)
{
document.getElementById('etiquetaRadicado').value=ids;
document.getElementById('nombreEtiqueta').value=nombetiqueta;
}

function asignarCheck(valor)
{
    if (valor == 'izquierda') 
        $('input[name=ubicacionEtiquetaRadicado]:checked').val('izquierda');
    else
        $('input[name=ubicacionEtiquetaRadicado]:checked').val('derecha');

}


function limpiarDivPreview() 
{
    // document.getElementById('preview').innerHTML='';
}

// function buscarDependencia(idDependencia)
// {

//     var token = document.getElementById('token').value;

//     $.ajax({
//         async: true,
//         headers: {'X-CSRF-TOKEN': token},
//         url: ip+'/radicado/'+idDependencia,
//         type: 'POST',
//         dataType: 'JSON',
//         method: 'GET',
//         data: {dependenciaClasificacionDocumental: idDependencia},
//         success: function(data){
//             var valoresd = data[0];      
//             var select = document.getElementById('Serie_idSerie');
            
//             select.options.length = 0;
//             var option = '';

//             option = document.createElement('option');
//             option.value = '';
//             option.text = 'Seleccione la serie';
//             select.appendChild(option);
            
//             for(var j=0,k=valoresd.length;j<k;j++)
//             {
//                 option = document.createElement('option');
//                 option.value = valoresd[j].idSerie;
//                 option.text = valoresd[j].nombreSerie;
//                 select.appendChild(option);
//             }
//         }
//     });
// }



// function buscarSubSerie(idSerie){

//     var token = document.getElementById('token').value;

//     $.ajax({
//             async: true,
//             headers: {'X-CSRF-TOKEN': token},
//             url: ip+'/radicado/'+idSerie,
//             type: 'POST',
//             dataType: 'JSON',
//             method: 'GET',
//             data: {Serie_idSerie: idSerie},
//             success: function(data){
//                 var valores = data[0];
                          
//                 var select = document.getElementById('SubSerie_idSubSerie');
               
//                 select.options.length = 0;
//                 var option = '';

//                 option = document.createElement('option');
//                 option.value = '';
//                 option.text = 'Seleccione la sub serie';
//                 select.appendChild(option);
                
//                 for(var j=0,k=valores.length;j<k;j++)
//                 {
//                     option = document.createElement('option');
//                     option.value = valores[j].idSubSerie;
//                     option.text = valores[j].nombreSubSerie;
//                     select.appendChild(option);
//                 }
//             }
//         });
// }

function guardarDatos(){

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
                ubicacion = $("#ubicacionEstanteRadicado").val();
                $(formId)[0].reset();
                alert(result);
                $("#myModalRadicado").modal("hide");
                // imprimirRadicado(result, ubicacion);
            },
            error: function(){
                alert('No se pudo guardar el documento.');
            }
        });
};  

function imprimirRadicado(codigoRadicado, ubicacion)
{
    fechaCreacion = $("#fechaRadicado").val();
    etiqueta = $('input[name=ubicacionEtiquetaRadicado]:checked').val();
    // alert(etiqueta);

    window.open('http://'+location.host+'/radicar/?codigoRadicado='+codigoRadicado+'&fecha='+fechaCreacion+"&ubicacion="+ubicacion+"&etiqueta="+etiqueta+"&accion=radicar",'_blank','width=400px, height=400px, scrollbars=yes');
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


function llenarMetadatos(value) 
{
    var campos = document.getElementById('campos').value;
    var tablaDocumento = document.getElementById('tablaDocumento').value;
    var condicion = document.getElementById('condicion').value;
    var idDocumento = document.getElementById('idDocumento').value;

    var token = document.getElementById('token').value;
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        dataType: "json",
        data: {'campos': campos, 'value': value, 'idDocumento': idDocumento, 'tablaDocumento': tablaDocumento, 'condicion': condicion},
        url:   ip+'/consultaMetadatos/',
        type:  'post',
        success: function(respuesta){
            if (respuesta == '') 
            {
                alert('No se han encontrado datos.');
            }
            else
            {
                for(i = 0; i < respuesta.length; i++)
                {
                    document.getElementById(respuesta[i]["campo"]).value = respuesta[i]["valor"];
                }
            }
        },
        error: function(xhr,err){ 
            alert("Error");
        }
    });
}

$(document).ready(function(){
  $('#preview').BootSideMenu({side:"right"});
});


function irArriba()
{
    $('body, html').animate({
        scrollTop: '0px'
    }, 300);
}