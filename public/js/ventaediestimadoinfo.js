
      var arrayProducto = ['','','','','',''];
      
      var condicionGeneral = '';

      function condicionRango(campo)
      {
            if($("#"+campo+"Inicial").val() != '' && $("#"+campo+"Final").val() != '')
            {
                  condicionRangos += " ("+campo+" >= '" + $("#"+campo+"Inicial").val() + "' AND "+campo+" <= '" + $("#"+campo+"Final").val() + "') AND ";
            }
            return condicionRangos;
      }

      function consultarInforme()
      {
            idPeriodo = $("#Periodo_idPeriodo").val();
            //var condicionRangos = '';

            //condicionRangos += condicionRango('referenciaProductoInicial');

            condicionGeneral = "clasificacionProducto LIKE '*01*' AND ";
            condicionGeneral += ($("#referenciaProductoInicial").val() != '') ? " referenciaProducto like '*"+$("#referenciaProductoInicial").val()+"*' AND " : "";
            condicionGeneral += ($("#Marca_idMarca").val() != '') ? " Marca_idMarca = "+$("#Marca_idMarca").val()+" AND " : "";
            condicionGeneral += ($("#TipoProducto_idTipoProducto").val() != '') ? " TipoProducto_idTipoProducto = "+$("#TipoProducto_idTipoProducto").val()+" AND " : "";
            condicionGeneral += ($("#TipoNegocio_idTipoNegocio").val() != '') ? " TipoNegocio_idTipoNegocio = "+$("#TipoNegocio_idTipoNegocio").val()+" AND " : "";
            condicionGeneral += ($("#Temporada_idTemporada").val() != '') ? " Temporada_idTemporada = "+$("#Temporada_idTemporada").val()+" AND " : "";
            condicionGeneral += ($("#Tercero_idTercero").val() != '') ? " Tercero_idTercero = "+$("#Tercero_idTercero").val()+" AND " : "";
            condicionGeneral += ($("#Categoria_idCategoria").val() != '') ? " Categoria_idCategoria = "+$("#Categoria_idCategoria").val()+" AND " : "";
            condicionGeneral += ($("#EsquemaProducto_idEsquemaProducto").val() != '') ? " EsquemaProducto_idEsquemaProducto = "+$("#EsquemaProducto_idEsquemaProducto").val()+" AND " : "";
            condicionGeneral += ($("#Bodega_idBodega").val() != '') ? " Bodega_idBodega IN ("+$("#Bodega_idBodega").val()+") AND " : "";

            // condicionGeneral = condicionRangos+condicionGeneral;

            condicionGeneral = condicionGeneral.slice(0, -4);

            
            if(condicionGeneral == '')
            {
                  alert('No se puede generar el informe sin aplicar ninguna condición de filtro');
                  return;
            }


            window.open('http://'+location.host+'/ventaediestimadoinfo/0?accion=imprimir&idPeriodo='+idPeriodo+'&condicion='+condicionGeneral, "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,width=1000,height=1000");

      }
