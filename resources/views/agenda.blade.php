@extends('layouts.calendario')
@section('titulo')<h3 id="titulo"><center>Agenda</center></h3>@stop

@section('content')
@include('alerts.request')

   @if(isset($agenda))
    @if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
      {!!Form::model($agenda,['route'=>['agenda.destroy',$agenda->idAgenda],'method'=>'DELETE'])!!}
    @else
      {!!Form::model($agenda,['route'=>['agenda.update',$agenda->idAgenda],'method'=>'PUT'])!!}
    @endif
  @else
    {!!Form::open(['route'=>'agenda.store','method'=>'POST'])!!}
  @endif

<?php 

$categoria = DB::Select('
    SELECT
        codigoCategoriaAgenda, 
        colorCategoriaAgenda
    FROM
        categoriaagenda');

echo '<style type="text/css">';
for ($i=0; $i < count($categoria); $i++) 
{ 
    $datoCategoria = get_object_vars($categoria[$i]);

    echo '.'.$datoCategoria["codigoCategoriaAgenda"].'
              {
                background-color: '.$datoCategoria["colorCategoriaAgenda"].';
              }';
}
echo '</style>';

?>


<div id='form-section'>
<input type="hidden" id="token" value="{{csrf_token()}}"/>
  <fieldset id="agenda-form-fieldset"> 

    <div class="row">
        <button type="button" onclick="agregarEvento()" class="btn btn-primary">Añadir evento</button>

      <div class="page-header">
        <div class="pull-right form-inline">
          <div class="btn-group">
            <button type="button" class="btn btn-primary" data-calendar-nav="prev"><< Anterior</button>
            <button type="button" class="btn" data-calendar-nav="today">Hoy</button>
            <button type="button" class="btn btn-primary" data-calendar-nav="next">Siguiente >></button>
          </div>
          <div class="btn-group">
            <button type="button" class="btn btn-warning" data-calendar-view="year">Año</button>
            <button type="button" class="btn btn-warning active" data-calendar-view="month">Mes</button>
            <button type="button" class="btn btn-warning" data-calendar-view="week">Semana</button>
            <button type="button" class="btn btn-warning" data-calendar-view="day">Día</button>
          </div>
        </div>
      </div>  
    </div>
    <label class="checkbox">
        <input type="checkbox" value="#events-modal" id="events-in-modal"> Abrir eventos en una ventana modal
    </label>  

    <div class="row">
      <div id="calendar"></div>
    </div>

  </fieldset>

  {!! Form::close() !!}
</div>
<script type="text/javascript">

function agregarEvento()
{
    $('#modalEvento').modal('show');
}

function consultarCamposAgenda(idCategoriaAgenda, idAgenda)
{
    var token = document.getElementById('token').value;

    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        dataType: "json",
        data: {'idCategoriaAgenda' : idCategoriaAgenda, 'idAgenda': idAgenda},
        url:   'http://'+location.host+'/mostrarCamposAgenda/',
        type:  'post',
        success: function(respuesta)
        {
            // alert(respuesta.toSource());
            // $("#claseAgenda").val(respuesta[0]['codigoCategoriaAgenda']);

            for (var i = 0; i < respuesta.length; i++) 
            {
                if (respuesta[i]['nombreCampoCRM'] == 'ubicacionAgenda') 
                    $("#ubicacion").css('display','block');

                if (respuesta[i]['nombreCampoCRM'] == 'MovimientoCRM_idMovimientoCRM') 
                    $("#MovimientoCRM").css('display','block');

                if (respuesta[i]['nombreCampoCRM'] == 'Tercero_idResponsable') 
                    $("#Tercero").css('display','block');

                if (respuesta[i]['nombreCampoCRM'] == 'porcentajeEjecucionAgenda') 
                    $("#porcentajeEjecucion").css('display','block');

                if (respuesta[i]['nombreCampoCRM'] == 'estadoAgenda') 
                    $("#estado").css('display','block');

                if (respuesta[i]['nombreCampoCRM'] == 'seguimientoAgenda') 
                    $("#divseguimiento").css('display','block');
                    $("#liseguimiento").css('display','block');

                if (respuesta[i]['nombreCampoCRM'] == 'Tercero_idAsistente') 
                    $("#divasistentes").css('display','block');
                    $("#liasistentes").css('display','block');
            }
        },
        error: function(xhr,err)
        { 
            $("#claseAgenda").val('');

            $("#ubicacionAgenda").css('display','none');

            $("#MovimientoCRM_idMovimientoCRM").css('display','none');

            $("#Tercero_idResponsable").css('display','none');

            $("#porcentajeEjecucionAgenda").css('display','none');

            $("#estadoAgenda").css('display','none');

            $("#liseguimiento").css('display','none');
            
            $("#liasistentes").css('display','none');
        }
    });
}

$(document).ready(function(){

//creamos la fecha actual

    if ($('#CategoriaAgenda_idCategoriaAgenda').val() > 0) 
    {
        consultarCamposAgenda($('#CategoriaAgenda_idCategoriaAgenda').val());
    }
       

        var date = new Date();
        var yyyy = date.getFullYear().toString();
        var mm = (date.getMonth()+1).toString().length == 1 ? "0"+(date.getMonth()+1).toString() : (date.getMonth()+1).toString();
        var dd  = (date.getDate()).toString().length == 1 ? "0"+(date.getDate()).toString() : (date.getDate()).toString();

        //establecemos los valores del calendario
        var options = {
            events_source: 'http://'+location.host+'/getAll',
            view: 'month',
            language: 'es-ES',
            tmpl_path: 'http://'+location.host+'/bower_components/bootstrap-calendar/tmpls/',
            tmpl_cache: false,
            day: yyyy+"-"+mm+"-"+dd,
            time_start: '10:00',
            time_end: '20:00',
            time_split: '30',
            width: '100%',
            onAfterEventsLoad: function(events) 
            {
                if(!events) 
                {
                    return;
                }
                var list = $('#eventlist');
                list.html('');

                $.each(events, function(key, val) 
                {
                    $(document.createElement('li'))
                        .html('<a href="' + val.url + '">' + val.title + '</a>')
                        .appendTo(list);
                });
            },
            onAfterViewLoad: function(view) 
            {
                $('.page-header h3').text(this.getTitle());
                $('.btn-group button').removeClass('active');
                $('button[data-calendar-view="' + view + '"]').addClass('active');
            },
            classes: {
                months: {
                    general: 'label'
                }
            }
        };

        var calendar = $('#calendar').calendar(options);

        $('.btn-group button[data-calendar-nav]').each(function() 
        {
            var $this = $(this);
            $this.click(function() 
            {
                calendar.navigate($this.data('calendar-nav'));
            });
        });

        $('.btn-group button[data-calendar-view]').each(function() 
        {
            var $this = $(this);
            $this.click(function() 
            {
                calendar.view($this.data('calendar-view'));
            });
        });

        $('#first_day').change(function()
        {
            var value = $(this).val();
            value = value.length ? parseInt(value) : null;
            calendar.setOptions({first_day: value});
            calendar.view();
        });

        $('#events-in-modal').change(function()
        {
            var val = $(this).is(':checked') ? $(this).val() : null;
            calendar.setOptions(
                {
                    modal: val,
                    modal_type:'iframe'
                }
            );
        });
    // }(jQuery));
});
</script>
@stop
<div id="modalEvento" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width:70%;">

    <!-- Modal content-->
    <div style="" class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Crear un nuevo evento</h4>
      </div>
      <div class="modal-body">
      <?php 
        echo '<iframe style="width:100%; height:400px; " id="campos" name="campos" src="http://'.$_SERVER["HTTP_HOST"].'/eventoagenda"></iframe>'
      ?>
      </div>
    </div>
  </div>
</div>


<!--ventana modal para el calendario-->
    <div class="modal fade" id="events-modal">
        <div class="modal-dialog" style="width:70%;">
          <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Agenda</h4>
              </div>
            <div class="modal-body" style="height: 400px">
                
            </div>
          </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->