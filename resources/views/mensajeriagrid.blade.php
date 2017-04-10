<?php 
$tipoMensajeria = $_GET['tipo'];
?>

@extends('layouts.grid')
@section('titulo')<h3 class="pestana" id="titulo"><center>Mensajería</center></h3>@stop
@section('content')
{!!Html::script('js/mensajeria.js')!!}
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
<?php 
    $visible = '';

    if (isset($datos[0])) 
    {
        $dato = get_object_vars($datos[0]);
        if ($dato['adicionarRolOpcion'] == 1) 
        {
            $visible = 'inline-block;';    
        }
        else
        {
            $visible = 'none;';
        }
    }
    else
    {
        $visible = 'none;';
    }
?> 
        <div class="container">
            <div class="row">
                <div class="container">
                    <br>
                    <div class="btn-group" style="margin-left: 94%;margin-bottom:0px" title="Columns">
                        <button type="button" class="btn btn-default dropdown-toggle"data-toggle="dropdown">
                            <i class="glyphicon glyphicon-th icon-th"></i> 
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                            <li><a class="toggle-vis" data-column="0"><label> Iconos</label></a></li>
                            <li><a class="toggle-vis" data-column="1"><label> ID</label></a></li>
                            <li><a class="toggle-vis" data-column="2"><label> Tipo de envío</label></a></li>
                            <li><a class="toggle-vis" data-column="3"><label> Tipo</label></a></li>
                            <li><a class="toggle-vis" data-column="4"><label> Prioridad</label></a></li>
                            <li><a class="toggle-vis" data-column="5"><label> Fecha limite</label></a></li>
                            <li><a class="toggle-vis" data-column="6"><label> Transportador</label></a></li>
                            <li><a class="toggle-vis" data-column="7"><label> Destinatario</label></a></li>
                            <li><a class="toggle-vis" data-column="8"><label> Estado</label></a></li>
                            <li><a class="toggle-vis" data-column="9"><label> Fecha de entrega</label></a></li>
                            <li><a class="toggle-vis" data-column="10"><label> Usuario creador</label></a></li>
                        </ul>
                    </div>
                    <table id="tmensajeria" name="tmensajeria" class="display table-bordered" width="100%">
                        <thead>
                            <tr class="btn-primary active">
                                <th style="width:80px;padding: 1px 8px;" data-orderable="false">
                                <?php echo '<a href="mensajeria/create?tipo='.$tipoMensajeria.'"><span style= "display:'.$visible.' color:white;" class="glyphicon glyphicon-plus"></span></a>'; ?>
                                 <a href="#"><span style="color:white" class="glyphicon glyphicon-refresh"></span></a>
                                 <a><span class="glyphicon glyphicon-remove-sign" style="color:white; cursor:pointer;" id="btnLimpiarFiltros"></span></a>
                                </th>
                                <th><b>ID</b></th>
                                <th><b>Tipo de envío</b></th>
                                <th><b>Tipo</b></th>
                                <th><b>Prioridad</b></th>
                                <th><b>Fecha limite</b></th>
                                <th><b>Transportador</b></th>
                                <th><b>Destinatario</b></th>
                                <th><b>Estado</b></th>
                                <th><b>Fecha de entrega</b></th>
                                <th><b>Usuario creador</b></th>
                            </tr>
                        </thead>
                                        <tfoot>
                            <tr class="btn-default active">
                                <th style="width:80px;padding: 1px 8px;">
                                    &nbsp;
                                </th>
                                <th>ID</th>
                                <th>Tipo de envío</th>
                                <th>Tipo</th>
                                <th>Prioridad</th>
                                <th>Fecha limite</th>
                                <th>Transportador</th>
                                <th>Destinatario</th>
                                <th>Estado</th>
                                <th>Fecha de entrega</th>
                                <th>Usuario creador</th>
                            </tr>
                        </tfoot>        
                    </table>
                </div>
            </div>
        </div>


<script type="text/javascript">

    function imprimirMensajeria(idMensajeria)
    {
        window.open('http://'+location.host+'/mensajeria/'+idMensajeria+'?tipo=datos','_blank','width=2500px, height=700px, scrollbars=yes');
    }

    function imprimirStickerMensajeria(idMensajeria)
    {
        window.open('http://'+location.host+'/mensajeria/'+idMensajeria+'?tipo=sticker','_blank','width=400px, height=400px, scrollbars=yes');
    }

    $(document).ready( function () {

        
        /*$('#tmensajeria').DataTable({
            "aProcessing": true,
            "aServerSide": true,
            "stateSave":true,
            "ajax": "{!! URL::to ('/datosmensajeria')!!}",
        });*/
        var lastIdx = null;
        var modificar = '<?php echo (isset($datos[0]) ? $dato["modificarRolOpcion"] : 0);?>';
        var eliminar = '<?php echo (isset($datos[0]) ? $dato["eliminarRolOpcion"] : 0);?>';
        var imprimir = '<?php echo (isset($datos[0]) ? $dato["consultarRolOpcion"] : 0);?>';
        var tipo = '<?php echo $tipoMensajeria?>';
        var table = $('#tmensajeria').DataTable( {
            "order": [[ 1, "asc" ]],
            "aProcessing": true,
            "aServerSide": true,
            "stateSave":true,
            "ajax": "{!! URL::to ('/datosMensajeria?modificar="+modificar+"&eliminar="+eliminar+"&imprimir="+imprimir+"&tipo="+tipo+"')!!}",
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

        $('#tmensajeria tbody')
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
    $('#tmensajeria tfoot th').each( function () {
        if($(this).index()>0){
        var title = $('#tmensajeria thead th').eq( $(this).index() ).text();
        $(this).html( '<input type="text" placeholder="Buscar por '+title+'" />' );
        }
    } );
 
    // DataTable
    var table = $('#tmensajeria').DataTable();
 
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

        $('#btnLimpiarFiltros').click(function() 
        {
            that
                .search('')
                .draw();
        });
    })

    
});
    
</script>

@stop