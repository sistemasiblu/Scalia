//Obtener ip del servidor
var ip = ((location.href.split('/'))[0])+'//'+((location.href.split('/'))[2]);

function abrirModalObservaciones(op)
{
	var token = document.getElementById('token').value;
	$.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            data: {op: op},
            url:   ip+'/consultaObservacionOP/',
            type:  'post',
            beforeSend: function(){
                },
            success: function(respuesta)
            {
            	$('#observacion').html(respuesta);
                
           // CKEDITOR.instances.observacion.SetData(respuesta);
           // CKEDITOR.instances.observacion.updateElement();
            	// CKEDITOR.instances['observacion'].setData(respuesta);
            },
            error: function(xhr,err)
            { 
                alert("Error");
            }
        });

	$('#modalObservacion').modal('show'); 	
}

function actualizarObservacion(contenido)
{
	var op = document.getElementById('numeroOP').value;
	var token = document.getElementById('token').value;
	$.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            data: {op: op, contenido: contenido},
            url:   ip+'/actualizarObservacionOP/',
            type:  'post',
            beforeSend: function(){
                },
            success: function(respuesta)
            {
            	alert(respuesta);
            	$('#modalObservacion').modal('hide');
            	location.reload();
            },
            error: function(xhr,err)
            { 
                alert("Error");
            }
        });
}

function imprimirFormato(valor, tipo)
{
    // si es de tipo movimiento y tiene una coma al final del campo, la quitamos
    if (tipo == 'Movimiento' && valor.substring(valor.length-1) == ',') 
    {
        valor = valor.substring(0,valor.length-1);
    }
    window.open('consultaproduccion/'+valor+'?tipo='+tipo,'_blank','width=2500px, height=700px, scrollbars=yes');
}