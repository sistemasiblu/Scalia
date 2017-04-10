
function abrirModalCampos()
{
    $('#ModalCampos').modal('show');

}


function abrirModalCompania()
{
    $('#myModalCompania').modal('show');

}

function abrirModalRol()
{
    $('#myModalRol').modal('show');

}


function llenarDatosCampo(id, reg)
{
    var token = document.getElementById('token').value;
    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            data: {idCampoCRM: id},
            url:   'http://'+location.host+'/llenarCampo/',
            type:  'post',
            beforeSend: function(){
                },
            success: function(respuesta)
            {
                $('#descripcionCampoCRM'+reg).val(respuesta);
                
            },
            error: function(xhr,err)
            { 
                alert("Error");
            }
        });
}


function llenarDatosCompania(id, reg)
{
    var token = document.getElementById('token').value;
    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            data: {idCompania: id},
            url:   'http://'+location.host+'/llenarCompania/',
            type:  'post',
            beforeSend: function(){
                },
            success: function(respuesta)
            {
                $('#nombreCompania'+reg).val(respuesta);
                
            },
            error: function(xhr,err)
            { 
                alert("Error");
            }
        });
}

function llenarDatosRol(id, reg)
{
    var token = document.getElementById('token').value;
    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            data: {idRol: id},
            url:   'http://'+location.host+'/llenarRol/',
            type:  'post',
            beforeSend: function(){
                },
            success: function(respuesta)
            {
                $('#nombreRol'+reg).val(respuesta);
                
            },
            error: function(xhr,err)
            { 
                alert("Error");
            }
        });
}