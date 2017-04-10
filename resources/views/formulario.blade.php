@extends('layouts.grid')
@section('titulo')<h3 id="titulo"><center>Formulario</center></h3>@stop

@section('content')
@include('alerts.request')
{!!Html::style('css/divopciones.css'); !!}
{!!Html::script('js/formulario.js'); !!}

<div id='form-section'>

	<fieldset id="consultaradicado-form-fieldset">	
		
        <div class="form-group" id='test'>
            {!!Form::label('Documento_idDocumento', 'Documento', array('class' => 'col-sm-1 control-label'))!!}
            <div class="col-sm-10">
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-search"></i>
                </span>
                {!!Form::select('Documento_idDocumento',$documento, (isset($consultaradicado) ? $consultaradicadoradicado->Documento_idDocumento : 0),["class" => "select form-control", 'onchange' => 'cuerpoGridFormulario(this.value);',"placeholder" =>"Seleccione un documento"])!!}
              </div>
            </div>
          </div>
</br>

<div>
      <iframe id="formularioTr"  name="formularioTr" height="470px" width="100%" style="border:hidden;"></iframe>
</div>
</fieldset>
</div>


<?php 

$divpropiedades = '';
  
$metadatos = DB::table('documentopropiedad')
->leftjoin('documento','documentopropiedad.Documento_idDocumento', "=", 'documento.idDocumento')
->select(DB::raw('documentopropiedad.*,documento.tablaDocumento'))
->where('Documento_idDocumento', "=", 1)
->get();

// for ($i=0; $i < count($metadatos); $i++) 
// { 
//       $nombremetadato = get_object_vars($metadatos[$i]);
      
//       $lista = DB::table('sublista')
//                   ->select (DB::raw('idSubLista, nombreSubLista, Lista_idLista'))
//                   ->where('Lista_idLista', "=", $nombremetadato["Lista_idLista"])
//                   ->get();

//           $idLista = $nombremetadato['Lista_idLista'];

// echo 
// '<div id="ListaSelect'.$idLista.'" class="modal fade" role="dialog">
//   <div class="modal-dialog">

//     <!-- Modal content-->
//     <div class="modal-content" style="width:1200px; left:-300px">
//       <div class="modal-header">
//         <button type="button" class="close" data-dismiss="modal">&times;</button>
//         <h4 class="modal-title">Seleccione un registro de la lista</h4>
//       </div>
//       <div class="modal-body">
//         <div class="container">
//           <div class="row">
//               <div class="container">
                  
                  
//                   <table id="tlistaselect'.$idLista.'" name="tlistaselect'.$idLista.'" class="display table-bordered" width="100%">
//                       <thead>
//                           <tr class="btn-default active">

//                               <th style="width:10px;"><b>Codigo</b></th>
//                               <th style="width:10px;"><b>Nombre</b></th>
//                               <th style="width:10px;"><b>ID</b></th>
//                           </tr>
//                       </thead>
//                       <tfoot>
//                           <tr class="btn-default active">

//                               <th>Codigo</th>
//                               <th>Nombre</th>
//                               <th>ID</th>
//                           </tr>
//                       </tfoot>
//                   </table>
//               </div>
//           </div>
//         </div>
//       </div>
//       <div class="modal-footer">
//         <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
//       </div>
//     </div>

//   </div>
// </div>';
// }


?>
      <!-- Modal -->



<script type="text/javascript">


</script>

@stop