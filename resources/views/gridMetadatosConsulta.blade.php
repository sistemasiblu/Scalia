@extends('layouts.modal') 
@section('titulo')<h3 id="titulo"><center></h3>@stop
@section('content')
@include('alerts.request')
{!!Html::style('css/divopciones.css'); !!}
{!!Html::script('js/consultaradicado.js'); !!}
{!!Html::style('css/BootSideMenu.css'); !!}
{!!Html::script('js/BootSideMenu.js'); !!}
{!!Html::script('js/dropzoneConsulta.js'); !!}<!--Llamo al dropzone-->
{!!Html::style('assets/dropzone/dist/min/dropzone.min.css'); !!}<!--Llamo al dropzone-->
{!!Html::style('css/dropzone.css'); !!}<!--Llamo al dropzone-->

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
$consultaMetadatos = DB::table('documentopropiedad')
->leftjoin('metadato','documentopropiedad.Metadato_idMetadato', "=", 'metadato.idMetadato')
->select(DB::raw('tituloMetadato'))
->where ('Documento_idDocumento', "=", $_GET['idDoc'])
->where('gridDocumentoPropiedad', "=", 1)
->get();

$idDoc = $_GET['idDoc'];
$consulta = $_GET['consulta'];
?>

<div  class="col-sm-10" id="all" style="width: 140px; height:20px; left:90px; top: 30px;">
<i id="descargarMasivo" style="cursor: pointer;"  class="fa fa-download fa-lg"></i>
&nbsp;
<i id="enviarMasivo" style="cursor: pointer;" onclick="activarEmail();" class="fa fa-envelope-o fa-lg"></i>
&nbsp;
<i id="imprimirMasivo" style="cursor: pointer;" class="fa fa-print fa-lg "></i>
&nbsp;
<i id="eliminarMasivo" style="cursor: pointer;" class="fa fa-trash fa-lg"></i>
</div>
    <div id="grid">
        <div class="container">
            <div class="row">
                <div class="btn-group" style="margin-left: 94%;margin-bottom:4px" title="Columns">
                    <button type="button" class="btn btn-default dropdown-toggle"data-toggle="dropdown">
                        <i class="glyphicon glyphicon-th icon-th"></i> 
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                        <li><a class="toggle-vis" data-column="0"><label> Iconos</label></a></li>
                        <li><a class="toggle-vis"  data-column="1"><label> ID Radicado</label></a></li>
                        <?php 
                        for($i = 0; $i < count($consultaMetadatos); $i++)
                        {
                          $data = $i + 2;
                          $metadatos = get_object_vars($consultaMetadatos[$i]);
                          echo '<li><a class="toggle-vis" data-column="'.$data.'"><label>'.$metadatos["tituloMetadato"].'</label></a></li>';
                        }
                        echo'</ul>';
                        ?>
                </div>
                  <table id="tconsultaradicado" name="tconsultaradicado" class="display table-bordered" width="100%">
                    <thead>
                        <tr>
                        </tr>
                        <tr class="btn-primary active">
                            <th style="width:40px;padding: 1px 8px;" data-orderable="false">
                                <a href="#"><span style="color:white" class="glyphicon glyphicon-refresh"></span></a>
                                <th><b>ID Radicado</b></th>
                            </th>
                                <?php
                                for($i = 0; $i < count($consultaMetadatos); $i++)
                                {
                                  $metadatos = get_object_vars($consultaMetadatos[$i]);
                                  echo'<th><b>'.$metadatos["tituloMetadato"].'</b></th>';
                                }
                                ?>
                        </tr>
                    </thead>
                        <tfoot>
                            <tr class="btn-default active">
                                <th style="width:40px;padding: 1px 8px;">
                                    &nbsp;
                                </th>
                                <th >ID Radicado</th>
                                <?php
                                for($i = 0; $i < count($consultaMetadatos); $i++)
                                 {
                                  $metadatos = get_object_vars($consultaMetadatos[$i]);
                                   echo'<th>'.$metadatos["tituloMetadato"].'</th>';
                                 }
                                ?>
                            </tr>
                        </tfoot>
                    </table> 
            </div>
        </div>
    </div>
        
    <div class="col-sm-12" id="preview" style="width: 100%; height:100%; background-color: white; z-index: 1000 ; border: 1px inset; border-color: #ddd; position: absolute; top: -1px; display: none;">
        <a class='cerrar' href='javascript:void(0);' onclick='cerrarEmail(); document.getElementById(&apos;preview&apos;).style.display = &apos;none&apos;'>x</a> <!--Es la funcion la cual cierra el div flotante-->
        </br>
         <div class="col-md-12">
            <input id="archivoRadicado" name="archivoRadicado" type="hidden" value="">
            <input id="idDoc" name="idDoc" type="hidden" value="<?php echo $idDoc; ?>">
            <input id="Radicado" name="Radicado" type="hidden" value="">
                <h2 id="titulo">
                    <left>Consulta<h4>
                        <select id="versionRadicadoMaxima" name="versionRadicadoMaxima" onchange="llamarMetadatos(document.getElementById('Radicado').value,this.value); llamarPreview(document.getElementById('Radicado').value, document.getElementById('idDoc').value,this.value)"> 
                        </select>
                    </h4></left>
                </h2>
                <!-- Inserto los metadatos -->
                <div id="metadatos" >
                      
                </div>
                </br> </br> </br> </br> </br> </br></br> </br> </br></br> </br> </br>
                {!!Form::button('Modificar',["class"=>"btn btn-primary", 'id'=>'enviarEdit', 'style' => 'display:none;', 'onclick' => 'actualizarDatos()'])!!}
        </div>
              
<script>
$(document).ready(function(){
    $('#vistaprev').BootSideMenu({side:"right"});
    $('#botones').BootSideMenu({side:"left"});
  });
</script>

<div id="botones" style="width=100%; height:100%; position:absolute;">
<a id="cargar" onclick="llamarMetadatosVersion(document.getElementById('versionRadicadoMaxima').value); activarDivVersion();" class="fa fa-upload fa-2x"></a>
<br/> <br/> 
<a id="descargar" class="fa fa-download fa-2x" style="cursor: pointer;" onclick="accionArchivo('descargar');"></a>
<br/> <br/> <br/> 
<a class="fa fa-pencil fa-2x" style="cursor: pointer;" onclick="activarEdit();"></a>
<br/> <br/> <br/> 
<a class="fa fa-envelope-o fa-2x" style="cursor: pointer;" onclick="activarEmail();"></a>
<br/> <br/> <br/> 
<a id="imprimir" class="fa fa-print fa-2x" style="cursor: pointer;" onclick="accionArchivo('imprimir');"></a>
<br/> <br/> <br/> 
<a class="fa fa-trash fa-2x" style="cursor: pointer;" onclick="accionArchivo('eliminar');"></a>
</div>
    <div id="vistaprev" class="col-md-6" style="top: 15px;" >
        <div id="vistaPrevia" style="width=100%; height:490px; top: 189px;">

        </div>
    </div>
</div>

<input type="hidden" id="token" value="{{csrf_token()}}"/>

<!-- Div de envío del email -->
<div class="col-sm-12" id="email" style="width: 100%; height:408px; background-color: white; z-index: 1000 ; border: 1px solid; border-color: #ddd; position: absolute; top: 30px; display: none;">
<a class='cerrar' href='javascript:void(0);' onclick='document.getElementById(&apos;email&apos;).style.display = &apos;none&apos;';>x</a> <!--Es la funcion la cual cierra el div flotante-->
<div class="form-group" id='test'>
</br>
</br>

<div>
    {!!Form::label('correo', 'Email', array('class' => 'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        <div class="input-group">
            <span class="input-group-addon">
                <i class="fa fa-at"></i>
            </span>
            {!!Form::text('correo',null,['class'=>'form-control','placeholder'=>'Destinatario'])!!}
        </div>
    </div>
</div>

<div>
    {!!Form::label('asunto', 'Asunto', array('class' => 'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        <div class="input-group">
            <span class="input-group-addon">
                <i class="fa fa-pencil-square-o"></i>
            </span>
            {!!Form::text('asunto',null,['class'=>'form-control','placeholder'=>'Asunto'])!!}
        </div>
    </div>
</div>

<div>
    {!!Form::label('mensaje', 'Mensaje', array('class' => 'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        <div class="input-group">
            <span class="input-group-addon">
                <i class="fa fa-envelope"></i>
            </span>
            {!!Form::textarea('mensaje',null,['class'=>'form-control','style'=>'height:150px','placeholder'=>'Mensaje'])!!}
        </div>
    </div>
</div>

<div>
    {!!Form::label('adjunto', 'Adjunto', array('class' => 'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        <div class="input-group">
            <span class="input-group-addon">
                <i class="fa fa-file-archive-o"></i>
            </span>
            {!!Form::text('adjunto',null,['class'=>'form-control', 'readonly', 'placeholder'=>'Adjunto'])!!}
        </div>
    </div>
</div>
        <!-- Boton de enviar email con los parametros correspondientes -->
        {!!Form::button('Enviar',['class'=>'btn btn-primary', 'onclick' => 'enviarMail(document.getElementById(\'correo\').value, document.getElementById(\'asunto\').value, document.getElementById(\'mensaje\').value, document.getElementById(\'archivoRadicado\').value)', 'id'=>'enviarEmail' ])!!}
    </div>
</div>
<!-- Termina div de envío de email -->

<!-- Script de la grid -->
<script type="text/javascript">
    $(document).ready( function () {

        
        /*$('#tconsultaradicado').DataTable({
            "aProcessing": true,
            "aServerSide": true,
            "stateSave":true,
            "ajax": "{!! URL::to ('/datosMetadatos')!!}",
        });*/
        var lastIdx = null;
        var idDoc = "<?php echo $_GET['idDoc'];?>";
        var consulta = "<?php echo $_GET['consulta'];?>";
        var table = $('#tconsultaradicado').DataTable( {
            "order": [[ 1, "asc" ]],
            "aProcessing": true,
            "aServerSide": true,
            "stateSave":true,
            "ajax": "{!! URL::to ('/datosMetadatos?idDoc="+idDoc+"&consulta="+consulta+"')!!}",
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

        $('#tconsultaradicado tbody')
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
    $('#tconsultaradicado tfoot th').each( function () {
        if($(this).index()>0){
        var title = $('#tconsultaradicado thead th').eq( $(this).index() ).text();
        $(this).html( '<input type="text" placeholder="Buscar por '+title+'" />' );
        }
    } );
 
    // DataTable
    var table = $('#tconsultaradicado').DataTable();
 
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

     $('#tconsultaradicado tbody').on( 'click', 'tr', function () {
        $(this).toggleClass('selected');
    } );
 
    $('#descargarMasivo, #enviarMasivo, #imprimirMasivo, #eliminarMasivo').click( function () {
        var datos = table.rows('.selected').data();
        var ids = "";
        for (var i = 0; i < datos.length; i++) 
        {
            ids += datos[i][1]+',';
        };
        ids= ids.substring(0,ids.length-1); //quitar el ultimo caracter, en este caso la coma (,)
        accionArchivoMasivo(this.id, ids)
        // alert(ids);

    });
});    
</script>
<!-- Termina el script de la grid -->
</fieldset>

    <!-- Modal de etiquetas -->
    <div id="myModalEtiqueta" class="modal fade" role="dialog">
      <div class="modal-dialog" style="width:1000px;">
        <div style="" class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Selecciona las etiquetas</h4>
          </div>
          <div class="modal-body">
            <iframe style="width:100%; height:510px; z-index: 964790"; id="etiqueta" name="etiqueta" src=<?php $ip?>"/etiquetaselect"> </iframe> 
          </div>
        </div>
      </div>
    </div>
    <!-- Termina el modal de etiquetas -->

<!-- Inicia div version nueva -->
<div class="col-sm-12" id="version" style="width: 100%; height:520px; background-color: white; z-index: 1000 ; border: 1px solid; border-color: #ddd; position: absolute; top: -1px; display: none;">
    <a class='cerrar' href='javascript:void(0);' onclick='document.getElementById(&apos;version&apos;).style.display = &apos;none&apos;';>x</a> 

    <h2 id="titulo">
    <left>Adjuntar nueva versión <h5>
    <label class= "col-sm-12 control-label">Versión</label> 
    <div class="input-group">
        <span class="input-group-addon">
            <i class="fa fa-bars"></i>
        </span>

        <input id="numeroVersion" style="height:30px; width:80px;" type="text" readonly="true" value="">
    </div>
    </h5></left>
    </h2>

    <!-- Inserto los metadatos al div version -->
    <div id="divVersion" class="col-md-8">

    </div>

    <!-- Div para adjuntar el nuevo archivo en un dropzone que solo permite un archivo -->
    <div id="upload" class="col-md-4">
        <label class= "col-sm-12 control-label">Cargar nueva versión</label>
        </br> </br> 
        <div class="input-group">  
            <input type="hidden" id="archivoNuevaVersion" name="archivoNuevaVersion" value="0">
            <div class="form-group">
                <div class="input-group">
                    <div class="dropzone dropzone-previews" id="dropzoneVersion"></div>  
                </div>
            </div>
        </div>
    </div>
</br> </br>

<!-- Script para el dropzone -->
<script type="text/javascript">
    var baseUrl = "{{ url("/") }}";
    var token = "{{ Session::getToken() }}";
    Dropzone.autoDiscover = false;
   //Le doy un nombre al dropzone (id)
    var myDropzone = new Dropzone
    ("div#dropzoneVersion",
    {
        url: baseUrl + "/dropzone/uploadFiles",
        params: {
            _token: token
        },
        
    });

    //Configuro el dropzone
    myDropzone.options.dropzoneVersion =  
    {
    paramName: "file", // The name that will be used to transfer the file
    maxFile: 1, // MB
    addRemoveLinks: true,
    clickable: true,
    previewsContainer: ".dropzone-previews",
    clickable: false,
    uploadMultiple: false,
    accept: function(file, done) 
    {

    }
    };

    //envio las funciones al realizar cuando se de clic en la vista previa dentro del dropzone
     myDropzone.on("addedfile", function(file) 
     {
        file.previewElement.addEventListener("click", function(reg, idDoc) 
        {
            var idDrop = this.parentNode.id; 
            Radicar(file, idDrop);
        });
    });

     myDropzone.on('success', function(file) {
            Radicar(file, '');
    });


     function Radicar(file, idDrop)
     {
        if(file != '')
        {
            document.getElementById("archivoNuevaVersion").value = file["name"]; //Envio el nombre del archivo   

        }
        else
        {
          document.getElementById("archivoNuevaVersion").value = ''; 
        }
    }     
</script>
<!-- Cierro el script -->
{!!Form::button('Cargar',["class"=>"btn btn-primary", 'id'=>'cargarVersion', 'onclick'=> 'actualizarDatos()'])!!}
<!-- Le doy tamaño al dropzone -->
<style>
#dropzoneVersion {
width: 400px;
height: 200px;
min-height: 0px !important;
}   
</style>
</div>
<!-- Cierro el div de la versión nueva -->
@stop
