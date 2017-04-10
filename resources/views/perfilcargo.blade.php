@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Perfil De Cargo</center></h3>@stop
@section('content')
@include('alerts.request')



@if(isset($perfilcargo))
    @if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
      {!!Form::model($perfilcargo,['route'=>['perfilcargo.destroy',$perfilcargo->idPerfilCargo],'method'=>'DELETE'])!!}
    @else
      {!!Form::model($perfilcargo,['route'=>['perfilcargo.update',$perfilcargo->idPerfilCargo],'method'=>'PUT'])!!}
    @endif
  @else
    {!!Form::open(['route'=>'perfilcargo.store','method'=>'POST'])!!}
  @endif



                          
<div class="perfilcargo-container">
      <form class="form-horizontal" action="" method="post">
         <legend class="text-center"></legend>    

                      <!-- Tipo de la educacion --> 
                  <div class="form-group" id='test'>
                             {!!Form::label('tipoPerfilCargo', 'Tipo', array('class' => 'col-sm-1 control-label')) !!}
                        <div class="col-sm-11">
                            <div class="input-group"> 
                                  <span class="input-group-addon">
                                    <i class="fa fa-bars"></i> 
                                  </span>
                        {!!Form::select('tipoPerfilCargo',
              array('Educacion'=>'Educacion', 'Formacion'=>'Formacion', 'Habilidad'=>'Habilidades propias del cargo'), (isset($tipoPerfilCargo) ? $tipoPerfilCargo->tipoPerfilCargo: 0),["class" => "form-control"])!!}
                                  {!!Form::hidden('idPerfilCargo', null, array('id' => 'idPerfilCargo')) !!}
                                 
                            </div>
                        </div>
                    </div>
                                   <!--  Nombre del Profesional -->

                    <div class="form-group" id='test'>
                                {!!Form::label('nombrePerfilCargo', 'Nombre ', array('class' => 'col-sm-1 control-label')) !!}
                          <div class="col-sm-11">
                            <div class="input-group"> 
                                  <span class="input-group-addon">
                                  <i class="fa fa-pencil-square-o"></i>
                                  </span>
                          {!!Form::text('nombrePerfilCargo',null,['class'=>'form-control','placeholder'=>'Por favor ingrese su Nombre','style'=>'width:100%;,right'])!!}
                                                                  
                        </div>
                       </div>
                    </div>

                     <!-- Observaciones de perfil cargo  -->
                     <div class="form-group" id='test'>
                             {!!Form::label('observacionPerfilCargo', 'Observaciones', array('class' => 'col-sm-1 control-label')) !!}
                          <div class="col-sm-12">
                              <div class="input-group">
                                      <span class="input-group-addon">
                                        <i class="fa fa-commenting-o"></i>
                                      </span>
                                {!!Form::textarea('observacionPerfilCargo',null,['class'=>'form-control','placeholder'=>'','style'=>'width:100%;,right'])!!}
                                                           
                              </div>
                            </br>

                            @if(isset($perfilcargo))
                               @if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
                                  {!!Form::submit('Eliminar',["class"=>"btn btn-primary"])!!}
                                @else
                                  {!!Form::submit('Modificar',["class"=>"btn btn-primary"])!!}
                                @endif
                              @else
                                {!!Form::submit('Guardar',["class"=>"btn btn-primary"])!!}
                              @endif

                             {!! Form::close() !!}
                          </div>


                      </div>  


     
       </form>
       
      

</div> 

    
   @stop
   

        

                   