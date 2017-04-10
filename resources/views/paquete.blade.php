@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Paquetes del Men&uacute;</center></h3>@stop

@section('content')
  @include('alerts.request')


	@if(isset($paquete))
		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
			{!!Form::model($paquete,['route'=>['paquete.destroy',$paquete->idPaquete],'method'=>'DELETE', 'files' => true])!!}
		@else
			{!!Form::model($paquete,['route'=>['paquete.update',$paquete->idPaquete],'method'=>'PUT', 'files' => true])!!}
		@endif
	@else
		{!!Form::open(['route'=>'paquete.store','method'=>'POST', 'files' => true])!!}
	@endif


<div id='form-section' >

	<fieldset id="paquete-form-fieldset">	
		<div class="form-group" id='test'>
          {!! Form::label('ordenPaquete', 'Orden', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-barcode"></i>
              </span>
              {!!Form::text('ordenPaquete',null,['class'=>'form-control','placeholder'=>'Ingresa el orden del paquete en el menu'])!!}
              {!! Form::hidden('idPaquete', null, array('id' => 'idPaquete')) !!}
            </div>
          </div>
        </div>


		
		    <div class="form-group" id='test'>
          {!! Form::label('nombrePaquete', 'Nombre', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-pencil-square-o "></i>
              </span>
      				{!!Form::text('nombrePaquete',null,['class'=>'form-control','placeholder'=>'Ingresa el nombre del paquete'])!!}
            </div>
          </div>
        </div>

        <div class="form-group" style="width:250px; display: inline;" >
        {!! Form::label('iconoPaquete', 'Icono', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10" style="width:250px;">
            <div class="panel panel-default">
              <input id="iconoPaquete" name="iconoPaquete" type="file" value="<?php echo (isset($paquete->iconoPaquete) ? 'images/'. $paquete->iconoPaquete : ''); ?>" >
            </div>
          </div>
        </div>

       

    </fieldset>
	@if(isset($paquete))
 		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
   			{!!Form::submit('Eliminar',["class"=>"btn btn-primary"])!!}
  		@else
   			{!!Form::submit('Modificar',["class"=>"btn btn-primary"])!!}
  		@endif
 	@else
  		{!!Form::submit('Adicionar',["class"=>"btn btn-primary"])!!}
 	@endif
	{!! Form::close() !!}

  <script type="text/javascript">
    document.getElementById('contenedor').style.width = '1350px';
    document.getElementById('contenedor-fin').style.width = '1350px';
        

    $('#iconoPaquete').fileinput({
      language: 'es',
      uploadUrl: '#',
      allowedFileExtensions : ['jpg', 'png','gif'],
       initialPreview: [
       '<?php if(isset($paquete->iconoPaquete))
            echo Html::image("images/". $paquete->iconoPaquete,"Imagen no encontrada",array("style"=>"width:148px;height:158px;"));
                           ;?>'
            ],
      dropZoneTitle: 'Seleccione el icono',
      removeLabel: '',
      uploadLabel: '',
      browseLabel: '',
      uploadClass: "",
      uploadLabel: "",
      uploadIcon: "",
    });
    </script>

	</div>
</div>
</body>
@stop