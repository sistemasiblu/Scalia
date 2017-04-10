@extends('layouts.grid')
@section('titulo')<h3 id="titulo"><center>Parámetros de Conciliación Contable</center></h3>@stop
@section('content')

{!!Html::script('js/grid.js'); !!}


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

<script type="text/javascript">
    


    
    var modificar = '<?php echo (isset($datos[0]) ? $dato["modificarRolOpcion"] : 0);?>';
    var eliminar = '<?php echo (isset($datos[0]) ? $dato["eliminarRolOpcion"] : 0);?>';
    var consultar = '<?php echo (isset($datos[0]) ? $dato["consultarRolOpcion"] : 0);?>';


    $(document).ready( function () {
        configurarGrid('tdocumentoconciliacion',"{!! URL::to ('/datosDocumentoConciliacion?modificar="+modificar+"&eliminar="+eliminar+"&consultar="+consultar+"')!!}");
    });
</script>



        <div class="container">
            <div class="row">
                <div class="container">
                    <br>
                    <div class="btn-group" style="margin-left: 94%;margin-bottom:4px" title="Columns">
                        <button type="button" class="btn btn-default dropdown-toggle"data-toggle="dropdown">
                            <i class="glyphicon glyphicon-th icon-th"></i> 
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                            <li><a class="toggle-vis" data-column="0"><label> Iconos</label></a></li>
                            <li><a class="toggle-vis" data-column="1"><label> ID</label></a></li>
                            <li><a class="toggle-vis" data-column="2"><label> Código</label></a></li>
                            <li><a class="toggle-vis" data-column="3"><label> Documento</label></a></li>
                            <li><a class="toggle-vis" data-column="3"><label> Grupo</label></a></li>
                        </ul>
                    </div>
                    <table id="tdocumentoconciliacion" name="tdocumentoconciliacion" class="display table-bordered" width="100%">
                        <thead>
                            <tr class="btn-primary active">
                                <th style="width:40px;padding: 1px 8px;" data-orderable="false">
                                 <a href="documentoconciliacion/create"><span style= "color:white; display: <?php echo $visible;?> " class="glyphicon glyphicon-plus"></span></a>
                                 <a href=""><span onclick="recargaPagina();" title="Recargar Pagina" style="color:white" class="glyphicon glyphicon-refresh"></span></a>
                                 <a><span title="Borrar Filtros" class="glyphicon glyphicon-remove-sign" style="color:white; cursor:pointer;" id="btnLimpiarFiltros"></span></a>
                                </th>
                                <th><b>ID</b></th>
                                <th><b>Código</b></th>
                                <th><b>Documento</b></th>
                                <th><b>Grupo</b></th>
                            </tr>
                        </thead>
                                        <tfoot>
                            <tr class="btn-default active">
                                <th style="width:40px;padding: 1px 8px;">
                                    &nbsp;
                                </th>
                                <th>ID</th>
                                <th>Codigo</th>
                                <th>Documento</th>
                                <th>Grupo</th>
                            </tr>
                        </tfoot>        
                    </table>
                </div>
            </div>
        </div>


    
</script>

@stop