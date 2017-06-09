

var AtributosNota = function(nombreObjeto, nombreContenedor, nombreDiv){
    this.alto = '100px;';
    this.ancho = '100%;';
    this.campoid = 'idMovimientoCRMNota';
    this.campoEliminacion = 'eliminarNota';
    this.botonEliminacion = true;

    this.nombre = nombreObjeto;
    this.contenedor = nombreContenedor;
    this.contenido = nombreDiv;
    this.contador = 0;
};
AtributosNota.prototype.agregarNota = function(datos, tipo){

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
        caneca.setAttribute("class","canecaNota col-md-1");
        caneca.setAttribute("style","");
        img.setAttribute("class","glyphicon glyphicon-trash");

        caneca.appendChild(img);
        div.appendChild(caneca);
    }

    
    //--------------------
    // id de la Nota
    //--------------------
    var input = document.createElement('input');
    input.type =  "hidden";
    input.id =  "idMovimientoCRMNota" + this.contador;
    input.name =  "idMovimientoCRMNota[]";
    input.value = valor[(tipo == 'A' ? 0 : "idMovimientoCRMNota")] ;
    input.setAttribute("class", "");
    input.readOnly = "";
    input.autocomplete = "false";
    div.appendChild(input);

    //--------------------
    // id de usuario
    //--------------------
    var input = document.createElement('input');
    input.type =  "hidden";
    input.id =  "Users_idUsuario" + this.contador;
    input.name =  "Users_idUsuario[]";
    input.value = valor[(tipo == 'A' ? 1 : "Users_idUsuario")] ;
    input.setAttribute("class", "");
    input.readOnly = "";
    input.autocomplete = "false";
    div.appendChild(input);

    //--------------------
    // Nombre de usuario
    //--------------------
    var input = document.createElement('input');
    input.type =  "text";
    input.id =  "nombreUsuario" + this.contador;
    input.name =  "nombreUsuario[]";
    input.value = 'Escrito por: '+valor[(tipo == 'A' ? 2 : "nombreUsuario")] ;
    input.setAttribute("class", "nombreUsuarioNota");
    input.readOnly = "readOnly";
    input.autocomplete = "false";
    div.appendChild(input);

    //--------------------
    // fecha elaboración
    //--------------------
    var input = document.createElement('input');
    input.type =  "text";
    input.id =  "fechaMovimientoCRMNota" + this.contador;
    input.name =  "fechaMovimientoCRMNota[]";
    input.value = valor[(tipo == 'A' ? 3 : "fechaMovimientoCRMNota")] ;
    input.setAttribute("class", "fechaNota");
    input.readOnly = "readOnly";
    input.autocomplete = "false";
    div.appendChild(input);

    //--------------------
    // Texto de la Nota
    //--------------------
    var input = document.createElement('textarea');
    input.id =  "observacionMovimientoCRMNota" + this.contador;
    input.name =  "observacionMovimientoCRMNota[]";
    input.placeholder =  "Descripción";
    input.value = valor[(tipo == 'A' ? 4 : "observacionMovimientoCRMNota")] ;
    input.setAttribute("class", "textoNota");
    input.readOnly = (tipo == 'L' ? "readOnly" : '');
    input.autocomplete = "false";
    div.appendChild(input);

    

 
       
    espacio.appendChild(div);

    this.contador++;
}

AtributosNota.prototype.borrarCampos = function(elemento, campoEliminacion, campoid){
   
    if(campoEliminacion && document.getElementById(campoEliminacion) && document.getElementById(campoid))
        document.getElementById(campoEliminacion).value += document.getElementById(campoid).value + ',';

    // aux = elemento.parentNode;
    // alert(aux);
    // if(aux );
        $("#"+elemento).remove();

}


AtributosNota.prototype.borrarTodosCampos = function(){
    
    
    for (var posborrar = 0 ; posborrar < this.contador; posborrar++) 
    {
        this.borrarCampos(this.contenido+posborrar, this.campoEliminacion, this.campoid+this.contador);
    }
    this.contador = 0;
}