
function ocultarSistema(elemento) 
{
  if(elemento.value=="1") 
  {
      document.getElementById("sistemainformacion").style.display = "none";
  }
  else
  {
     document.getElementById("sistemainformacion").style.display = "block";
  }
        
 }

function mostrarModalDocumento()
{
    $('#myModalDocumento').modal();
}

function mostrarModalRol()
{
   $('#myModalRol').modal('show'); 
}


