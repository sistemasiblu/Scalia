function ocultarConsulta(elemento) {
          if(elemento.value=="1" || elemento.value=="2") {
              document.getElementById("lista").style.display = "block";
              document.getElementById("consulta").style.display = "none";
           }else{
               if(elemento.value=="3"){
                   document.getElementById("lista").style.display = "none";
                   document.getElementById("consulta").style.display = "block";
               }else{
                   if(elemento.value=="4"){
                        document.getElementById("lista").style.display = "none";
                        document.getElementById("consulta").style.display = "none";
                    }  
                }
            }
          }
