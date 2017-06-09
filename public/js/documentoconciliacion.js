
var valorDetalle = [0,0,'','',''];

$(document).ready(function(){
    
    comercial = new Atributos('comercial','contenedor_comercial','comercial_');
    
    comercial.altura = '36px;';
    comercial.campoid = 'idDocumentoConciliacionComercial';
    comercial.campoEliminacion = 'eliminarDocumentoConciliacionComercial';

    comercial.campos = ['idDocumentoConciliacionComercial', 'ValorConciliacion_idValorConciliacionCom', 'nombreValorConciliacionCom', 'cuentasLocalDocumentoConciliacionComercial', 'cuentasNiifDocumentoConciliacionComercial'];
    comercial.etiqueta = ['input','input','input','input','input'];
    comercial.tipo = ['hidden','hidden','text','text','text'];
    comercial.estilo = ['','','width: 20%;height:35px;','width: 36%;height:35px;','width: 36%;height:35px;'];
    comercial.clase = ['','','','',''];
    comercial.sololectura = [false,false,true,false,false];
    
    for(var j=0, k = DocumentoConciliacionComercial.length; j < k; j++)
    {
        comercial.agregarCampos(JSON.stringify(DocumentoConciliacionComercial[j]),'L');
    }



    cartera = new Atributos('cartera','contenedor_cartera','cartera_');
    
    cartera.altura = '36px;';
    cartera.campoid = 'idDocumentoConciliacionCartera';
    cartera.campoEliminacion = 'eliminarDocumentoConciliacionCartera';

    cartera.campos = ['idDocumentoConciliacionCartera', 'ValorConciliacion_idValorConciliacionCar', 'nombreValorConciliacionCar', 'cuentasLocalDocumentoConciliacionCartera', 'cuentasNiifDocumentoConciliacionCartera'];
    cartera.etiqueta = ['input','input','input','input','input'];
    cartera.tipo = ['hidden','hidden','text','text','text'];
    cartera.estilo = ['','','width: 20%;height:35px;','width: 36%;height:35px;','width: 36%;height:35px;'];
    cartera.clase = ['','','','',''];
    cartera.sololectura = [false,false,true,false,false];
    
    for(var j=0, k = DocumentoConciliacionCartera.length; j < k; j++)
    {
        cartera.agregarCampos(JSON.stringify(DocumentoConciliacionCartera[j]),'L');
    }
   
});

function abrirModalValor(tipo)
{
    $(this).removeClass("selected");
    
    $("#divTabla").html('');

    estructuraTabla = '<table id="tvalorSelect" name="tvalorSelect" class="display table-bordered" width="100%">'+
                          '<thead>'+
                              '<tr class="btn-default active">'+
                                  '<th><b>ID</b></th>'+
                                  '<th><b>Valor</b></th> '+       
                              '</tr>'+
                          '</thead>'+
                          '<tfoot>'+
                              '<tr class="btn-default active">'+

                                  '<th>ID</th>'+
                                  '<th>Valor</th> '+                            
                              '</tr>'+
                          '</tfoot>'+
                      '</table>';

    $("#divTabla").html(estructuraTabla);

	var lastIdx = null;
    window.parent.$("#tvalorSelect").DataTable().ajax.url('http://'+location.host+"/datosValorConciliacionSelect?tipo="+tipo).load();
     // Abrir modal
    window.parent.$("#ModalValor").modal()

    $("a.toggle-vis").on( "click", function (e) {
        e.preventDefault();
 
        // Get the column API object
        var column = table.column( $(this).attr("data-column") );
 
        // Toggle the visibility
        column.visible( ! column.visible() );
    } );

    window.parent.$("#tvalorSelect tbody").on( "mouseover", "td", function () 
    {
        var colIdx = table.cell(this).index().column;

        if ( colIdx !== lastIdx ) {
            $( table.cells().nodes() ).removeClass( "highlight" );
            $( table.column( colIdx ).nodes() ).addClass( "highlight" );
        }
    }).on( "mouseleave", function () 
    {
        $( table.cells().nodes() ).removeClass( "highlight" );
    } );


    // Setup - add a text input to each footer cell
    window.parent.$("#tvalorSelect tfoot th").each( function () 
    {
        var title = window.parent.$("#tvalorSelect thead th").eq( $(this).index() ).text();
        $(this).html( "<input type='text' placeholder='Buscar por "+title+"'/>" );
    });
 
    // DataTable
    var table = window.parent.$("#tvalorSelect").DataTable();
 
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

    window.parent.$('#tvalorSelect tbody').on( 'click', 'tr', function () {
        $(this).toggleClass('selected');

        var datos = table.rows('.selected').data();


    } );

    window.parent.$('#botonCampo').click(function() {
        var datos = table.rows('.selected').data();  

        for (var i = 0; i < datos.length; i++) 
        {
            var valores = new Array(0, datos[i][0],datos[i][1],'','');

            if(tipo == 'comercial')
            {
                resultCom = false;
                for(cont = 0; cont < comercial.contador; cont++)
                {
                    if($('#ValorConciliacion_idValorConciliacionCom'+cont).val() == datos[i][0])
                    {
                        resultCom = true;
                        cont = comercial.contador;
                    }
                }

                if(resultCom === false)
                {
                    window.parent.comercial.agregarCampos(valores,'A');
                }
            }
            else
            {
                resultCar = false;
                for(cont = 0; cont < cartera.contador; cont++)
                {
                  if($('#ValorConciliacion_idValorConciliacionCar'+cont).val() == datos[i][0])
                  {
                    resultCar = true;
                    cont = cartera.contador;
                  }
                }

                if(resultCar === false)
                {
                    window.parent.cartera.agregarCampos(valores,'A');
                }                  
            }
        }
        window.parent.$("#ModalValor").modal("hide");
    });

}