
<?php

$compania=Session::get('baseDatosCompania');


// $asesores = DB::select(
// 	"select idGrupoEstadoAsesor,GrupoEstado_idGrupoEstado, tercero.nombre1Tercero 
// 	from grupoestadoasesor 
// 	left join ".$compania.".Tercero as tercero
// 	on grupoestadoasesor.Tercero_idAsesor=tercero.idTercero");

$asesores = DB::select(
	"select idTercero as Tercero_idAsesor,nombre1Tercero
	from  ".$compania.".Tercero as tercero
	where tipoTercero like '%05%' or tipoTercero like '%02%'
	order by nombre1Tercero");

$row = array();

foreach ($asesores as $key => $value) 
{  
    $valores = get_object_vars($value);
   
    $row[$key][] = $valores['Tercero_idAsesor'];
    $row[$key][] = $valores['nombre1Tercero'];

      
}

    $output['aaData'] = $row;
    echo json_encode($output);
   
?>