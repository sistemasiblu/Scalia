function validarFormulario(event)
{
    var route = "http://"+location.host+"/agenda";
    var token = $("#token").val();
    var dato0 = document.getElementById('idAgenda').value;
    var dato1 = document.getElementById('CategoriaAgenda_idCategoriaAgenda').value;
    var dato2 = document.getElementById('asuntoAgenda').value;
    var dato3 = document.getElementById('fechaHoraInicioAgenda').value;
    var dato4 = document.getElementById('fechaHoraFinAgenda').value;
    var dato5 = document.getElementById('Tercero_idSupervisor').value;
    // var dato6 = document.getElementById('detallesAgenda').value;

    var valor = '';
    var sw = true;

    $.ajax({
        async: false,
        url:route,
        headers: {'X-CSRF-TOKEN': token},
        type: 'POST',
        dataType: 'json',
        data: {respuesta: 'falso',
                idAgenda: dato0,
                CategoriaAgenda_idCategoriaAgenda: dato1,
                asuntoAgenda: dato2,
                fechaHoraInicioAgenda: dato3,
                fechaHoraFinAgenda: dato4,
                Tercero_idSupervisor: dato5
                // detallesAgenda: dato6
                },
        success:function(){
            //$("#msj-success").fadeIn();
            //console.log(' sin errores');
        },
        error:function(msj){
            var mensaje = '';
            var respuesta = JSON.stringify(msj.responseJSON); 
            if(typeof respuesta === "undefined")
            {
                sw = false;
                $("#msj").html('');
                $("#msj-error").fadeOut();
            }
            else
            {
                sw = true;
                respuesta = JSON.parse(respuesta);

                (typeof msj.responseJSON.CategoriaAgenda_idCategoriaAgenda === "undefined" ? document.getElementById('CategoriaAgenda_idCategoriaAgenda').style.borderColor = '' : document.getElementById('CategoriaAgenda_idCategoriaAgenda').style.borderColor = '#a94442');

                (typeof msj.responseJSON.asuntoAgenda === "undefined" ? document.getElementById('asuntoAgenda').style.borderColor = '' : document.getElementById('asuntoAgenda').style.borderColor = '#a94442');

                (typeof msj.responseJSON.fechaHoraInicioAgenda === "undefined" ? document.getElementById('fechaHoraInicioAgenda').style.borderColor = '' : document.getElementById('fechaHoraInicioAgenda').style.borderColor = '#a94442');

                (typeof msj.responseJSON.fechaHoraFinAgenda === "undefined" ? document.getElementById('fechaHoraFinAgenda').style.borderColor = '' : document.getElementById('fechaHoraFinAgenda').style.borderColor = '#a94442');

                (typeof msj.responseJSON.Tercero_idSupervisor === "undefined" ? document.getElementById('Tercero_idSupervisor').style.borderColor = '' : document.getElementById('Tercero_idSupervisor').style.borderColor = '#a94442');

                // (typeof msj.responseJSON.detallesAgenda === "undefined" ? document.getElementById('detallesAgenda').style.borderColor = '' : document.getElementById('detallesAgenda').style.borderColor = '#a94442');

                var mensaje = 'Por favor verifique los siguientes valores <br><ul>';
                $.each(respuesta,function(index, value){
                    mensaje +='<li>' +value+'</li><br>';
                });
                mensaje +='</ul>';
               
                $("#msj").html(mensaje);
                $("#msj-error").fadeIn();
            }

        }
    });

    if(sw === true)
        event.preventDefault();
}

function agregarEvento()
{
    $('#modalEvento').modal('show');
}

function consultarCamposAgenda(idCategoriaAgenda, idAgenda)
{
    var token = document.getElementById('token').value;

    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        dataType: "json",
        data: {'idCategoriaAgenda' : idCategoriaAgenda, 'idAgenda': idAgenda},
        url:   'http://'+location.host+'/mostrarCamposAgenda/',
        type:  'post',
        success: function(respuesta)
        {
            // alert(respuesta.toSource());
            // $("#claseAgenda").val(respuesta[0]['codigoCategoriaAgenda']);

            for (var i = 0; i < respuesta.length; i++) 
            {
                if (respuesta[i]['nombreCampoCRM'] == 'ubicacionAgenda') 
                    $("#ubicacion").css('display','block');

                if (respuesta[i]['nombreCampoCRM'] == 'MovimientoCRM_idMovimientoCRM') 
                    $("#MovimientoCRM").css('display','block');

                if (respuesta[i]['nombreCampoCRM'] == 'Tercero_idResponsable') 
                    $("#Tercero").css('display','block');

                if (respuesta[i]['nombreCampoCRM'] == 'porcentajeEjecucionAgenda') 
                    $("#porcentajeEjecucion").css('display','block');

                if (respuesta[i]['nombreCampoCRM'] == 'estadoAgenda') 
                    $("#estado").css('display','block');

                if (respuesta[i]['nombreCampoCRM'] == 'seguimientoAgenda') 
                    $("#divseguimiento").css('display','block');
                    $("#liseguimiento").css('display','block');

                if (respuesta[i]['nombreCampoCRM'] == 'Tercero_idAsistente') 
                    $("#divasistentes").css('display','block');
                    $("#liasistentes").css('display','block');
            }
        },
        error: function(xhr,err)
        { 
            $("#claseAgenda").val('');

            $("#ubicacionAgenda").css('display','none');

            $("#MovimientoCRM_idMovimientoCRM").css('display','none');

            $("#Tercero_idResponsable").css('display','none');

            $("#porcentajeEjecucionAgenda").css('display','none');

            $("#estadoAgenda").css('display','none');

            $("#liseguimiento").css('display','none');
            
            $("#liasistentes").css('display','none');
        }
    });
}

function consultarTercero(id, value)
{
    alert('id:'+id+' value: '+value);
}

function guardarDatos(){

        var formId = '#agenda';

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
                window.parent.location.replace("http://"+location.host+"/agenda");
                $('#modalEvento').modal('hide');
            },
            error: function(){
                alert('No se pudo guardar el evento.');
            },
        });
}; 

function cancelarCita(idAgenda)
{
    var borrar = confirm("¿Realmente desea cancelar la cita?");
    if (borrar) 
    {
        var token = document.getElementById('token').value;
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            data: {idAgenda: idAgenda},
            url:  'http://'+location.host+'/eliminarAgenda/delete/'+idAgenda,
            type:  'get',
            beforeSend: function(){
                console.log(idAgenda);
                },
            success: function(respuesta){
                alert(respuesta);
                window.parent.location.replace("http://"+location.host+"/agenda");
            },
            error: function(xhr,err)
            { 
                alert("Error");
            }
        });
    }
}

// -----------------------------------
// A G E N D A  S E G U I M I E N T O
// -----------------------------------

var AtributosSeguimiento = function(nombreObjeto, nombreContenedor, nombreDiv){
    this.alto = '100px;';
    this.ancho = '100%;';
    this.campoid = 'idFichaTecnicaSeguimiento';
    this.campoEliminacion = 'eliminarSeguimiento';
    this.botonEliminacion = true;

    this.nombre = nombreObjeto;
    this.contenedor = nombreContenedor;
    this.contenido = nombreDiv;
    this.contador = 0;
};

AtributosSeguimiento.prototype.agregarSeguimiento = function(datos, tipo){

    var valor;
    if(tipo == 'A')
       valor = datos;
    else
        valor = $.parseJSON(datos);
    
    var espacio = document.getElementById(this.contenedor);
   
    var div = document.createElement('div');
    div.id = this.contenido+this.contador;
    div.setAttribute("class", "col-sm-12");
    div.setAttribute("style",  "overflow: auto; background: transparent; height:"+this.alto+"width:"+this.ancho+";margin: 3px 3px 3px 3px; padding: 2px 2px 2px 2px;");
    
    // si esta habilitado el parametro de eliminacion de registros del detalle, adicionamos la caneca
    if(this.botonEliminacion && tipo == 'A')
    {
        var img = document.createElement('i');
        var caneca = document.createElement('div');
        caneca.id = 'eliminarRegistro'+ this.contador;
        caneca.setAttribute('onclick',this.nombre+'.borrarCampos(\''+div.id+'\',\''+this.campoEliminacion+'\',\''+this.campoid+this.contador+'\')');
        caneca.setAttribute("class","canecaSeguimiento col-md-1");
        caneca.setAttribute("style","width:40px; height:35px;");
        img.setAttribute("class","glyphicon glyphicon-trash");

        caneca.appendChild(img);
        div.appendChild(caneca);
    }

    
    //--------------------
    // id de seguimiento
    //--------------------
    var input = document.createElement('input');
    input.type =  "hidden";
    input.id =  "idAgendaSeguimiento" + this.contador;
    input.name =  "idAgendaSeguimiento[]";
    input.value = valor[(tipo == 'A' ? 0 : "idAgendaSeguimiento")] ;
    input.setAttribute("class", "");
    input.readOnly = "";
    input.autocomplete = "false";
    div.appendChild(input);

    //--------------------
    // id de usuario
    //--------------------
    var input = document.createElement('input');
    input.type =  "hidden";
    input.id =  "Users_idCrea" + this.contador;
    input.name =  "Users_idCrea[]";
    input.value = valor[(tipo == 'A' ? 1 : "Users_idCrea")] ;
    input.setAttribute("class", "");
    input.readOnly = "";
    input.autocomplete = "false";
    div.appendChild(input);

    //--------------------
    // id de agenda
    //--------------------
    var input = document.createElement('input');
    input.type =  "hidden";
    input.id =  "Agenda_idAgenda" + this.contador;
    input.name =  "Agenda_idAgenda[]";
    input.value = valor[(tipo == 'A' ? 2 : "Agenda_idAgenda")] ;
    input.setAttribute("class", "");
    input.readOnly = "";
    input.autocomplete = "false";
    div.appendChild(input);

    //--------------------
    // Fecha de seguimiento
    //--------------------
    var input = document.createElement('input');
    input.type =  "text";
    input.id =  "fechaHoraAgendaSeguimiento" + this.contador;
    input.name =  "fechaHoraAgendaSeguimiento[]";
    input.value = valor[(tipo == 'A' ? 3 : "fechaHoraAgendaSeguimiento")] ;
    input.setAttribute("class", "fechaNota");
    input.setAttribute("style","width:150px; height:35px;");
    input.readOnly = "readOnly";
    input.autocomplete = "false";
    div.appendChild(input);

    //--------------------
    // Detalles de seguimiento
    //--------------------
    var input = document.createElement('input');
    input.type =  "text";
    input.id =  "detallesAgendaSeguimiento" + this.contador;
    input.name =  "detallesAgendaSeguimiento[]";
    input.value = valor[(tipo == 'A' ? 4 : "detallesAgendaSeguimiento")] ;
    input.setAttribute("class", "");
    input.setAttribute("style","width:310px; height:35px;");
    input.readOnly = "";
    input.autocomplete = "false";
    div.appendChild(input);
 
       
    espacio.appendChild(div);

    this.contador++;
}

AtributosSeguimiento.prototype.borrarCampos = function(elemento, campoEliminacion, campoid){
   
    if(campoEliminacion && document.getElementById(campoEliminacion) && document.getElementById(campoid))
        document.getElementById(campoEliminacion).value += document.getElementById(campoid).value + ',';

    // aux = elemento.parentNode;
    // alert(aux);
    // if(aux );
        $("#"+elemento).remove();

}

AtributosSeguimiento.prototype.borrarTodosCampos = function(){
    
    
    for (var posborrar = 0 ; posborrar < this.contador; posborrar++) 
    {
        this.borrarCampos(this.contenido+posborrar, this.campoEliminacion, this.campoid+this.contador);
    }
    this.contador = 0;
}
