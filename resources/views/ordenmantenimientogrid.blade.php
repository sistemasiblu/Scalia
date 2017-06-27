
@extends('layouts.grid')
@section('titulo')<h3 id="titulo"><center>ORDEN MANTENIMIENTO</center></h3>@stop

@section('content')
{!!Html::script('js/grid.js'); !!}
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

<script type="text/javascript">

function recargaPage() 
{
  location.reload();
}

  $(document).ready( function () 
    {
     
     configurarGrid('tordenmantenimiento',"{!! URL::to ('/datosOrdenMantenimiento')!!}");

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
                    <li><a class="toggle-vis" data-column="3"><label> Nombre</label></a></li>
                </ul>
            </div>
            <table id="tordenmantenimiento" name="tordenmantenimiento" class="display table-bordered" width="100%">
                <thead>
                    <tr class="btn-primary active">
                        <th style="width:40px;padding: 1px 8px;" data-orderable="false">
                           <a href="ordenmantenimiento/create"><span class="glyphicon glyphicon-plus"></span></a>
                           <a href="#"><span onclick="recargaPage()" class="glyphicon glyphicon-refresh"></span></a>
                       </th>
                       <th><b>ID</b></th>
                       <th><b>Nombre</b></th>
                       <th><b>Tipo Activo</b></th>
                       <th><b>Tipo Accion</b></th>
                       
                   </tr>
               </thead>
               <tfoot>
                <tr class="btn-default active">
                    <th style="width:40px;padding: 1px 8px;">
                        &nbsp;</th>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Tipo Activo</th>
                    <th>Tipo Accion</th>
                </tr>
            </tfoot>        
        </table>
    </div>
  </div>
</div>





@stop
