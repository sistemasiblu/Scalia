function llamarCaracteristicas(id) 
{
var token = document.getElementById('token').value;
$.ajax(
{
    headers: {'X-CSRF-TOKEN': token},
    dataType: "json",
    url:'/llamarCaracteristica',
    data:{idTipoActivo: id},
    type:  'get',
    beforeSend: function(){},
    success: function(data)
    {
        caracteristicaactivo.borrarTodosCampos();
        for (var i = 0;  i <= data.length; i++) 
        {
            caracteristicaactivo.agregarCampos(JSON.stringify(data[i]),'L');
        }
    },
           

    error:    function(xhr,err)
    {
        alert('Se ha producido un error: ' +err);
    }
});
};

function llamarDocumentos(id) 
{
var token = document.getElementById('token').value;
$.ajax(
{
    headers: {'X-CSRF-TOKEN': token},
    dataType: "json",
    url:'/llamarDocumento',
    data:{idTipoActivo: id},
    type:  'get',
    beforeSend: function(){},
    success: function(data)
    {
        documentoactivo.borrarTodosCampos();
        for (var i = 0;  i <= data.length; i++) 
        {
            documentoactivo.agregarCampos(JSON.stringify(data[i]),'L');
        }
    },
    error:    function(xhr,err)
    {
        alert('Se ha producido un error: ' +err);
    }
});
};