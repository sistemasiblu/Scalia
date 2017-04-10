{!!Html::script('js/movimientocrm.js'); !!}
{!!Html::script('js/grid.js'); !!}

<?php 
    $TipoEstado = (isset($_GET["TipoEstado"]) ? $_GET["TipoEstado"] : 'Nuevo');

    $visible = '';
    $aprobador = 0;
    if (isset($datos[0])) 
    {
        $dato = get_object_vars($datos[0]);

        $aprobador = (isset($dato['aprobarDocumentoCRMRol']) ? $dato['aprobarDocumentoCRMRol'] : 0);


        if ($dato['adicionarDocumentoCRMRol'] == 1) 
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


// consultamos el tercero asociado al  usuario logueado, para 
// relacionarlo al campo de solicitante

$tercero  = DB::select(
    'SELECT idTercero, nombre1Tercero as nombreCompletoTercero
    FROM '.\Session::get("baseDatosCompania").'.Tercero
    where idTercero = '.\Session::get('idTercero'));
if(count($tercero) == 0)
{   
    $tercero['idTercero'] = null;
    $tercero['nombreCompletoTercero'] = null;
}
else
{
    $tercero = get_object_vars($tercero[0]); 
}



$id = isset($_GET["idDocumentoCRM"]) ? $_GET["idDocumentoCRM"] : 0; 
$campos = DB::select(
    'SELECT codigoDocumentoCRM, nombreDocumentoCRM, nombreCampoCRM,descripcionCampoCRM, 
            mostrarGridDocumentoCRMCampo, relacionTablaCampoCRM, relacionNombreCampoCRM, relacionAliasCampoCRM
    FROM documentocrm
    left join documentocrmcampo
    on documentocrm.idDocumentoCRM = documentocrmcampo.DocumentoCRM_idDocumentoCRM
    left join campocrm
    on documentocrmcampo.CampoCRM_idCampoCRM = campocrm.idCampoCRM
    where   documentocrm.idDocumentoCRM = '.$id.' and
            relacionTablaCampoCRM != "" and 
            mostrarGridDocumentoCRMCampo = 1');

$camposGrid = 'idMovimientoCRM, numeroMovimientoCRM, asuntoMovimientoCRM, DATEDIFF(CURDATE(), fechaSolicitudMovimientoCRM) as diasProceso';
$camposBase = 'idMovimientoCRM, numeroMovimientoCRM, asuntoMovimientoCRM, diasProceso';
$titulosGrid = 'ID, Número, Asunto, Dias Proceso';
for($i = 0; $i < count($campos); $i++)
{
    $datos = get_object_vars($campos[$i]); 
    
    $camposGrid .= ', '. $datos["relacionTablaCampoCRM"].'.'.$datos["relacionNombreCampoCRM"]  .
                     ($datos["relacionAliasCampoCRM"] == null 
                        ? ''
                        : ' As '. $datos["relacionAliasCampoCRM"]);

    $camposBase .= ','.($datos["relacionAliasCampoCRM"] == null 
                        ? $datos["relacionNombreCampoCRM"]
                        : $datos["relacionAliasCampoCRM"]);

    $titulosGrid .= ', '. $datos["descripcionCampoCRM"];
}

// $tercero  = DB::select(
//     'SELECT idTercero, nombreCompletoTercero
//     FROM tercero
//     where idTercero = '.\Session::get('idTercero'));
// $tercero = get_object_vars($tercero[0]); 


?>
@extends('layouts.grid')
@section('titulo')<h3 id="titulo"><center><?php 
echo '('.$datos["codigoDocumentoCRM"].') '.$datos["nombreDocumentoCRM"].'<br>['.
($TipoEstado == '' ? 'Todos' : $TipoEstado).']';?></center></h3>@stop
@section('content')


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
function recargaPage() 
{
location.reload();
}



    var id = "<?php echo $id;?>";
    var camposBase = "<?php echo $camposBase;?>";
    var camposGrid = "<?php echo $camposGrid;?>";

    var lastIdx = null;
    
    var modificar = '<?php echo (isset($dato["modificarDocumentoCRMRol"]) ? $dato["modificarDocumentoCRMRol"] : 0);?>';
    var eliminar = '<?php echo (isset($dato["anularDocumentoCRMRol"]) ? $dato["anularDocumentoCRMRol"] : 0);?>';
    var consultar = '<?php echo (isset($dato["consultarDocumentoCRMRol"]) ? $dato["consultarDocumentoCRMRol"] : 0);?>';
    var aprobar = '<?php echo (isset($dato["aprobarDocumentoCRMRol"]) ? $dato["aprobarDocumentoCRMRol"] : 0);?>';
    var TipoEstado = '<?php echo $TipoEstado;?>';

    $(document).ready( function () {
        configurarGrid('tmovimientocrm',"{!! URL::to ('/datosMovimientoCRM?idDocumento="+id+"&TipoEstado="+TipoEstado+"&modificar="+modificar+"&eliminar="+eliminar+"&consultar="+consultar+"&aprobar="+aprobar+"')!!}");
    });
</script>


        <div class="container">
            <div class="row">
                <div class="container">
                    <a href="#" onclick="cambiarEstado(<?php echo $id;?>,'', modificar, eliminar, consultar, aprobar);" title="Mostrar Todos">
                        <img  src='images/iconoscrm/sin_filtro.png' style="width:28px; height:28px;">
                    <a href="#" onclick="cambiarEstado(<?php echo $id;?>,'Nuevo', modificar, eliminar, consultar, aprobar);" title="Mostrar Nuevas">
                        <img  src='images/iconoscrm/estado_nuevo.png' style="width:28px; height:28px;">
                    </a>
                    <a href="#" onclick="cambiarEstado(<?php echo $id;?>,'Pendiente', modificar, eliminar, consultar, aprobar);" title="Mostrar Pendientes">
                        <img  src='images/iconoscrm/estado_pendiente.png' style="width:28px; height:28px;">
                    </a>
                    <a href="#" onclick="cambiarEstado(<?php echo $id;?>,'En Proceso', modificar, eliminar, consultar, aprobar);" title="Mostrar En Proceso">
                        <img  src='images/iconoscrm/estado_proceso.png' style="width:28px; height:28px;">
                    </a>
                    <a href="#" onclick="cambiarEstado(<?php echo $id;?>,'Cancelado', modificar, eliminar, consultar, aprobar);" title="Mostrar Canceladas / Rechazadas">
                        <img  src='images/iconoscrm/estado_cancelado.png' style="width:28px; height:28px;">
                    </a>
                    <a href="#" onclick="cambiarEstado(<?php echo $id;?>,'Fallido', modificar, eliminar, consultar, aprobar);" title="Mostrar Finalizadas Sin Exito / Fallidas">
                        <img  src='images/iconoscrm/estado_fallido.png' style="width:28px; height:28px;">
                    </a>
                    <a href="#" onclick="cambiarEstado(<?php echo $id;?>,'Exitoso', modificar, eliminar, consultar, aprobar);" title="Mostrar Finalizadas Con Exito / Exitosas">
                        <img  src='images/iconoscrm/estado_exitoso.png' style="width:28px; height:28px;">
                    </a>
                    <a style="float: right;" href="#" onclick="mostrarTableroCRM(<?php echo $id;?>);" title="Mostrar Nuevas">
                        <img  src='images/iconoscrm/dashboardcrm.png' style="width:36px; height:36px;">
                    </a>
                                 
                    <div class="btn-group" style="margin-left: 94%;margin-bottom:4px" title="Columns">

                        <button type="button" class="btn btn-default dropdown-toggle"data-toggle="dropdown">
                            <i class="glyphicon glyphicon-th icon-th"></i> 
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                            <li><a class="toggle-vis" data-column="0" ><label> Iconos</label></a></li>
                            <?php 
                                $titulos = explode(',', $titulosGrid);
                                for($i = 0; $i < count($titulos); $i++)
                                {
                                    echo '<li><a class="toggle-vis" data-column="'.($i+1).'"><label> '.$titulos[$i].'</label></a></li>';
                                }
                            ?>

                           
                        </ul>
                    </div>
                    <div class="col-md-12" style="overflow: auto;">
                    <table id="tmovimientocrm" name="tmovimientocrm" class="display table-bordered" width="100%">
                        <thead>
                            <tr class="btn-warning active">

                            <th style="width: 100px; padding: 1px 8px;" data-orderable="false">
                                <a href=<?php echo "movimientocrm/create?idDocumentoCRM=".$id."&aprobador=".$aprobador;?>><span title="Agregar" style= "display: <?php echo $visible;?> color:white " class="glyphicon glyphicon-plus"></span></a>
                                 <a href=""><span onclick="recargaPage();" title="Recargar Pagina" style="color:white" class="glyphicon glyphicon-refresh"></span></a>
                                 <a><span title="Borrar Filtros" class="glyphicon glyphicon-remove-sign" style="color:white; cursor:pointer;" id="btnLimpiarFiltros"></span></a>
                                </th>
                                <?php 
                                    for($i = 0; $i < count($titulos); $i++)
                                    {
                                        echo '<th><b>'.$titulos[$i].'</b></th>';
                                    }
                                ?>
                            </tr>
                        </thead>
                                        <tfoot>
                            <tr class="btn-default active">
                                <th style="width:40px;padding: 1px 8px;">
                                    &nbsp;
                                </th>
                                <?php 
                                    for($i = 0; $i < count($titulos); $i++)
                                    {
                                        echo '<th>'.$titulos[$i].'</th>';
                                    }
                                ?>
                            </tr>
                        </tfoot>        
                    </table>
                    </div>
                </div>
            </div>
        </div>



@stop

<div id="ModalAsesor" class="modal fade" role="dialog" style="display:none;">
  <div class="modal-dialog" style="width:70%;">

    <!-- Modal content-->
    <div style="" class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Asignación de Asesor</h4>
      </div>
      <div class="modal-body">
        <div class="container col-md-12"  style="height:200px;">

            <div class="col-sm-12">
                <div class="col-sm-4">
                    {!!Form::label('Tercero_idSupervisor', 'Supervisor', array())!!}
                </div>
                <div class="col-sm-8">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-pencil-square-o"></i>
                        </span>
                        <input type="hidden" id="token" value="{{csrf_token()}}"/>
                        {!!Form::hidden('idMovimientoCRM',null, array("id"=>"idMovimientoCRM"))!!}
                        {!!Form::hidden('Tercero_idSupervisor',$tercero["idTercero"] , array("id"=>"Tercero_idSupervisor"))!!}
                        {!!Form::text('nombreCompletoSupervisor',$tercero["nombreCompletoTercero"],['class'=>'form-control', 'readonly'=>'readonly'])!!}
                    </div>
                </div>
            </div>
            
            <div class="col-sm-12">
                <div class="col-sm-4">
                    {!!Form::label('Tercero_idAsesor', 'Asesor', array())!!}
                </div>
                <div class="col-sm-8">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-pencil-square-o"></i>
                        </span>
                        {!!Form::select('Tercero_idAsesor',$asesores, (isset($movimientocrm) ? $movimientocrm->Tercero_idAsesor : 0),["placeholder"=>"Seleccione","class" => "chosen-select form-control"])!!}

                    </div>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="col-sm-4">
                    {!!Form::label('AcuerdoServicio_idAcuerdoServicio', 'Acuerdo de Servicio', array())!!}
                </div>
                <div class="col-sm-8">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-pencil-square-o"></i>
                        </span>
                        {!!Form::select('AcuerdoServicio_idAcuerdoServicio',$acuerdoservicio, (isset($movimientocrm) ? $movimientocrm->AcuerdoServicio_idAcuerdoServicio : 0),["onchange"=>"mostrarDiasAcuerdo(this.value)","class" => "chosen-select form-control"])!!}

                    </div>
                </div>
            </div>
            
            <div class="col-sm-12">
                <div class="col-sm-4">
                    {!!Form::label('diasEstimadosSolucionMovimientoCRM', 'Días Est. Solución', array())!!}
                </div>
                <div class="col-sm-8">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-pencil-square-o"></i>
                        </span>
                        {!!Form::text('diasEstimadosSolucionMovimientoCRM',null,['readonly'=>'readonly', 'class'=>'form-control','placeholder'=>'Segun Acuerdo de Servicio'])!!}
                    </div>
                </div>
            </div>
                       


        </div>

      </div>
       <div class="modal-footer">
        
            <button type="button" class="btn btn-primary"  data-dismiss="modal" onclick="guardarAsesor();">Actualizar</button>
            <button type="button" class="btn btn-danger"  data-dismiss="modal">Cancelar</button>

      </div>
    </div>
  </div>
</div>
