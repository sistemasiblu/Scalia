
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
