function validarCampos(event)
{
   var mensaje = ''
   if($("#tituloEncuesta").val() == '')
    {
        $("#tituloEncuesta").css('border-bottom','solid 2px red');
        mensaje += 'Debe digitar el título de la encuesta<br>';
    }
    else
    {
        $("#tituloEncuesta").css('border-bottom','solid 2px gray');
    }

   // recorremos cada una de los LI de las preguntas verificando su informacion
    $(".Pregunta").each(function (index) 
    { 
        var mensajePreg = '';
        // si la pregunta esta vacia
        if($("#preguntaEncuestaPregunta"+index).val() == '')
        {
            $("#preguntaEncuestaPregunta"+index).css('border-bottom','solid 2px red');
            mensajePreg += 'Debe digitar la pregunta<br>';
        }
        else
        {
            $("#preguntaEncuestaPregunta"+index).css('border-bottom','solid 2px gray');
        }


        // si el tipo de respuesta esta vacio
        if($("#tipoRespuestaEncuestaPregunta"+index).val() == '')
        {
            $("#tipoRespuestaEncuestaPregunta"+index).css('border-bottom','solid 2px red');
            mensajePreg += 'Debe seleccionar el tipo de respuesta<br>';
        }
        else
        {
            $("#tipoRespuestaEncuestaPregunta"+index).css('border-bottom','solid 2px gray');
        }

        // si el tipo de respuesta es multiregistro, validamos que ingresen por lo menos 
        // un registro de opciones con su valor y titulo
        var multiregistro = ['Selección Múltiple','Casillas de Verificación','Lista de Opciones'];
        if(multiregistro.indexOf($("#tipoRespuestaEncuestaPregunta"+index).val()) >= 0) 
        {
            $('.divOpcion'+index ).each(function (reg) 
            {
                console.log(index+'_'+reg+' / '+$("#valorEncuestaOpcion"+index+'_'+reg).val()+' - '+$("#nombreEncuestaOpcion"+index+'_'+reg).val());
                if($("#valorEncuestaOpcion"+index+'_'+reg).val() == '' || $("#nombreEncuestaOpcion"+index+'_'+reg).val() == '')
                {
                    $("#valorEncuestaOpcion"+index+'_'+reg).css('border','solid 1px red');
                    $("#nombreEncuestaOpcion"+index+'_'+reg).css('border','solid 1px red');
                    mensajePreg += "\t"+'Debe digitar el valor y el nombre de la opción '+(reg+1)+'<br>';
                }
                else
                {
                    $("#valorEncuestaOpcion"+index+'_'+reg).css('border','solid 1px gray');
                    $("#nombreEncuestaOpcion"+index+'_'+reg).css('border','solid 1px gray');
                }

            });
        }
        if(mensajePreg != '')
            mensaje += 'Pregunta No. '+(index+1)+'<br>' + mensajePreg;


    }) ;

    if(mensaje != '')
    {
        $("#msj").html(mensaje);
        $("#msj-error").css("display","block");
    
        event.preventDefault();
    }
}


function validarFormulario(event)
{
    var token = $("#token").val();
    var dato0 = document.getElementById('idEncuesta').value;
    var dato1 = document.getElementById('tituloEncuesta').value;
    var dato2 = document.getElementById('descripcionEncuesta').value;
    var datoPregunta = document.querySelectorAll("[name='preguntaEncuestaPregunta[]']");
    var dato3 = [];
    
    var valor = '';
    var sw = true;
    
    for(var j=0,i=datoPregunta.length; j<i;j++)
    {
        dato3[j] = datoPregunta[j].value;
    }

    $.ajax({
        async: false,
        url: "http://"+location.host+"/encuesta",
        headers: {'X-CSRF-TOKEN': token},
        type: 'POST',
        dataType: 'json',
        data: {respuesta: 'falso',
                idEncuesta: dato0,
                tituloEncuesta: dato1,
                descripcionEncuesta: dato2,
                datoPregunta: dato3
                },

        success:function(){
            // $("#msj-success").fadeIn();
            // console.log(' sin errores');
        },
        error:function(msj){

            var mensaje = '';
            var respuesta = JSON.stringify(msj.responseJSON); 

            console.log(respuesta);
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

                (typeof msj.responseJSON.tituloEncuesta === "undefined" 
                    ? document.getElementById('tituloEncuesta').style.borderColor = '' 
                    : document.getElementById('tituloEncuesta').style.borderColor = '#a94442');

                (typeof msj.responseJSON.descripcionEncuesta === "undefined" 
                    ? document.getElementById('descripcionEncuesta').style.borderColor = '' 
                    : document.getElementById('descripcionEncuesta').style.borderColor = '#a94442');

                for(var j=0,i=datoPregunta.length; j<i;j++)
                {
                    (typeof respuesta['preguntaEncuestaPregunta'+j] === "undefined" 
                        ? document.getElementById('preguntaEncuestaPregunta'+j).style.borderColor = '' 
                        : document.getElementById('preguntaEncuestaPregunta'+j).style.borderColor = '#a94442');
                }

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

function abrirModalRol()
{
    $('#ModalRoles').modal('show');

}

var TituloMulti = function(titnombreObjeto, titnombreContenedor, titnombreDiv){

    this.nombre = titnombreObjeto;
    this.contenedor = titnombreContenedor;
    this.contenido = titnombreDiv;
    this.contador = 0;
    this.texto = new Array();
    this.estilo = new Array();
    this.clase = new Array();
};

TituloMulti.prototype.agregarTituloMulti = function(grupo, nombreGrupo){

    // cada que adicione titulos del detalle, los creamos dentro del div detalles
    // y el nombre de cada div, va a set el nombre del contenedor + id del grupo de preguntas
    // este mismo div, nos va a servir para adicionar los div de titulos y los div de datos
    var espacio = document.getElementById(this.contenedor);
    
    var divpadre = document.createElement('div');
    divpadre.id = this.contenedor+grupo;
    divpadre.setAttribute("class", 'row show-grid col-sm-12');
    divpadre.setAttribute("style", 'margin-bottom: 0px;');
    espacio.appendChild(divpadre);

    var espacio = document.getElementById(this.contenedor+grupo);
    
    var div = document.createElement('div');
    div.id = this.contenido+this.contador;
    div.setAttribute("width", '100%');

    for (var i = 0,  e = this.texto.length; i < e ; i++)
    {
    
            var label = document.createElement('div');
            label.setAttribute("class", this.clase[i] );
            label.setAttribute("style", this.estilo[i]);
            label.innerHTML = this.texto[i];
            div.appendChild(label);
    }

    espacio.appendChild(div);
    this.contador++;
}

var RegistroMulti = function(nombreObjeto, nombreContenedor, nombreDiv){
    this.altura = '35px;';
    this.campoid = '';
    this.campoEliminacion = '';
    this.botonEliminacion = true;
    
    this.nombre = nombreObjeto;
    this.contenedor = nombreContenedor;
    this.contenido = nombreDiv;
    this.contador = Array();
    this.campos = new Array();
    this.etiqueta = new Array();
    this.tipo = new Array();
    this.estilo = new Array();
    this.clase = new Array();
    this.sololectura = new Array();
    this.completar = new Array();
    this.etiqueta = new Array();
    this.opciones = new Array();
    this.funciones = new Array();
    this.nombreOpcion = new Array();
    this.valorOpcion = new Array();
    this.eventoclick = new Array();

};

RegistroMulti.prototype.agregarCampos = function(datos, tipo, pos){


    if(!this.contador[pos])
    {

        this.contador[pos] = 0;
        console.log('no existe pos '+pos+' = '+this.contador[pos]);
    }
    else
        console.log('SI existe pos '+pos+' = '+this.contador[pos]);

    var valor;
    if(tipo == 'A')
       valor = datos;
    else
        valor = $.parseJSON(datos);
    
    var espacio = document.getElementById('divMulti_pregunta'+pos);
   
    
    var div = document.createElement('div');
    div.id = this.contenido+pos+'_'+this.contador[pos];
    div.setAttribute("class", "col-sm-12 divOpcion"+pos);
    div.setAttribute("style",  "height:"+this.altura+"margin: 0px 0px 0px 0px; padding: 0px 0px 0px 0px;");
    
    // si esta habilitado el parametro de eliminacion de registros del detalle, adicionamos la caneca
    if(this.botonEliminacion)
    {
        var img = document.createElement('i');
        var caneca = document.createElement('div');
        caneca.id = 'eliminarRegistro'+ this.contador[pos];
        caneca.setAttribute('onclick',this.nombre+'.borrarOpcion(\''+div.id+'\',\''+this.campoEliminacion+'\',\''+this.campoid+ pos+'_'+this.contador+'\')');
        caneca.setAttribute("class","col-md-1");
        caneca.setAttribute("style","width:40px; height:35px; cursor:pointer;");
        img.setAttribute("class","glyphicon glyphicon-trash");

        caneca.appendChild(img);
        div.appendChild(caneca);
    }


    for (var i = 0,  e = this.campos.length; i < e ; i++)
    {
        if(this.etiqueta[i] == 'input')
        {
            var input = document.createElement('input');
            input.type =  this.tipo[i];
            input.id =  this.campos[i] + pos +'_' + this.contador[pos];
            input.name =  this.campos[i]+'['+pos+']'+'[]';

            input.value = (typeof(valor[(tipo == 'A' ? i : this.campos[i])]) !== "undefined" ? valor[(tipo == 'A' ? i : this.campos[i])] : '');
            input.setAttribute("class", this.clase[i]);
            input.setAttribute("style", this.estilo[i]);
            input.readOnly = this.sololectura[i];
            input.autocomplete = this.completar[i];
            if(typeof(this.funciones[i]) !== "undefined") 
            {
                for(var h=0,c = this.funciones[i].length;h<c;h+=2) 
                {
                    input.setAttribute(this.funciones[i][h], this.funciones[i][h+1]);
                }
            }

            div.appendChild(input);

        }
    }

    
    espacio.appendChild(div);

    this.contador[pos]++;

}

RegistroMulti.prototype.borrarOpcion = function(elemento, campoEliminacion, campoid){
   
    if(campoEliminacion && document.getElementById(campoEliminacion) && document.getElementById(campoid))
        document.getElementById(campoEliminacion).value += document.getElementById(campoid).value + ',';
    
    $("#"+elemento).remove();

}

RegistroMulti.prototype.borrarTodosOpciones = function(){
    
    
    for (var posborrar = 0 ; posborrar < this.contador; posborrar++) 
    {
        this.borrarOpcion(this.contenido+posborrar, this.campoEliminacion, this.campoid+this.contador);
    }
    this.contador = 0;
}

var Propiedades = function(nombreObjeto, nombreContenedor, nombreDiv){
    this.altura = '150px;';
    this.campoid = '';
    this.campoEliminacion = '';
    this.botonEliminacion = true;
    

    this.nombre = nombreObjeto;
    this.contenedor = nombreContenedor;
    this.contenido = nombreDiv;
    this.contador = 0;

};

Propiedades.prototype.agregarPregunta = function(datos, tipo){

    var valor;
    if(tipo == 'A')
       valor = datos;
    else
        valor = $.parseJSON(datos);
    
    var espacio = document.getElementById(this.contenedor);
   
  
    //----------------
    //********************************************
    // Campos Ordenables
    //********************************************

    // ul.sortable({
    //   placeholder: "ui-state-highlight"
    // });
    // ul.disableSelection();

    var li = document.createElement('li');
    li.id = 'li_'+this.contenido+this.contador;
    li.setAttribute("class", "col-sm-12 Pregunta");
    li.setAttribute("style", "");


    //********************************************
    // Boton eliminar
    //********************************************
    var img = document.createElement('i');
    var caneca = document.createElement('div');
    caneca.id = 'eliminarRegistro'+ this.contador;
    caneca.setAttribute('onclick',this.nombre+'.borrarCampos(\''+li.id+'\',\''+this.campoEliminacion+'\',\''+this.campoid+this.contador+'\')');
    caneca.setAttribute("class","col-md-1");
    caneca.setAttribute("style","width:40px; height:25px;");
    img.setAttribute("class","glyphicon glyphicon-trash");

    caneca.appendChild(img);
    li.appendChild(caneca);


    //********************************************
    // Lista de seleccion de tipo de respuesta
    //********************************************
    var divSelect = document.createElement('div');
    divSelect.id = 'div_'+this.contenido+this.contador;
    divSelect.setAttribute("class", "col-sm-3");
    divSelect.setAttribute("style", "float:right;");
    li.appendChild(divSelect);

    var select = document.createElement('select');
    var option = '';
    select.id =  'tipoRespuestaEncuestaPregunta' + this.contador;
    select.name =  'tipoRespuestaEncuestaPregunta[]';
    select.placeholder =  'Tipo de Respuesta...';
    select.setAttribute("style", '');
    select.setAttribute("class", 'Encuesta-Tipo');
    select.setAttribute("onchange", 'cambiarTipoPregunta(this.value,"'+this.contenido+'","'+this.contador+'")');

    option = document.createElement('option');
    option.value = '';
    option.text = 'Seleccione Tipo de Respuesta...';
    select.appendChild(option);

    //,'Escala Lineal'
    opciones =  [
                    ['Respuesta Corta', 'Párrafo','Selección Múltiple','Casillas de Verificación','Lista de Opciones','Fecha', 'Hora'],
                    ['Respuesta Corta', 'Párrafo','Selección Múltiple','Casillas de Verificación','Lista de Opciones','Fecha', 'Hora']
                ];


    // creamos un array con las opciones y las recorremos adicionandolas
    for(var j=0,k=opciones.length;j<k;j+=2)
    {
        for(var p=0,l = opciones[j].length;p<l;p++)
        {
            option = document.createElement('option');
            option.value = opciones[j][p];
            option.text = opciones[j+1][p];
            //option.setAttribute('style','background-image:url(../images/division.png);');

            option.selected = (valor['tipoRespuestaEncuestaPregunta'] == opciones[j][p] ? true : false);
            select.appendChild(option);
        }    
    }

    divSelect.appendChild(select);

    //********************************************
    // Campo ID de Pregunta (OCULTO)
    //********************************************
    var input0 = document.createElement('input');
    input0.type =  'hidden';
    input0.id =  'idEncuestaPregunta' + this.contador;
    input0.value =  (typeof(valor['idEncuestaPregunta']) !== "undefined" ? valor['idEncuestaPregunta'] : '');
    input0.name =  'idEncuestaPregunta[]';

    li.appendChild(input0);

    //********************************************
    // Campo de Pregunta
    //********************************************
    var input1 = document.createElement('input');
    input1.type =  'text';
    input1.id =  'preguntaEncuestaPregunta' + this.contador;
    input1.name =  'preguntaEncuestaPregunta[]';
    input1.value =  (typeof(valor['preguntaEncuestaPregunta']) !== "undefined" ? valor['preguntaEncuestaPregunta'] : '');
    input1.placeholder =  'Pregunta...';
    input1.setAttribute("class", 'Encuesta-Titulo');
    input1.setAttribute("style", '');
    input1.readOnly = false;
    input1.autocomplete = false;
    
    li.appendChild(input1);

    //********************************************
    // Campo de Descripción de Pregunta
    //********************************************
    var input2 = document.createElement('input');
    input2.type =  'text';
    input2.id =  'detalleEncuestaPregunta' + this.contador;
    input2.name =  'detalleEncuestaPregunta[]';
    input2.value =  (typeof(valor['detalleEncuestaPregunta']) !== "undefined" ? valor['detalleEncuestaPregunta'] : '');
    input2.placeholder =  'Detalles de la Pregunta...';
    input2.setAttribute("class", 'Encuesta-Subtitulo');
    input2.setAttribute("style", '');
    input2.readOnly = false;
    input2.autocomplete = false;

    li.appendChild(input2);
    
   
    //********************************************
    // Campo de Respuesta unica
    //********************************************
    var divRespCampo = document.createElement('div');
    divRespCampo.id = 'divCampo_'+this.contenido+this.contador;
    divRespCampo.setAttribute("class", "col-sm-12");
    divRespCampo.setAttribute("style", "display:none;");
    li.appendChild(divRespCampo);

    // adicionamos un campo solo de nuestra para el usuario, de como sería su respuesta
    var input3 = document.createElement('input');
    input3.type =  'text';
    input3.id =  'CampoRespuesta' + this.contador;
    input3.name =  'CampoRespuesta[]';
    input3.placeholder =  'Respuesta Corta';
    input3.setAttribute("class", 'Encuesta-Respuesta');
    input3.setAttribute("style", '');
    input3.readOnly = true;
    input3.autocomplete = false;

    divRespCampo.appendChild(input3);
    

    //********************************************
    // Campo de respuesta multiple (oculto)
    //********************************************
    var divRespMulti = document.createElement('div');
    divRespMulti.id = 'divMulti_'+this.contenido+this.contador;
    divRespMulti.setAttribute("class", "col-sm-12");
    divRespMulti.setAttribute("style", "display:none; height: 150px; overflow: auto;");
    li.appendChild(divRespMulti);



    // creamos los titulos del detalle por cada grupo de preguntas
    opcionTitulos = new TituloMulti('pregunta_opcion', divRespMulti.id,'pregunta_opcion');
    opcionTitulos.texto   = ['<a onclick="opcionPregunta.agregarCampos([0,\'\',\'\'],\'A\',\''+this.contador+'\');" ><span class="fa fa-plus"></span></a>','Valor', 'Opción'];
    opcionTitulos.estilo   = ['width: 40px;', 'width: 100px;', 'width: 400px;'];
    opcionTitulos.clase   = ['col-md-1','col-md-1','col-md-1'];

    opcionPregunta = new RegistroMulti('opcionPregunta',divRespMulti.id,'opcionPregunta');
    opcionPregunta.contador[this.contador] = 0;

    opcionPregunta.altura = '25px;';
    opcionPregunta.campoid = 'idEncuestaOpcion';
    opcionPregunta.campoEliminacion = 'eliminarOpcion';

    opcionPregunta.campos = ['idEncuestaOpcion', 'valorEncuestaOpcion', 'nombreEncuestaOpcion'];
    opcionPregunta.etiqueta = ['input', 'input','input'];
    opcionPregunta.tipo = ['hidden', 'text','text'];
    opcionPregunta.estilo = ['', 'width: 100px;height:25px;','width: 400px;height:25px;'];
    opcionPregunta.clase = ['', '',''];
    opcionPregunta.sololectura = [false, false,false];
    opcionPregunta.completar = ['off', 'off','off'];
    opcionPregunta.opciones = ['', '',''];
    opcionPregunta.funciones  = ['', '',''];
    
    espacio.appendChild(li);

    opcionTitulos.agregarTituloMulti(0, 'Lista de Opciones');


    this.contador++;
    $('#totalPreguntas').val(this.contador);

    var config = {
      '.chosen-select'           : {},
      '.chosen-select-deselect'  : {allow_single_deselect:true},
      '.chosen-select-no-single' : {disable_search_threshold:10},
      '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
      '.chosen-select-width'     : {width:"95%"}
    }
    for (var selector in config) {
      $(selector).chosen(config[selector]);
    }

}

Propiedades.prototype.borrarCampos = function(elemento, campoEliminacion, campoid){
   
    if(campoEliminacion && document.getElementById(campoEliminacion) && document.getElementById(campoid))
        document.getElementById(campoEliminacion).value += document.getElementById(campoid).value + ',';

        $("#"+elemento).remove();

}

Propiedades.prototype.borrarTodosCampos = function(){
    
    
    for (var posborrar = 0 ; posborrar < this.contador; posborrar++) 
    {
        this.borrarCampos(this.contenido+posborrar, this.campoEliminacion, this.campoid+this.contador);
    }
    this.contador = 0;
}


function cambiarTipoPregunta(tipo, nombre, reg)
{
    if(tipo == 'Respuesta Corta' || tipo == 'Párrafo' || tipo == 'Fecha' || tipo == 'Hora')
    {
        $("#CampoRespuesta"+reg).prop('placeholder', tipo);
        $("#divCampo_"+nombre+reg).css('display', 'block');
        $("#divMulti_"+nombre+reg).css('display', 'none');
    }
    else
    {
        // 'Selección Múltiple','Casillas de Verificación','Lista de Opciones','Escala Lineal'
        $("#divCampo_"+nombre+reg).css('display', 'none');
        $("#divMulti_"+nombre+reg).css('display', 'block');
    } 
}