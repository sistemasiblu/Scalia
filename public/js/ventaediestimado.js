
      var arrayProducto = ['','','','','',''];
      var condicionRangos = '';
      var condicionGeneral = '';

      $(document).ready(function()
      { 
            producto = new Atributos('producto','contenedor_productos','producto_');

            producto.altura = '35px;';
            producto.botonEliminacion = true;
            // producto.campoid = 'idVentaEDIEstimado';
            // producto.campoEliminacion = 'eliminarVentaEDIEstimado';

            producto.campos   = ['Producto_idProducto', 'codigoAlternoProducto','referenciaProducto','nombreLargoProducto','diasVentaEDIEstimado','fechaInicioVentaEDIEstimado'];
            producto.etiqueta = ['input', 'input','input','input','input','input'];
            producto.tipo     = ['text','text','text','text','text','date'];
            producto.estilo   = ['width:139px;height:35px;','width:234px;height:35px;','width:234px;height:35px;','width:292px;height:35px;','width:234px;height:35px;','width:234px;height:35px;'];
            producto.clase    = ['','','','','',''];
            producto.opciones = ['','','','','',''];      

            producto.sololectura = [true,true,true,true,false,false];
            producto.funciones = ['', '', '', '', '', ''];
      });

      function condicionRango(campo)
      {
            if($("#"+campo+"Inicial").val() != '' && $("#"+campo+"Final").val() != '')
            {
                  condicionRangos = " ("+campo+" >= '" + $("#"+campo+"Inicial").val() + "' AND "+campo+" <= '" + $("#"+campo+"Final").val() + "')  AND ";
            }
      }

      function consultarProducto(tipo)
      {
            condicionGeneral = ($("#Marca_idMarca").val() != '') ? " Marca_idMarca = "+$("#Marca_idMarca").val()+" AND " : "";
            condicionGeneral += ($("#TipoProducto_idTipoProducto").val() != '') ? " TipoProducto_idTipoProducto = "+$("#TipoProducto_idTipoProducto").val()+" AND " : "";
            condicionGeneral += ($("#TipoNegocio_idTipoNegocio").val() != '') ? " TipoNegocio_idTipoNegocio = "+$("#TipoNegocio_idTipoNegocio").val()+" AND " : "";
            condicionGeneral += ($("#Temporada_idTemporada").val() != '') ? " Temporada_idTemporada = "+$("#Temporada_idTemporada").val()+" AND " : "";
            condicionGeneral += ($("#Tercero_idTercero").val() != '') ? " Tercero_idTercero = "+$("#Tercero_idTercero").val()+" AND " : "";
            condicionGeneral += ($("#Categoria_idCategoria").val() != '') ? " Categoria_idCategoria = "+$("#Categoria_idCategoria").val()+" AND " : "";
            condicionGeneral += ($("#EsquemaProducto_idEsquemaProducto").val() != '') ? " EsquemaProducto_idEsquemaProducto = "+$("#EsquemaProducto_idEsquemaProducto").val()+" AND " : "";

            condicionGeneral = condicionRangos+condicionGeneral;

            condicionGeneral = condicionGeneral.slice(0, -4);
            // alert( location.host+'/consultarProducto/');
            // return;
            if(tipo == 1)
            {     
                  if(condicionGeneral != '')
                  {             
                        var token = document.getElementById('token').value;
                        $.ajax({
                              headers: {'X-CSRF-TOKEN': token},
                              dataType: "json",
                              data: {'condicion': condicionGeneral},
                              url: 'http://'+location.host+'/consultarProducto',
                              type:  'get',
                              beforeSend: function(){
                                  },
                              success: function(respuesta)
                              {
                                    if(respuesta.length > 0)
                                    {
                                          for(cont = 0; cont < respuesta.length; cont++)
                                          {
                                                arrayProducto = [respuesta[cont]['idProducto'],respuesta[cont]['codigoAlternoProducto'],respuesta[cont]['referenciaProducto'],respuesta[cont]['nombreLargoProducto'],respuesta[cont]['diasVentaEDIEstimado'],respuesta[cont]['fechaInicioVentaEDIEstimado']];
                                                producto.agregarCampos(arrayProducto,'A');
                                          }
                                    }
                              },
                              error: function(xhr,err)
                              { 
                                    alert("Error");
                              }
                        });
                  }
                  else
                  {
                        alert('Debe ingresar filtros para la consulta.');
                  }
            }
            else
            {
                  abrirModalDocumentos(condicionGeneral);
            }
      }

      function abrirModalDocumentos(condicion)
      {   
            if(condicion != '')
            {
                  var lastIdx = null;
                  window.parent.$("#tmodalProductos").DataTable().ajax.url("http://"+location.host+"/datosMaestroProductos?condicion="+condicion).load();
                  // Abrir modal
                  window.parent.$("#modalProductos").modal()

                  $("a.toggle-vis").on( "click", function (e) {
                  e.preventDefault();

                  // Get the column API object
                  var column = table.column( $(this).attr("data-column") );

                  // Toggle the visibility
                  column.visible( ! column.visible() );
                  } );

                  window.parent.$("#tmodalProductos tbody").on( "mouseover", "td", function () 
                  {
                        var colIdx = table.cell(this).index().column;

                        if ( colIdx !== lastIdx ) {
                            $( table.cells().nodes() ).removeClass( "highlight" );
                            $( table.column( colIdx ).nodes() ).addClass( "highlight" );
                        }
                  }).on( "mouseleave", function () {
                        $( table.cells().nodes() ).removeClass( "highlight" );
                  });

                  // Setup - add a text input to each footer cell
                  window.parent.$("#tmodalProductos tfoot th").each( function () 
                  {
                        var title = window.parent.$("#tmodalProductos thead th").eq( $(this).index() ).text();
                        $(this).html( "<input type='text' placeholder='Buscar por "+title+"'/>" );
                  });

                  // DataTable
                  var table = window.parent.$("#tmodalProductos").DataTable();

                  // Apply the search
                  table.columns().every( function () 
                  {
                        var that = this;

                        $( "input", this.footer() ).on( "blur change", function () {
                            if ( that.search() !== this.value ) {
                                that
                                    .search( this.value )
                                    .draw();
                            }
                        } );
                  })

                  $('#tmodalProductos tbody').on('click', 'tr', function () {
                        $(this).toggleClass('selected');
                  } );

                  $('#btnSeleccionar').click(function() {
                        var datos = table.rows('.selected').data();

                        for (var i = 0; i < datos.length; i++) 
                        {
                            var valores = new Array(datos[i][0],datos[i][1],datos[i][2],datos[i][3],'','');
                            window.parent.producto.agregarCampos(valores,'A');  
                        }
                        window.parent.$("#modalProductos").modal("hide");
                  });
            }
            else
            {
                  alert('Debe ingresar filtros para la consulta.');
            }
      }

      function llenarMasivo(valor,campo)
      {
            for (var i = 0; i < producto.contador; i++) 
            {
                  if(document.getElementById('Producto_idProducto'+i))
                  {
                        document.getElementById(campo+i).value = valor;
                  }
            };
      }

      function Limpiar()
      {
            window.location.href = '{{route("/ventaediestimado")}}';
      }