// var AtributosValidacion = function(nombreObjeto, nombreContenedor, nombreDiv){

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
//     this.etiqueta = new Array();
//     this.eventoclick = new Array();
//     this.eventochange = new Array();

//     this.nombreOpcion = new Array();
//     this.valorOpcion = new Array();


// };

// AtributosValidacion.prototype.agregarCamposValidacion = function(datos, tipo){

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

//         if(this.etiqueta[i] == 'input')
//         {
//             var input = document.createElement('input');
//             input.type =  this.tipo[i];
//             input.id =  this.campos[i] + this.contador;
//             input.name =  this.campos[i]+'[]';

//             input.value = valor[(tipo == 'A' ? i : this.campos[i])];
//             input.setAttribute("class", this.clase[i]);
//             input.setAttribute("style", this.estilo[i]);

//             div.appendChild(input);
//         }
//         else if(this.etiqueta[i] == 'select')
//         {

//             var select = document.createElement('select');
//             var option = '';
//             select.id =  this.campos[i] + this.contador;
//             select.name =  this.campos[i]+'[]';
//             select.setAttribute("style", this.estilo[i]);
//             //select.setAttribute("class", this.clase[i]);
            
//             for(var j=0,k=this.valorOpcion.length;j<k;j++)
//             {
//                 option = document.createElement('option');
//                 option.value = this.valorOpcion[j];
//                 option.text = this.nombreOpcion[j];

//                 option.selected = (valor[(tipo == 'A' ? i : this.campos[i])] == this.valorOpcion[j] ? true : false);
//                 select.appendChild(option);
//             }
            
//             div.appendChild(select);
//         }
//     }
//     caneca.id = 'eliminarRegistro'+ this.contador;
//     caneca.setAttribute('onclick',this.nombre+'.borrarCamposValidacion('+this.contenido+this.contador+')');
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

// AtributosValidacion.prototype.borrarCamposValidacion = function(elemento)
// {

//     aux = elemento.parentNode;
//     aux.removeChild(elemento);

// }

// AtributosValidacion.prototype.cambiarCheckboxValidacion = function(campo, registro)
// {
//     //console.log(campo+' ----> '+registro);
//     document.getElementById(campo+registro).value = document.getElementById(campo+"C"+registro).checked ? 1 : 0;
// }

function concatenarValidacion(reg)
{
    var concatenado = '';
    for (var i = 0; i < validacion.contador; i++)
    {
        concatenado +=
        document.getElementById('validacionDocumentoValidacion'+i).value+':' + document.getElementById('valorDocumentoValidacion'+i).value+'|';
    }

    document.getElementById('validacionDocumentoPropiedad'+reg).value += concatenado;

    
    document.getElementById("validacion").style.display = "none";
}

function borrarConcatenado(reg)
{
    document.getElementById("validacionDocumentoPropiedad"+reg).value = document.getElementById("validacionDocumentoPropiedad"+reg).value = '';
    document.getElementById("validacion").style.display = "none";
}
