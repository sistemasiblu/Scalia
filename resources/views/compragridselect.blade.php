<?php 
$idDocumentoImportacion = $_GET['idDocumento'];


$docImportacion  = DB::Select('SELECT * from documentoimportacion where idDocumentoImportacion = '.$idDocumentoImportacion);

$importacion = get_object_vars($docImportacion[0]);

?>

@extends('layouts.modal') 
@section('titulo')<h3 class="pestana" id="titulo"><center>Compra <?php echo $importacion['nombreDocumentoImportacion'];?></h3>@stop
@section('content')
{!!Html::script('js/embarque.js')!!}
<style>
    tfoot input {
                width: 100%;
                padding: 3px;
                background-color: #fff;
                background-image: none;
                border: 1px solid #ccc;
                border-radius: 4px;
            }
</style> 

        <div class="container">
            <div class="row">
                <div class="container">
                    <div class="btn-group" style="margin-left: 94%;margin-bottom:4px" title="Columns">
                        <button  type="button" class="btn btn-default dropdown-toggle"data-toggle="dropdown">
                            <i class="glyphicon glyphicon-th icon-th"></i> 
                            <span class="caret"></span>
                        </button>
                       <ul class="dropdown-menu dropdown-menu-right" role="menu">
                            <li><a class="toggle-vis" data-column="0"><label> Compra</label></a></li>
                            <li><a class="toggle-vis" data-column="1"><label> Proveedor</label></a></li>
                            <li><a class="toggle-vis" data-column="2"><label> PI</label></a></li>
                            <li><a class="toggle-vis" data-column="3"><label> Volumen</label></a></li>
                            <li><a class="toggle-vis" data-column="4"><label> Valor</label></a></li>
                            <li><a class="toggle-vis" data-column="5"><label> Unidades</label></a></li>
                            <li><a class="toggle-vis" data-column="6"><label> Embarcadas</label></a></li>
                            <li><a class="toggle-vis" data-column="7"><label> Faltantes</label></a></li>
                            <li><a class="toggle-vis" data-column="8"><label> Peso</label></a></li>
                            <li><a class="toggle-vis" data-column="9"><label> Bultos</label></a></li>
                            <li><a class="toggle-vis" data-column="10"><label> Delivery</label></a></li>
                        </ul>
                    </div>
                    
                    <table id="tcompraSelect" name="tcompraSelect" class="display table-bordered" width="100%">
                        <thead>
                            <tr class="btn-primary active">

                                <th><b>Compra</b></th>
                                <th><b>Proveedor</b></th>
                                <th><b>PI</b></th>
                                <th><b>Volumen</b></th>
                                <th><b>Valor</b></th>
                                <th><b>Unidades</b></th>
                                <th style="background:#A9F5A9;"><b>Embarcadas</b></th>
                                <th style="background:#F5A9A9;"><b>Faltantes</b></th>
                                <th><b>Peso</b></th>
                                <th><b>Bultos</b></th>
                                <th><b>Delivery</b></th>
                                
                            </tr>
                        </thead>
                        <tfoot>
                            <tr class="btn-default active">

                                <th>Compra</th>
                                <th>Proveedor</th>
                                <th>PI</th>
                                <th>Volumen</th>
                                <th>Valor</th>
                                <th>Unidades</th>
                                <th>Embarcadas</th>
                                <th>Faltantes</th>
                                <th>Peso</th>
                                <th>Bultos</th>
                                <th>Delivery</th>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="modal-footer">
                        <button id="botonCompra" name="botonCompra" type="button" class="btn btn-primary" >Seleccionar</button>
                    </div>

                <div class="form-group col-md-4" id='test' style="display:inline-block">
                  {!!Form::label('volumenTotalCompra', 'Volumen: ', array('class' => 'col-sm-3 control-label')) !!}
                  <div class="col-md-8">
                    <div class="input-group">
                      <span class="input-group-addon">
                        <i class="fa fa-expand"></i>
                      </span>
                      {!!Form::text('volumenTotalCompra',0,['class'=>'form-control','readonly', 'placeholder'=>'', 'style'=>'width:150px; height:30px;text-align: right;'])!!}
                    </div>
                  </div>
                </div>

                <div class="form-group col-md-4" id='test' style="display:inline-block">
                  {!!Form::label('valorTotalCompra', 'Valor: ', array('class' => 'col-sm-3 control-label')) !!}
                  <div class="col-md-8">
                    <div class="input-group">
                      <span class="input-group-addon">
                        <i class="fa fa-usd"></i>
                      </span>
                      {!!Form::text('valorTotalCompra',0,['class'=>'form-control','readonly', 'placeholder'=>'', 'style'=>'width:150px; height:30px;text-align: right;'])!!}
                    </div>
                  </div>
                </div>

                <div class="form-group col-md-4" id='test' style="display:inline-block">
                  {!!Form::label('unidadesTotalCompra', 'Unidades: ', array('class' => 'col-sm-3 control-label')) !!}
                  <div class="col-md-8">
                    <div class="input-group">
                      <span class="input-group-addon">
                        <i class="fa fa-cubes"></i>
                      </span>
                      {!!Form::text('unidadesTotalCompra',0,['class'=>'form-control','readonly', 'placeholder'=>'', 'style'=>'width:150px; height:30px;text-align: right;'])!!}
                    </div>
                  </div>
                </div>

                <div class="form-group col-md-4" id='test' style="display:inline-block">
                  {!!Form::label('pesoTotalCompra', 'Peso: ', array('class' => 'col-sm-3 control-label')) !!}
                  <div class="col-md-8">
                    <div class="input-group">
                      <span class="input-group-addon">
                        <i class="fa fa-sort-amount-desc"></i>
                      </span>
                      {!!Form::text('pesoTotalCompra',0,['class'=>'form-control','readonly', 'placeholder'=>'', 'style'=>'width:150px; height:30px;text-align: right;'])!!}
                    </div>
                  </div>
                </div>

                <div class="form-group col-md-4" id='test' style="display:inline-block">
                  {!!Form::label('bultoTotalCompra', 'Bultos: ', array('class' => 'col-sm-3 control-label')) !!}
                  <div class="col-md-8">
                    <div class="input-group">
                      <span class="input-group-addon">
                        <i class="fa fa-cube"></i>
                      </span>
                      {!!Form::text('bultoTotalCompra',0,['class'=>'form-control','readonly', 'placeholder'=>'', 'style'=>'width:150px; height:30px;text-align: right;'])!!}
                    </div>
                  </div>
                </div>

                </div>
            </div>
        </div>


<script type="text/javascript">

    $(document).ready( function () {

        
        /*$('#tcompraSelect').DataTable({
            "aProcessing": true,
            "aServerSide": true,
            "stateSave":true,
            "ajax": "{!! URL::to ('/datosCompra')!!}",
        });*/
        var lastIdx = null;
        var idDoc = "<?php echo $_GET['idDocumento'];?>";
        var table = $('#tcompraSelect').DataTable( {
            "order": [[ 1, "asc" ]],
            "aProcessing": true,
            "aServerSide": true,
            "stateSave":true,
            "ajax": "{!! URL::to ('/datosCompraSelect?idDocumento="+idDoc+"')!!}",
            "language": {
                        "sProcessing":     "Procesando...",
                        "sLengthMenu":     "Mostrar _MENU_ registros",
                        "sZeroRecords":    "No se encontraron resultados",
                        "sEmptyTable":     "Ning&uacute;n dato disponible en esta tabla",
                        "sInfo":           "Registros del _START_ al _END_ de un total de _TOTAL_ ",
                        "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
                        "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
                        "sInfoPostFix":    "",
                        "sSearch":         "Buscar:",
                        "sUrl":            "",
                        "sInfoThousands":  ",",
                        "sLoadingRecords": "Cargando...",
                        "oPaginate": {
                            "sFirst":    "Primero",
                            "sLast":     "&Uacute;ltimo",
                            "sNext":     "Siguiente",
                            "sPrevious": "Anterior"
                        },
                        "oAria": {
                            "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                            "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                        }
                    }
        });
         
        $('a.toggle-vis').on( 'click', function (e) {
            e.preventDefault();
     
            // Get the column API object
            var column = table.column( $(this).attr('data-column') );
     
            // Toggle the visibility
            column.visible( ! column.visible() );
        } );

        $('#tcompraSelect tbody')
        .on( 'mouseover', 'td', function () {
            var colIdx = table.cell(this).index().column;
 
            if ( colIdx !== lastIdx ) {
                $( table.cells().nodes() ).removeClass( 'highlight' );
                $( table.column( colIdx ).nodes() ).addClass( 'highlight' );
            }
        } )
        .on( 'mouseleave', function () {
            $( table.cells().nodes() ).removeClass( 'highlight' );
        } );


        // Setup - add a text input to each footer cell
    $('#tcompraSelect tfoot th').each( function () {
        
        var title = $('#tcompraSelect thead th').eq( $(this).index() ).text();
        $(this).html( '<input type="text" placeholder="Buscar por '+title+'" />' );
        
    } );
 
    // DataTable
    var table = $('#tcompraSelect').DataTable();
 
    // Apply the search
    table.columns().every( function () {
        var that = this;
 
        $( 'input', this.footer() ).on( 'blur change', function () {
            if ( that.search() !== this.value ) {
                that
                    .search( this.value )
                    .draw();
            }
        } );
    })

    $('#tcompraSelect tbody').on( 'click', 'tr', function () {
        $(this).toggleClass('selected');

        var datos = table.rows('.selected').data();
        
        volumen = 0;
        valor = 0;
        unidades = 0;
        peso = 0;
        bulto = 0;
        for (var i = 0; i < datos.length; i++) 
        {
            volumen += parseFloat(datos[i][3]);
            valor += parseFloat(datos[i][4]);
            unidades += parseFloat(datos[i][7]);
            peso += parseFloat(datos[i][8]);
            bulto += parseFloat(datos[i][9]);
        }

        $('#volumenTotalCompra').val(volumen);
        $('#valorTotalCompra').val(valor);
        $('#unidadesTotalCompra').val(unidades);
        $('#pesoTotalCompra').val(peso);
        $('#bultoTotalCompra').val(bulto);

    } );
 
     $('#botonCompra').click(function() {
        var datos = table.rows('.selected').data();  

        for (var i = 0; i < datos.length; i++) 
        {
            var valores = new Array(datos[i][11],'',datos[i][0],datos[i][1],datos[i][2],datos[i][10],datos[i][2],datos[i][3],datos[i][4],datos[i][7],datos[i][7],datos[i][8],datos[i][9],'','','','','','','','','','','',datos[i][15],0,'','',0,datos[i][13],'','','','',0,0,'',0,0,'','');
            window.parent.embarques.agregarCampos(valores,'A');  
        }
        window.parent.$("#myModalCompra").modal("hide");

       calcularTotales();     
    });

    
});
    
</script>
@stop