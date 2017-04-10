// var Atributos = function(nombreObjeto, nombreContenedor, nombreDiv){

//     this.nombre = nombreObjeto;
//     this.contenedor = nombreContenedor;
//     this.contenido = nombreDiv;
//     this.contador = 0;
//     this.campos = new Array();
//     this.etiqueta = new Array();
//     this.tipo = new Array();
//     this.estilo = new Array();
//     this.clase = new Array();
//     this.sololectura = new Array();
//     this.eventoclick = new Array();
//     this.valorOpcion = new Array();

// };

// Atributos.prototype.agregarCampos = function(datos, tipo,valorOpcion){ 

//     var valor;
//     if(tipo == 'A')
//        valor = datos;
//     else
//         valor = $.parseJSON(datos);
    
//     var espacio = document.getElementById(this.contenedor);
//     var caneca = document.createElement('div');
//     var img = document.createElement('i');
//     var div = document.createElement('div');
//     div.id = this.contenido+this.contador;
//     div.setAttribute("width", '100%');


//     for (var i = 0,  e = this.campos.length; i < e ; i++)
//     {
//         if  (this.etiqueta[i] == 'input')  
//             {
//                 var input = document.createElement('input');
//                 input.type =  this.tipo[i];
//                 input.id =  this.campos[i] + this.contador;
//                 input.name =  this.campos[i]+'[]';

//                 input.value = valor[(tipo == 'A' ? i : this.campos[i])];
//                 input.setAttribute("class", this.clase[i]);
//                 input.setAttribute("style", this.estilo[i]);

//                 div.appendChild(input);

//             }
//     }
    

//     caneca.id = 'eliminarRegistro'+ this.contador;
//     caneca.setAttribute('onclick',this.nombre+'.borrarCampos('+this.contenido+this.contador+')');
//     caneca.setAttribute("class","col-md-1");
//     caneca.setAttribute("style","width:40px; height: 35px;");
//     img.setAttribute("class","glyphicon glyphicon-trash");
//     caneca.appendChild(img);
//     div.appendChild(caneca);
//     espacio.appendChild(div);

//     this.contador++;

//     var config = {
//       '.chosen-select'           : {},
//       '.chosen-select-deselect'  : {allow_single_deselect:true},
//       '.chosen-select-no-single' : {disable_search_threshold:10},
//       '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
//       '.chosen-select-width'     : {width:"95%"}
//     }
//     for (var selector in config) {
//       $(selector).chosen(config[selector]);
//     }
// }

// Atributos.prototype.borrarCampos = function(elemento){

//     aux = elemento.parentNode;
//     aux.removeChild(elemento);

// }

// Atributos.prototype.cambiarCheckbox = function(campo, registro)
// {
//     //console.log(campo+' ----> '+registro);
//     document.getElementById(campo+registro).value = document.getElementById(campo+"C"+registro).checked ? 1 : 0;
// }
//Obtener ip del servidor
var ip = ((location.href.split('/'))[0])+'//'+((location.href.split('/'))[2]);

function ocultarSistema(elemento) 
{
  if(elemento.value=="1") 
  {
      document.getElementById("sistemainformacion").style.display = "none";
      document.getElementById("lista").style.display = "none";
      document.getElementById("consulta").style.display = "none";
  }
  else
  {
     document.getElementById("sistemainformacion").style.display = "block";
  }
        
}

function ocultarConsulta(elemento) 
{
  if(elemento.value=="1" || elemento.value=="2") 
  {
      document.getElementById("lista").style.display = "block";
      document.getElementById("consulta").style.display = "none";
   }
   else
   {
        if(elemento.value=="3")
        {
           document.getElementById("lista").style.display = "none";
           document.getElementById("consulta").style.display = "block";
        }

        else
        {
            if(elemento.value=="4")
            {
                document.getElementById("lista").style.display = "none";
                document.getElementById("consulta").style.display = "none";
            }  
        }
    }
}

function tipoBoton()
{
    var boton = document.getElementById('boton');
    if (document.getElementById('SistemaInformacion_idSistemaInformacion') != '') 
    {
        boton.className = 'glyphicon glyphicon-plus';
    }
    else
    {
        boton.className = 'glyphicon glyphicon-refresh';   
    }
}

function llenarMultiregistro(idSistema, condicion)
{
    var token = document.getElementById('token').value;
    $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                dataType: "json",
                data: {idSistema: idSistema, condicion: condicion},
                url:   ip+'/llenarDatosMultiregistro/',
                type:  'post',
                beforeSend: function(){
                    },
                success: function(respuesta){
                    alert('respuesta');
                },
                error:    function(xhr,err){ 
                    alert("No se ha llenado la tabla");
                }
            });
}

function consultarTablaVista(idSistema, tablaDocumento)
{
     var token = document.getElementById('token').value;
    $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                dataType: "json",
                data: {idSistema: idSistema},
                url:   ip+'/conexionDocumento/',
                type:  'post',
                beforeSend: function(){
                    },
                success: function(respuesta){
                    var tablas = respuesta[0];
                    var nombreDB = respuesta[1];
                    
                    var select = document.getElementById('tablaLista');
                       
                    select.options.length = 0;
                    var option = '';

                    option = document.createElement('option');
                    option.value = null;
                    option.text = 'Seleccione la tabla';
                    select.appendChild(option);


                    for (var i = 0; i < tablas.length ; i++)
                    {
                        option = document.createElement('option');
                        option.value = tablas[i]["Tables_in_"+nombreDB];
                        option.text = tablas[i]["Tables_in_"+nombreDB];
                        option.selected = (tablaLista ==  tablas[i]["Tables_in_"+nombreDB] ? true : false);
                        select.appendChild(option);                    
                    }
                },
                error:    function(xhr,err){ 
                    alert("No se ha podido conectar a la base de datos");
                }
            });
}


function consultarCampos(idSistema, nombreTabla)
{
     var token = document.getElementById('token').value;
    $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                dataType: "json",
                data: {idSistema: idSistema, nombreTabla: nombreTabla},
                url:   ip+'/conexionDocumentoCampos/',
                type:  'post',
                beforeSend: function(){
                    },
                success: function(respuesta){
                    var select = document.getElementById('select1');
            
                    select.options.length = 0;
                    var option = '';

                    option = document.createElement('option');
                    option.value = '';
                    option.text = 'Seleccione...';
                    option.style =  'width: 150px;'+'height: 20px;';
                    select.appendChild(option);

                    for(var j=0,k=respuesta.length;j<k;j++)
                    {
                        option = document.createElement('option');
                        option.value = respuesta[j]["COLUMN_NAME"];
                        option.text = respuesta[j]["COLUMN_NAME"];
                        option.style =  'width: 150px;'+'height: 18px;';
                        select.appendChild(option);
                    }
                // ******************************************************
                    var select2 = document.getElementById('select2');
            
                    select2.options.length = 0;
                    var option2 = '';

                    option2 = document.createElement('option');
                    option2.value = '';
                    option2.text = 'Seleccione...';
                    option2.style =  'width: 150px;'+'height: 20px;';
                    select2.appendChild(option2);

                    for(var j=0,k=respuesta.length;j<k;j++)
                    {
                        option2 = document.createElement('option');
                        option2.value = respuesta[j]["COLUMN_NAME"];
                        option2.text = respuesta[j]["COLUMN_NAME"];
                        option2.style =  'width: 150px;'+'height: 18px;';
                        select2.appendChild(option2);
                    }

                // ******************************************************

                    var select3 = document.getElementById('select3');
            
                    select3.options.length = 0;
                    var option3 = '';

                    option3 = document.createElement('option');
                    option3.value = '';
                    option3.text = 'Seleccione...';
                    option3.style =  'width: 150px;'+'height: 20px;';
                    select3.appendChild(option3);

                    for(var j=0,k=respuesta.length;j<k;j++)
                    {
                        option3 = document.createElement('option');
                        option3.value = respuesta[j]["COLUMN_NAME"];
                        option3.text = respuesta[j]["COLUMN_NAME"];
                        option3.style =  'width: 150px;'+'height: 18px;';
                        select3.appendChild(option3);
                    }

                // ******************************************************

                    var select4 = document.getElementById('select4');
            
                    select4.options.length = 0;
                    var option4 = '';

                    option4 = document.createElement('option');
                    option4.value = '';
                    option4.text = 'Seleccione...';
                    option4.style =  'width: 150px;'+'height: 20px;';
                    select4.appendChild(option4);

                    for(var j=0,k=respuesta.length;j<k;j++)
                    {
                        option4 = document.createElement('option');
                        option4.value = respuesta[j]["COLUMN_NAME"];
                        option4.text = respuesta[j]["COLUMN_NAME"];
                        option4.style =  'width: 150px;'+'height: 18px;';
                        select4.appendChild(option4);
                    }

                // ******************************************************

                    var select5 = document.getElementById('select5');
            
                    select5.options.length = 0;
                    var option5 = '';

                    option5 = document.createElement('option');
                    option5.value = '';
                    option5.text = 'Seleccione...';
                    option5.style =  'width: 150px;'+'height: 20px;';
                    select5.appendChild(option5);

                    for(var j=0,k=respuesta.length;j<k;j++)
                    {
                        option5 = document.createElement('option');
                        option5.value = respuesta[j]["COLUMN_NAME"];
                        option5.text = respuesta[j]["COLUMN_NAME"];
                        option5.style =  'width: 150px;'+'height: 18px;';
                        select5.appendChild(option5);
                    }
                    
                },
                error:    function(xhr,err){ 
                    alert("No se ha podido conectar a la base de datos");
                }
            });


}
