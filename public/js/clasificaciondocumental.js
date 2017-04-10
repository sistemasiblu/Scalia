var Atributos = function(nombreObjeto, nombreContenedor, nombreDiv){

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
    this.etiqueta = new Array();
    this.nombreSerie = new Array();
    this.idSerie = new Array();
    this.nombreSubSerie = new Array();
    this.idSubSerie = new Array();
    this.nombreRetencion = new Array();
    this.idRetencion = new Array();
    this.valorEstado = new Array();
    this.nombreEstado = new Array();
    this.eventoclick = new Array();

};

Atributos.prototype.agregarCampos = function(datos, tipo, idSerie){

    var valor;
    if(tipo == 'A')
       valor = datos;
    else
        valor = $.parseJSON(datos);
    
    var espacio = document.getElementById(this.contenedor);
    var caneca = document.createElement('div');
    var img = document.createElement('i');
    var div = document.createElement('div');
    div.id = this.contenido+this.contador;
    div.setAttribute("width", '100%');

    for (var i = 0,  e = this.campos.length; i < e ; i++)
    {

        if(this.etiqueta[i] == 'input')
        {
            var input = document.createElement('input');
            input.type =  this.tipo[i];
            input.id =  this.campos[i] + this.contador;
            input.name =  this.campos[i]+'[]';

            input.value = valor[(tipo == 'A' ? i : this.campos[i])];
            input.setAttribute("class", this.clase[i]);
            input.setAttribute("style", this.estilo[i]);
            input.setAttribute("onclick", this.eventoclick[i]);
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

            div.appendChild(input);
        }
        else if(this.etiqueta[i] == 'select1')
        {

            var select = document.createElement('select');
            var option = '';
            select.id =  this.campos[i] + this.contador;
            select.name =  this.campos[i]+'[]';
            select.setAttribute("style", this.estilo[i]);
            select.setAttribute("class", this.clase[i]);
            
             
            for(var j=0,k=this.idSerie.length;j<k;j++)
            {
                option = document.createElement('option');
                option.value = this.idSerie[j];
                option.text = this.nombreSerie[j];

                option.selected = (valor[(tipo == 'A' ? i : this.campos[i])] == this.idSerie[j] ? true : false);
                select.appendChild(option);
            }
 
            div.appendChild(select);

 
        }
        else if(this.etiqueta[i] == 'select2')
        {

            var select = document.createElement('select');
            var option = '';
            select.id =  this.campos[i] + this.contador;
            select.name =  this.campos[i]+'[]';
            select.setAttribute("style", this.estilo[i]);
            select.setAttribute("class", this.clase[i]);
            
             
            for(var j=0,k=this.idSubSerie.length;j<k;j++)
            {
                option = document.createElement('option');
                option.value = this.idSubSerie[j];
                option.text = this.nombreSubSerie[j];

                option.selected = (valor[(tipo == 'A' ? i : this.campos[i])] == this.idSubSerie[j] ? true : false);
                select.appendChild(option);
            }
 
            div.appendChild(select);

 
        }
        else if(this.etiqueta[i] == 'select3')
        {

            var select = document.createElement('select');
            var option = '';
            select.id =  this.campos[i] + this.contador;
            select.name =  this.campos[i]+'[]';
            select.setAttribute("style", this.estilo[i]);
            select.setAttribute("class", this.clase[i]);
            
             
            for(var j=0,k=this.idRetencion.length;j<k;j++)
            {
                option = document.createElement('option');
                option.value = this.idRetencion[j];
                option.text = this.anioRetencion[j];

                option.selected = (valor[(tipo == 'A' ? i : this.campos[i])] == this.idRetencion[j] ? true : false);
                select.appendChild(option);
            }
 
            div.appendChild(select);

 
        }

        else if(this.etiqueta[i] == 'select4')
        {

            var select = document.createElement('select');
            var option = '';
            select.id =  this.campos[i] + this.contador;
            select.name =  this.campos[i]+'[]';
            select.setAttribute("style", this.estilo[i]);
            select.setAttribute("class", this.clase[i]);
            
             
            for(var j=0,k=this.valorEstado.length;j<k;j++)
            {
                option = document.createElement('option');
                option.value = this.nombreEstado[j];
                option.text = this.valorEstado[j];

                option.selected = (valor[(tipo == 'A' ? i : this.campos[i])] == this.valorEstado[j] ? true : false);
                select.appendChild(option);
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
            inputHidden.value = valor[i];
 
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

    }

    caneca.id = 'eliminarRegistro'+ this.contador;
    caneca.setAttribute('onclick',this.nombre+'.borrarCampos('+this.contenido+this.contador+')');
    caneca.setAttribute("class","col-md-1");
    caneca.setAttribute("style","width:40px; height: 34px;");
    img.setAttribute("class","glyphicon glyphicon-trash");

    caneca.appendChild(img);
    div.appendChild(caneca);
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

Atributos.prototype.borrarCampos = function(elemento){

    aux = elemento.parentNode;
    aux.removeChild(elemento);

}

Atributos.prototype.cambiarCheckbox = function(campo, registro)
{
    document.getElementById(campo+registro).value = document.getElementById(campo+"C"+registro).checked ? 1 : 0;
}

function mostrarModalDependencia(nombre)
{
    if(nombre.indexOf("abreviaturaClasificacionDocumental")  >= 0)
    {
        document.getElementById("registro").value = nombre.replace("abreviaturaClasificacionDocumental","");
        document.getElementById("campo").value = ''
    }
    else
    {
        document.getElementById("registro").value = nombre.replace("abreviaturasubClasificacionDocumental","");
        document.getElementById("campo").value = 'sub'
    }
   $('#myModal').modal('show'); 
    
}
