@extends('layouts.grid')
@section('titulo')<h3 id="titulo"><left>Recalcular cartera</left></h3>@stop
@section('content')
@include('alerts.request')

<div id='form-section' >

	<fieldset id="forward-form-fieldset">	
    <div id="padre" class="col-md-6" style="background-color:#F2F2F2; border:1px solid; border-color:#255986">
        <br>
		    <div class="form-group col-md-8" id='test'>
          {!!Form::label('fechaRecalculo', 'Fecha', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </span>
              {!!Form::text('fechaRecalculo',date("Y-m-d"),['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-4" id='load'>
          
        </div>

        

    <input type="hidden" id="token" value="{{csrf_token()}}"/>

  </div>
</fieldset>
      <br>
      {!!Form::button('Recalcular',["class"=>"btn btn-primary",'id'=>'Recalcular','onclick'=>'recalcularCartera($("#fechaRecalculo").val())'])!!}

	{!! Form::close() !!}
</div>

<script>
  $(document).ready(function(){
    $("#fechaRecalculo").datetimepicker
      (
        ({
           format: "YYYY-MM-DD"
         })
    );  
  });

function recalcularCartera(fecha)
{
  if (fecha == '') 
  {
    alert("Debe seleccionar una fecha");
  }
  else
  {
    var token = document.getElementById('token').value;
      $.ajax({
              headers: {'X-CSRF-TOKEN': token},
              dataType: "json",
              data: {'fecha': fecha},
              url:   'http://'+location.host+'/recalculoCartera/',
              type:  'post',
              beforeSend: function()
              {
                var gif = '<img src="imagenes/load1.gif" height="60" width="60">';
                $("#load").html(gif) ; 
              },
              success: function(respuesta)
              {
                var success = '<img src="imagenes/success.jpg" height="60" width="60">';
                $("#load").html(success); 
              },
              error: function(xhr,err)
              {
                var error = '<img src="imagenes/error.png" height="60" width="60">';
                $("#load").html(error); 
              }
          });
  }
}

</script>
@stop
