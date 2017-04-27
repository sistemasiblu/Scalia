
@extends('layouts.vista')
@section('titulo')
  <h3 id="titulo">
    <center>Cambio de Contraseña</center>
  </h3>
@stop

@section('content')
@include('alerts.request')

<title>Scalia</title>
</head>
<body>

{!!Form::open(['route' => 'cambiopassword.update', 'method' => 'PUT'])!!}

<div id='form-section' >
<div class="form-group" id='test'>
{!!Form::label('password', 'Nueva Contraseña:', array('class' => 'col-sm-2 control-label')) !!}
  <div class="col-sm-6">
      <div class="input-group">
        <span class="input-group-addon">
        <i class="fa fa-barcode"></i>
        </span>
        {!!Form::password('password',['required'=>'required','class'=>'form-control','placeholder'=>'Ingresa la contraseña '])!!}
      </div>
  </div>


<br><br><br>
{!!Form::label('cpassword', 'Confirma la Contraseña:', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-6">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-barcode"></i>
              </span>
              {!!Form::password('password_confirmation',['required'=>'required','class'=>'form-control','placeholder'=>'Confirma la contraseña'])!!}
            </div>
          </div>

 <br><br><br>

{!!Form::submit('Modificar',["class"=>"btn btn-primary","onclick"=>'validarFormulario(event);'])!!}
{!! Form::close() !!}
</div>
</body>
</html>
@stop