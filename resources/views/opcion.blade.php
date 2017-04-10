@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Opciones del Men&uacute;</center></h3>@stop

@section('content')
@include('alerts.request')

  @if(isset($opcion))
    @if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
      {!!Form::model($opcion,['route'=>['opcion.destroy',$opcion->idOpcion],'method'=>'DELETE', 'files' => true])!!}
    @else
      {!!Form::model($opcion,['route'=>['opcion.update',$opcion->idOpcion],'method'=>'PUT', 'files' => true])!!}
    @endif
  @else
    {!!Form::open(['route'=>'opcion.store','method'=>'POST', 'files' => true])!!}
  @endif

<div id='form-section' >


  <fieldset id="opcion-form-fieldset">  
    <div class="form-group" id='test'>
          {!! Form::label('ordenOpcion', 'Orden', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-barcode"></i>
              </span>
              {!!Form::text('ordenOpcion',null,['class'=>'form-control','placeholder'=>'Ingresa el orden de la opcion en el menu'])!!}
              {!! Form::hidden('idOpcion', null, array('id' => 'idOpcion')) !!}
            </div>
          </div>
        </div>


    
        <div class="form-group" id='test'>
          {!! Form::label('nombreOpcion', 'Nombre', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-pencil-square-o "></i>
              </span>
              {!!Form::text('nombreOpcion',null,['class'=>'form-control','placeholder'=>'Ingresa el nombre de la opcion'])!!}
            </div>
          </div>
        </div>

        <div class="form-group" id='test'>
          {!! Form::label('nombreCortoOpcion', 'Nombre Corto', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-pencil-square-o "></i>
              </span>
              {!!Form::text('nombreCortoOpcion',null,['class'=>'form-control','placeholder'=>'Ingresa el nombre corto de la opcion'])!!}
            </div>
          </div>
        </div>


        <div class="form-group" id='test'>
          {!! Form::label('rutaOpcion', 'Ruta', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-code"></i>
              </span>
              {!!Form::text('rutaOpcion',null,['class'=>'form-control','placeholder'=>'Ingresa la ruta de acceso'])!!}
            </div>
          </div>
        </div>

        <div class="form-group" id='test'>
            {!!Form::label('Paquete_idPaquete', 'Paquete', array('class' => 'col-sm-2 control-label'))!!}
            <div class="col-sm-10">
                    <div class="input-group">
                        <span class="input-group-addon">
                          <i class="fa fa-bars"></i>
                        </span>
                {!!Form::select('Paquete_idPaquete',$paquete, (isset($opcion) ? $opcion->Paquete_idPaquete : 0),["class" => "chosen-select form-control", "placeholder" =>"Seleccione el Paquete"])!!}
              </div>
            </div>
          </div>

      

        <div class="form-group" style="width:250px; display: inline;" >
          {!! Form::label('iconoOpcion', 'Icono', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10" style="width:250px;">
            <div class="panel panel-default">
              <input id="iconoOpcion" name="iconoOpcion" type="file" value="<?php echo (isset($opcion->iconoOpcion) ? 'images/'. $opcion->iconoOpcion : ''); ?>" >
            </div>
          </div>
        </div>



    </fieldset>
  @if(isset($opcion))
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
        

    $('#iconoOpcion').fileinput({
      language: 'es',
      uploadUrl: '#',
      allowedFileExtensions : ['jpg', 'png','gif'],
       initialPreview: [
       '<?php if(isset($opcion->iconoOpcion))
            echo Html::image("images/". $opcion->iconoOpcion,"Imagen no encontrada",array("style"=>"width:148px;height:158px;"));
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
@stop