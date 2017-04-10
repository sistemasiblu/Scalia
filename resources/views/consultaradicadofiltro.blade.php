@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Consulta de radicado</center></h3>@stop

@section('content')
@include('alerts.request')

@if(isset($consultaradicadofiltro))
  @if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
    {!!Form::model($consultaradicadofiltro,['route'=>['consultaradicadofiltro.destroy',$consultaradicadofiltro->idConsultaRadicadoFiltro],'method'=>'DELETE'])!!}
  @else
    {!!Form::model($consultaradicadofiltro,['route'=>['consultaradicadofiltro.update',$consultaradicadofiltro->idConsultaRadicadoFiltro],'method'=>'PUT'])!!}
  @endif
@else
  {!!Form::open(['route'=>'consultaradicadofiltro.store','method'=>'POST'])!!}
@endif

<script>
    var idDocumento = '<?php echo isset($idDocumento) ? $idDocumento : "";?>';
    var nombreDocumento = '<?php echo isset($nombreDocumento) ? $nombreDocumento : "";?>';

    var datodocumento = [JSON.parse(idDocumento), JSON.parse(nombreDocumento)];

    var valorDocumento = [0];

    $(document).ready(function(){

      documento = new Atributos('documento','contenedor_documento','documento_');

      documento.altura = '35px';
      documento.campoid = '';
      documento.campoEliminacion = '';

      documento.campos   = ['Documento_idDocumento'];
      documento.etiqueta = ['select'];
      documento.tipo     = [''];
      documento.estilo   = ['width: 540px;height:35px;'];
      documento.clase    = ['chosen-select'];
      documento.opciones = [datodocumento];
      documento.sololectura = [true];
    });

  </script>

  <script>
    var idDependencia = '<?php echo isset($idDependencia) ? $idDependencia : "";?>';
    var nombreDependencia = '<?php echo isset($nombreDependencia) ? $nombreDependencia : "";?>';

    var datodependencia = [JSON.parse(idDependencia), JSON.parse(nombreDependencia)];

    var valorDependencia = [0];

    $(document).ready(function(){

      dependencia = new Atributos('dependencia','contenedor_dependencia','dependencia_');

      dependencia.altura = '35px';
      dependencia.campoid = '';
      dependencia.campoEliminacion = '';

      dependencia.campos   = ['Dependencia_idDependencia'];
      dependencia.etiqueta = ['select'];
      dependencia.tipo     = ['',];
      dependencia.estilo   = ['width: 540px;height:35px;'];
      dependencia.clase    = ['chosen-select'];
      dependencia.sololectura = [true];
      dependencia.opciones = [datodependencia];
    });

  </script>

  <script>

    var idMetadato = '<?php echo isset($idMetadato) ? $idMetadato : "";?>';
    var tituloMetadato = '<?php echo isset($tituloMetadato) ? $tituloMetadato : "";?>';

    var parentesisAbre = [["", "(", "((", "(((", "(((("], ["", "(", "((", "(((", "(((("]];
    var parentesisCierra = [["", ")", "))", ")))", "))))"], ["", ")", "))", ")))", "))))"]];

    var operador = [["=", ">", ">=", "<", "<=", "like"],
                    ["Igual a", "Mayor que", "Mayor o igual", "Menor que", "Menor o igual que", "Contiene"]];

    var datometadato = [JSON.parse(idMetadato), JSON.parse(tituloMetadato)];

    var valorMetadato = ['','','','','',''];
    
    var conector =  [["AND", "OR"], ["Y", "O"]];

    $(document).ready(function(){
      metadato = new Atributos('metadato','contenedor_metadato','metadato_');
      metadato.campos   = ['parentesisIniciometadato', 'campometadato', 'operadormetadato','valormetadato','parentesisFinmetadato','conectormetadato'];
      metadato.etiqueta = ['select', 'select','select','input','select','select'];
      metadato.tipo     = ['','','','text','',''];
      metadato.opciones = [parentesisAbre,datometadato,operador,'',parentesisCierra,conector];
      metadato.estilo   = ['width: 120px;height:35px;','width: 300px;height:35px;','width: 190px;height:35px;','width: 220px;height:35px;','width: 130px;height:35px;','width: 130px;height:35px;'];
      metadato.clase    = ['','','','','',''];
      metadato.sololectura = [false,false,false,false,false,false];
    });
  </script>

<div id='form-section'>

  
  <fieldset id="consultaradicadofiltro-form-fieldset"> 
      
    <div class="col-md-6">
      <div class="panel panel-primary">
        <div class="panel-heading">
          <h4 class="panel-title">
            <a>Documento</a>
          </h4>
        </div>
        <div class="panel-body">
          <div class="form-group" id='test'>
            <div class="col-sm-12">
              <div class="row show-grid">
                <div class="col-md-1" style="width: 40px; cursor: pointer;" onclick="documento.agregarCampos(valorDocumento,'A');">
                  <span class="glyphicon glyphicon-plus"></span>
                </div>
                <div class="col-md-1" style="width: 540px;">Documento</div>
                <div id="contenedor_documento"> 
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="panel panel-primary">
        <div class="panel-heading">
          <h4 class="panel-title">
            <a>Dependencia</a>
          </h4>
        </div>
        <div class="panel-body">
          <div class="form-group" id='test'>
            <div class="col-sm-12">
              <div class="row show-grid">
                <div class="col-md-1" style="width: 40px; cursor: pointer;" onclick="dependencia.agregarCampos(valorDependencia,'A');">
                  <span class="glyphicon glyphicon-plus"></span>
                </div>
                <div class="col-md-1" style="width: 540px;">Dependencia</div>
                <div id="contenedor_dependencia"> 
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-12">
        <div class="panel panel-primary">
          <div class="panel-heading">
            <h4 class="panel-title">
              <a>Condición</a>
            </h4>
          </div>
          <div class="panel-body">
            <div class="form-group" id='test'>
              <div class="col-sm-12">
                <div class="row show-grid">
                  <div class="col-md-1" style="width: 40px; cursor: pointer;" onclick="metadato.agregarCampos(valorMetadato,'A')">
                    <span class="glyphicon glyphicon-plus"></span>
                  </div>
                  <div class="col-md-1" style="width: 120px;">Agrupador</div>
                  <div class="col-md-1" style="width: 300px;">Campo</div>
                  <div class="col-md-1" style="width: 190px;">Operador</div>
                  <div class="col-md-1" style="width: 220px;">Valor</div>
                  <div class="col-md-1" style="width: 130px;">Agrupador</div>
                  <div class="col-md-1" style="width: 130px;">Conector</div>
                  <div id="contenedor_metadato">
                  </div>
                </div>

                  </br></br></br>
                  <!-- {!!Form::button('Buscar Directorio',["class"=>"btn btn-primary", 'onclick' => 'ejecutarConsulta("Directorio");', 'id'=>'consulta'])!!} -->
                  {!!Form::button('Buscar Lista',["class"=>"btn btn-primary", 'onclick' => 'ejecutarConsulta("Lista");', 'id'=>'consulta'])!!}
                  {!!Form::button('Limpiar',["class"=>"btn btn-default", 'onclick' => 'limpiarCondicion();'])!!}
                  {!! Form::hidden('condicionMetadato', null, array('id' => 'condicionMetadato')) !!}
              </div>
            </div>
          </div>
        </div>
      </div>

  </fieldset>

  <script type="text/javascript">

    function ejecutarConsulta(tipo)
    {
      document.getElementById("condicionMetadato").value = '';
        datos = '';

        for(i = 0; i < metadato.contador; i++)
        {
          valor = (document.getElementById("operadormetadato"+i).value == 'like') ? "*"+document.getElementById("valormetadato"+i).value+"-" : "."+document.getElementById("valormetadato"+i).value+",";
          if(document.getElementById("operadormetadato"+i))
          {
            datos +=
                document.getElementById("parentesisIniciometadato"+i).value+' Metadato_idMetadato = '+
                document.getElementById("campometadato"+i).value+' and valorRadicadoDocumentoPropiedad '+
                document.getElementById("operadormetadato"+i).value+' '+
                valor +' '+
                document.getElementById("parentesisFinmetadato"+i).value+''+
                document.getElementById("conectormetadato"+i).value+' ';
          }
        }
        query = document.getElementById("condicionMetadato").value += datos;

        // reiniciamos el contador de registros de la condicion
        // metadato.contador = 0;

      if (tipo == 'Lista') 
      {
        idDocoumento = '';
        idDependencia = '';

        for (var i = 0; i < documento.contador; i++) 
        {
          if(document.getElementById("Documento_idDocumento"+i))
            idDocoumento += document.getElementById("Documento_idDocumento"+i).value+',';
        }

        for (var i = 0; i < dependencia.contador; i++) 
        {
          if(document.getElementById("Dependencia_idDependencia"+i))
            idDependencia += document.getElementById("Dependencia_idDependencia"+i).value+',';
        }

        idDoc = idDocoumento;
        idDep = idDependencia;
        

        idDoc = idDoc.substring(0,idDoc.length-1);
        idDep = idDep.substring(0,idDep.length-1);

        if (query != '') 
        {
          window.open('http://'+location.host+'/consultaradicado?idDoc='+idDoc+'&idDep='+idDep+'&consulta='+query); 
        }
        else
        {
          alert("Debe ingresar al menos una condición.");
        }
        
      }
    }

    function limpiarCondicion()
    {
      document.getElementById("contenedor_metadato").innerHTML = '';
      metadato.contador = 0;
    }

  </script>

  
  {!!Form::close()!!}
@stop