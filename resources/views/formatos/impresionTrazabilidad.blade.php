@extends('layouts.formato')

<title>Trazabilidad</title>
@section('contenido')

{!!Form::model($metadatos)!!}
<?php 

$tipo = $_GET['tipo'];

switch ($tipo) {
  case 'excel':
    header('Content-type: application/vnd.ms-excel');
    header("Content-Disposition: attachment; filename=Formulario.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    break;
    

    case 'word':
    header('Content-type: application/vnd.ms-word');
    header("Content-Disposition: attachment; filename=Formulario.doc");
    header("Pragma: no-cache");
    header("Expires: 0");
    break;
}


function base64($archivo)
{
  
    $logo = '&nbsp;';
    $fp = fopen($archivo,"r", 0);
    if($archivo != '' and $fp)
    {
       $imagen = fread($fp,filesize($archivo));
       fclose($fp);
       // devuelve datos cifrados en base64
       //  formatear $imagen usando la sem ntica del RFC 2045

       $base64 = chunk_split(base64_encode($imagen));
       $logo =  '<img src="data:image/png;base64,' . $base64 .'" alt="Texto alternativo" width="130px"/>';
    }
    return $logo;
}

?>


<?php
  $img = '';
  if (\Session::get("nombreCompania") == 'Ci Iblu') 
  {
    $img = base64('imagenes/Logo_iblu.png');
  }
  elseif (\Session::get("nombreCompania") == 'Extiblu')
  {
    $img = base64("imagenes/Extiblu.png");
  }

    $nombremetadato = array();
  // por facilidad de manejo convierto el stdclass a tipo array con un cast (array)
       for ($i = 0, $c = count($metadatos); $i < $c; ++$i) 
       {
          $nombremetadato[$i] = (array) $metadatos[$i];
       }

       for ($i=0; $i < count($titulo); $i++) 
       { 
         $nombretitulo = get_object_vars($titulo[$i]);
       }
?>

<div>
  <div style="border:1px;">
    <div class="col-md-4"> <?php echo $img ?> </div>
    <div class="col-md-8"><center><h1> <?php echo $nombretitulo['nombreDocumento']; ?> </h1></center></div>
  </div>

<br/>

  <div class="col-md-12">
    <?php 
      echo '
      <table class="table table-striped table-bordered table-hover" style="width:100%; overflow:scroll;">
        <tr>';
          for($i = 0; $i < count($titulo); $i++)
          {
            $nombretitulo = get_object_vars($titulo[$i]);
            echo'<th><b>'.str_replace('_', ' ', $nombretitulo["tituloMetadato"]).'</b></th>';
          }
      echo '</tr>';
        $i = 0;
          while ($i < count($nombremetadato)) 
          {
            $radAnt = $nombremetadato[$i]['RadicadoVersion_idRadicadoVersion'];
            echo '<tr>';
            while ($i < count($nombremetadato) and $radAnt == $nombremetadato[$i]['RadicadoVersion_idRadicadoVersion']) 
            {
              $campo = ($nombremetadato[$i]['tipoMetadato'] == 'Editor') ? $nombremetadato[$i]['editorRadicadoDocumentoPropiedad'] : $nombremetadato[$i]['valorRadicadoDocumentoPropiedad'];
              echo '<td>'.$campo.'</td>';
              $i++;
            }
            echo '</tr>';
          }
        echo '</tr>
      </table>';
    ?>
  </div>

</div>
 
{!!Form::close()!!}
@stop