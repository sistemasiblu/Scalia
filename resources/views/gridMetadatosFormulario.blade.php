@extends('layouts.modal') 
@section('titulo')<h3 id="titulo"><center></h3>@stop
@section('content')
@include('alerts.request')
{!!Html::style('css/divopciones.css'); !!}
{!!Html::style('css/dropdown.css'); !!}
{!!Html::script('js/formulario.js'); !!}


{!!Form::open(['route'=>'radicado.update','method'=>'PUT', 'action' => 'RadicadoController@update', 'id' => 'radicado' , 'files' => true])!!}   
<style>
    tfoot input 
    {
        width: 100%;
        padding: 3px;
        background-color: #fff;
        background-image: none;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
</style> 

<!-- Esta es la ip del servidor -->
<?php $ip = $_SERVER['HTTP_HOST']; ?>

<!-- Consulto el nombre del titulo de la columna que se mostrará en la grid -->
<?php
$consultaFormulario = DB::table('documentopropiedad')
    ->leftjoin('metadato','documentopropiedad.Metadato_idMetadato', "=", 'metadato.idMetadato')
    ->leftjoin('documento','documentopropiedad.Documento_idDocumento', "=", 'documento.idDocumento')
    ->select(DB::raw('tituloMetadato'))
    ->where ('idDocumento', "=", $_GET['idDoc'])
    ->where('gridDocumentoPropiedad', "=", 1)
    ->where('tipoDocumento', "=", 1)
    ->get();

$idDoc = $_GET['idDoc'];
?>

<fieldset>
<!-- Esqueleto de la grid -->
    <div id="grid">
        <div class="container">
            <div class="row">
                <div class="btn-group" style="margin-left: 100%;margin-bottom:4px" title="Columns">
                    <button type="button" class="btn btn-default dropdown-toggle"data-toggle="dropdown">
                        <i class="glyphicon glyphicon-th icon-th"></i> 
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                        <li><a class="toggle-vis" data-column="0"><label> Iconos</label></a></li>
                        <li><a class="toggle-vis" data-column="1"><label> ID Radicado</label></a></li>
                        <?php 
                        for($i = 0; $i < count($consultaFormulario); $i++)
                        {
                          $data = $i + 2;
                          $formulario = get_object_vars($consultaFormulario[$i]);
                          echo '<li><a class="toggle-vis" data-column="'.$data.'"><label>'.str_replace('_', ' ', $formulario["tituloMetadato"]).'</label></a></li>';
                        }
                        echo'</ul>';
                        ?>
                </div>
                  <table id="tformulario" name="tformulario" class="display table-bordered" width="100%">
                    <thead>
                        <tr>
                        </tr>
                        <tr class="btn-primary active">
                            <th style="width:40px;padding: 1px -1px;" data-orderable="false">
                                <a href="#"><span style="color:white;" onclick="divFormulario(); armarMetadatosFormulario();" class="glyphicon glyphicon-plus"></span></a>
                                  <ul class="dropgroup">
                                      <li class="dropdown">
                                        <a href="#" class="dropbtn" style="color:white" ><span class="glyphicon glyphicon-print"></span></a>
                                        <div class="dropdown-content">
                                        <a href="#" title="Generar informe"><img src="../../imagenes/html.png" onclick="imprimirFormato('html');" style="width: 15px; cursor: pointer;"></a>
                                          <a href="#" title="Exportar a Excel"><img src="../../imagenes/excel.png" onclick="imprimirFormato('excel');" style="width: 20px; cursor: pointer;"></a>
                                          <a href="#" title="Exportar a Word"><img src="../../imagenes/word.png" onclick="imprimirFormato('word');" style="width: 20px; cursor: pointer;"></a>
                                        </div>
                                      </li>
                                    </ul>
                            </th>
                            <th>ID Radicado</th>
                                <?php
                                for($i = 0; $i < count($consultaFormulario); $i++)
                                {
                                  $formulario = get_object_vars($consultaFormulario[$i]);
                                  echo'<th><b>'.str_replace('_', ' ', $formulario["tituloMetadato"]).'</b></th>';
                                }
                                ?>
                        </tr>
                    </thead>
                        <tfoot>
                            <tr class="btn-default active">
                                <th style="width:40px;padding: 1px 8px;">
                                    &nbsp;
                                </th>
                                <th>ID Radicado</th>
                                <?php
                                for($i = 0; $i < count($consultaFormulario); $i++)
                                 {
                                  $formulario = get_object_vars($consultaFormulario[$i]);
                                   echo'<th>'.str_replace('_', ' ', $formulario["tituloMetadato"]).'</th>';
                                 }
                                ?>
                            </tr>
                        </tfoot>
                    </table> 
            </div>
        </div>
    </div>
<input id="idDocumentoF" name="idDocumentoF" type="hidden" value="<?php echo $idDoc; ?>">
<!-- Termina el esqueleto de la grid -->

<input type="hidden" id="token" value="{{csrf_token()}}"/>

<!-- Script de la grid -->
<script type="text/javascript">

    $(document).ready( function () {
        /*$('#tformulario').DataTable({
            "aProcessing": true,
            "aServerSide": true,
            "stateSave":true,
            "ajax": "{!! URL::to ('/datosFormulario')!!}",
        });*/
        var lastIdx = null;
        var idDoc = "<?php echo $_GET['idDoc'];?>";
        var table = $('#tformulario').DataTable( {
            "order": [[ 1, "asc" ]],
            "aProcessing": true,
            "aServerSide": true,
            "stateSave":true,
            "bSortClasses": false,
            "deferRender" : true,
            "ajax": "{!! URL::to ('/datosFormulario?idDoc="+idDoc+"')!!}",
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
        $('#tformulario tbody').on( 'click', 'tr', function () {
    console.log( table.row( this ).data() );
} );
        $('a.toggle-vis').on( 'click', function (e) {
            e.preventDefault();
     
            // Get the column API object
            var column = table.column( $(this).attr('data-column') );
     
            // Toggle the visibility
            column.visible( ! column.visible() );
        } );

        $('#tformulario tbody')
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
    $('#tformulario tfoot th').each( function () {
        if($(this).index()>0){
        var title = $('#tformulario thead th').eq( $(this).index() ).text();
        $(this).html( '<input type="text" placeholder="Buscar por '+title+'" />' );
        }
    } );
 
    // DataTable
    var table = $('#tformulario').DataTable();
 
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

    //  $('#tformulario tbody').on( 'click', 'tr', function () {
    //     $(this).toggleClass('selected');
    // } );
 
    // $('#descargarMasivo, #enviarMasivo, #imprimirMasivo, #eliminarMasivo').click( function () {
    //     var datos = table.rows('.selected').data();
    //     var ids = "";
    //     for (var i = 0; i < datos.length; i++) 
    //     {
    //         ids += datos[i][1]+',';
    //     };
    //     ids= ids.substring(0,ids.length-1); //quitar el ultimo caracter, en este caso la coma (,)
    //     accionArchivoMasivo(this.id, ids)
    //     // alert(ids);

    // });
});    
</script>
<!-- Termina el script de la grid -->

<!-- Inicia el div de formulario -->
    
    <div class="col-sm-10" id="formulario" style="width: 100%; height: 100%; background-color: white; z-index: 1000 ; border: 1px solid; border-color: #ddd; position: absolute; top: -1px; display: none;">
    <a class='cerrar' href='javascript:void(0);' onclick='document.getElementById(&apos;formulario&apos;).style.display = &apos;none&apos;';>x</a> 
        <h2 id="titulo">
        <left>Llenar formulario<h5>
        <label class= "col-sm-12 control-label">Versión</label> 
        <div class="input-group">
            <span class="input-group-addon">
                <i class="fa fa-bars"></i>
            </span>
            <input id="versionInicialFormulario" name="versionInicialFormulario" style="height:30px; width:80px;" type="text" readonly="true" value="">
        </div>
        </h5></left>
        </h2>

            <div id="metadatosFormulario">
                
            </div>
            <br/><br/><br/><br/><br/><br/><br/>
            &nbsp; &nbsp;
            {!!Form::button('Guardar',["class"=>"btn btn-primary", 'id'=>'guardarFormulario', 'onclick' => 'guardarDatosFormulario()'])!!} 
            <input id="tipoFormulario" name="tipoFormulario" type="hidden"  value="">    
    </div>
<!-- Termina el div de formulario -->

<!-- Inicia el div de la actualización a la versión -->
    <div class="col-sm-10" id="editarVersion" style="width: 100%;  float: left; background-color: white; z-index: 1000 ; border: 1px solid; border-color: #ddd; position: absolute; top: -1px; display: none;">
        <a class='cerrar' href='javascript:void(0);' onclick='document.getElementById(&apos;editarVersion&apos;).style.display = &apos;none&apos;';>x</a> 
        <h2 id="titulo">
        <left>Consulta de formulario<h5>
        <label class= "col-sm-12 control-label">Versión</label> 
        <div class="input-group">
            <span class="input-group-addon">
                <i class="fa fa-bars"></i>
            </span>
            <select id="versionMaximaFormulario" name= "versionMaximaFormulario" style="height: 30px;" onchange="llamarMetadatosFormulario(document.getElementById('idRadicadoF').value,this.value);">
            </select>    
        </div>
        &nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
        <i id="btn_nuevaVersion" class="fa fa-upload fa-lg" style="color:green; cursor: pointer;" onclick="divNuevaVersionFormulario(); llamarMetadatosVersionFormulario(document.getElementById('versionMaximaFormulario').value);">    Subir nueva versión</i> 
        </h5></left>
        </h2>
        <div id="consultaMetadatosFormulario">
            
        </div>
        <br/><br/><br/><br/><br/><br/><br/>
        &nbsp; &nbsp;
        {!!Form::button('Actualizar',["class"=>"btn btn-primary", 'id'=>'actualizarF', 'onclick' => 'actualizarFormulario()'])!!} 
        <input id="idRadicadoF" name="idRadicadoF" type="hidden"  value="">
    </div>
<!-- Termina el div de la actualización a la versión -->

<!-- Inicia el div de la nueva versión -->
    
    <div class="col-sm-10" id="nuevaVersionFormulario" style="width: 100%;  float: left; background-color: white; z-index: 1000 ; border: 1px solid; border-color: #ddd; position: absolute; top: -1px; display: none;">
    <a class='cerrar' href='javascript:void(0);' onclick='document.getElementById(&apos;nuevaVersionFormulario&apos;).style.display = &apos;none&apos;';>x</a>
        <h2 id="titulo">
        <left>Adjuntar nueva versión <h5>
        <label class= "col-sm-12 control-label">Versión</label> 
        <div class="input-group">
            <span class="input-group-addon">
                <i class="fa fa-bars"></i>
            </span>

            <input id="numeroVersionFormulario" name="numeroVersionFormulario" style="height:30px; width:80px;" type="text" readonly="true" value="">
        </div>
        </h5></left>
        </h2>
        <div id="consultaMetadatosFormularioNV">
            
        </div>
        <br/><br/><br/><br/><br/><br/><br/>
        &nbsp; &nbsp;
        {!!Form::button('Guardar',["class"=>"btn btn-primary", 'id'=>'guardarFormularioN', 'onclick' => 'guardarFormularioNV()'])!!} 
    </div>
<!-- Termina eldiv de la nueva versión -->
</fieldset>

@stop
