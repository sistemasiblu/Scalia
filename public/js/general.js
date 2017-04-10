var Titulos = function(titnombreObjeto, titnombreContenedor, titnombreDiv){

    this.nombre = titnombreObjeto;
    this.contenedor = titnombreContenedor;
    this.contenido = titnombreDiv;
    this.contador = 0;
    this.texto = new Array();
    this.estilo = new Array();
    this.clase = new Array();
};

Titulos.prototype.agregarTitulos = function(grupo, nombreGrupo){

    // cada que adicione titulos del detalle, los creamos dentro del div detalles
    // y el nombre de cada div, va a set el nombre del contenedor + id del grupo de preguntas
    // este mismo div, nos va a servir para adicionar los div de titulos y los div de datos
    var espacio = document.getElementById('detalle');
    var divpadre = document.createElement('div');
    divpadre.id = this.contenedor+grupo;
    divpadre.setAttribute("class", 'row show-grid');
    divpadre.setAttribute("width", '100%');
    espacio.appendChild(divpadre);

    var espacio = document.getElementById(this.contenedor+grupo);
    
    var div = document.createElement('div');
    div.id = this.contenido+this.contador;
    div.setAttribute("width", '100%');
    
    var label = document.createElement('div');
    label.setAttribute("class", this.clase[0] );
    label.setAttribute("style", "width: 1120px;");
    label.innerHTML = nombreGrupo;
    div.appendChild(label);

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


var Atributos = function(nombreObjeto, nombreContenedor, nombreDiv){
    this.altura = '35px;';
    this.campoid = '';
    this.campoEliminacion = '';
    this.botonEliminacion = true;
    
    this.nombre = nombreObjeto;
    this.contenedor = nombreContenedor;
    this.contenido = nombreDiv;
    this.contador = 0;
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

Atributos.prototype.agregarCampos = function(datos, tipo){

    var valor;
    if(tipo == 'A')
       valor = datos;
    else
        valor = $.parseJSON(datos);
    
    var espacio = document.getElementById(this.contenedor);
   
    
    var div = document.createElement('div');
    div.id = this.contenido+this.contador;
    div.setAttribute("class", "col-sm-12");
    div.setAttribute("style",  "height:"+this.altura+"margin: 0px 0px 0px 0px; padding: 0px 0px 0px 0px;");
    
    // si esta habilitado el parametro de eliminacion de registros del detalle, adicionamos la caneca
    if(this.botonEliminacion)
    {
        var img = document.createElement('i');
        var caneca = document.createElement('div');
        caneca.id = 'eliminarRegistro'+ this.contador;
        caneca.setAttribute('onclick',this.nombre+'.borrarCampos(\''+div.id+'\',\''+this.campoEliminacion+'\',\''+this.campoid+this.contador+'\')');
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
            input.id =  this.campos[i] + this.contador;
            input.name =  this.campos[i]+'[]';

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

            // este codigo es despues de que se cree espacio (div contenedor)
            //  $('#fotoInspeccionDetalle0').fileinput({
            //   language: 'es',
            //   uploadUrl: '#',
            //   allowedFileExtensions : ['jpg', 'png','gif'],
            //    initialPreview: 
            //     [               
            //         '<img style="width:60px; height:60px;" src="imagenes/accidente/firmaaccidente_1.png">'
            //     ],
            //   dropZoneTitle: 'Seleccione la imagen',
            //   removeLabel: '',
            //   uploadLabel: '',
            //   browseLabel: '',
            //   uploadClass: "",
            //   uploadLabel: "",
            //   uploadIcon: "",
            // });
        }
        if(this.etiqueta[i] == 'file')
        {
            var input = document.createElement('input');
            input.type =  'file';
            input.id =  this.campos[i] + this.contador;
            input.name =  this.campos[i]+'[]';

            input.filename = '';
            input.setAttribute("class", this.clase[i]);
            input.setAttribute("style", this.estilo[i]);
            input.readOnly = this.sololectura[i];
            if(typeof(this.funciones[i]) !== "undefined") 
            {
                for(var h=0,c = this.funciones[i].length;h<c;h+=2) 
                {
                    input.setAttribute(this.funciones[i][h], this.funciones[i][h+1]);
                }
            }

            div.appendChild(input);

           

        }
        else if(this.etiqueta[i] == 'textarea')
        {
            var input = document.createElement('textarea');
            input.id =  this.campos[i] + this.contador;
            input.name =  this.campos[i]+'[]';

            input.value = valor[(tipo == 'A' ? i : this.campos[i])];
            input.setAttribute("class", this.clase[i]);
            input.setAttribute("style", this.estilo[i]);
            if(this.sololectura[i] === true)
                input.setAttribute("readOnly", "readOnly");

            div.appendChild(input);
        }
        else if(this.etiqueta[i] == 'select')
        {
            var select = document.createElement('select');
            var option = '';
            select.id =  this.campos[i] + this.contador;
            select.name =  this.campos[i]+'[]';
            select.setAttribute("style", this.estilo[i]);
            select.setAttribute("class", this.clase[i]);

            if(typeof(this.funciones[i]) !== "undefined") 
            {
                for(var h=0,c = this.funciones[i].length;h<c;h+=2) 
                {
                    select.setAttribute(this.funciones[i][h], this.funciones[i][h+1]);
                }
            } 

            option = document.createElement('option');
            option.value = '';
            option.text = 'Seleccione...';
            select.appendChild(option);
            
            for(var j=0,k=this.opciones[i].length;j<k;j+=2)
            {
                for(var p=0,l = this.opciones[i][j].length;p<l;p++)
                {
                    option = document.createElement('option');
                    option.value = this.opciones[i][j][p];
                    option.text = this.opciones[i][j+1][p];

                    option.selected = (valor[(tipo == 'A' ? i : this.campos[i])] == this.opciones[i][j][p] ? true : false);
                    select.appendChild(option);
                }    
            }
 
            div.appendChild(select);

 
        }
        else if(this.etiqueta[i] == 'checkbox')
        {
            var divCheck = document.createElement('div');
            divCheck.setAttribute('class',this.clase[i]);
            divCheck.setAttribute('style',this.estilo[i]);
 
            var inputHidden = document.createElement('input');
            inputHidden.type =  'hidden';
            inputHidden.id =  this.campos[i] + this.contador;
            inputHidden.name =  this.campos[i]+'[]';
            inputHidden.value = valor[(tipo == 'A' ? i : this.campos[i])];
 
            divCheck.appendChild(inputHidden);
 
            var input = document.createElement('input');
            input.type = this.tipo[i];
            input.setAttribute('style',this.estilo[i]);
            input.id =  this.campos[i]+'C'+this.contador;
            input.name =  this.campos[i]+'C'+'[]';
            input.checked = (valor[(tipo == 'A' ? i : this.campos[i])] == 1 ? true : false);
            input.setAttribute("onclick", this.nombre+'.cambiarCheckbox("'+this.campos[i]+'",'+this.contador+')');
     
            divCheck.appendChild(input);
 
            div.appendChild(divCheck);

        }
        else if(this.etiqueta[i] == 'firma')
        {
            // conlos campos de firma creamos 
            // un img para mostrar la firma en base64 y desde la vista
            // tambien debe crear un input hidden para 
            // guardar el dato base64 para que el controlador lo guarde
            var firma = document.createElement('img');
            firma.id =  this.campos[i] + this.contador;
            firma.src = (typeof(valor[(tipo == 'A' ? i : this.campos[i])]) !== "undefined" ? valor[(tipo == 'A' ? i : this.campos[i])] : '');
            firma.setAttribute("class", this.clase[i]);
            firma.setAttribute("style", this.estilo[i]);
            firma.setAttribute("onclick", "mostrarFirma("+this.contador+")");
            if(typeof(this.funciones[i]) !== "undefined") 
            {
                for(var h=0,c = this.funciones[i].length;h<c;h+=2) 
                {
                    firma.setAttribute(this.funciones[i][h], this.funciones[i][h+1]);
                }
            }
            div.appendChild(firma);
        }
        else if(this.etiqueta[i] == 'button')
        {
            var button = document.createElement('button');
            button.type =  this.tipo[i];
            button.id =  this.campos[i] + this.contador;
            button.name =  this.campos[i]+'[]';

            button.value = (typeof(valor[(tipo == 'A' ? i : this.campos[i])]) !== "undefined" ? valor[(tipo == 'A' ? i : this.campos[i])] : '');
            button.setAttribute("class", this.clase[i]);
            button.setAttribute("style", this.estilo[i]);
            if(typeof(this.funciones[i]) !== "undefined") 
            {
                for(var h=0,c = this.funciones[i].length;h<c;h+=2) 
                {
                    button.setAttribute(this.funciones[i][h], this.funciones[i][h+1]);
                }
            }

            div.appendChild(button);
        }


    }

    
    espacio.appendChild(div);

    this.contador++;

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

Atributos.prototype.borrarCampos = function(elemento, campoEliminacion, campoid){
   
    if(campoEliminacion && document.getElementById(campoEliminacion) && document.getElementById(campoid))
        document.getElementById(campoEliminacion).value += document.getElementById(campoid).value + ',';

    // aux = elemento.parentNode;
    // alert(aux);
    // if(aux );
        $("#"+elemento).remove();

}

Atributos.prototype.borrarTodosCampos = function(){
    
    
    for (var posborrar = 0 ; posborrar < this.contador; posborrar++) 
    {
        this.borrarCampos(this.contenido+posborrar, this.campoEliminacion, this.campoid+this.contador);
    }
    this.contador = 0;
}

Atributos.prototype.cambiarCheckbox = function(campo, registro)
{
    document.getElementById(campo+registro).value = document.getElementById(campo+"C"+registro).checked ? 1 : 0;
}
