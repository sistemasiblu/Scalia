<?php

$idDependenciaLocalizacion = $_POST['idDependenciaLocalizacion'];

$estado = DB::Select('SELECT capacidadDependenciaLocalizacion from dependencialocalizacion where idDependenciaLocalizacion = '.$idDependenciaLocalizacion);

$capacidad = get_object_vars($estado[0])['capacidadDependenciaLocalizacion'];

$mensaje = '';

if ($capacidad == 'Disponible') 
{
  $capacidad = DB::update('
  UPDATE 
    dependencialocalizacion 
  SET 
    capacidadDependenciaLocalizacion = "NoDisponible"
  WHERE idDependenciaLocalizacion = '.$idDependenciaLocalizacion);

  $mensaje = 'Caja cerrada correctamente';
}
else
{
  $capacidad = DB::update('
  UPDATE 
    dependencialocalizacion 
  SET 
    capacidadDependenciaLocalizacion = "Disponible"
  WHERE idDependenciaLocalizacion = '.$idDependenciaLocalizacion);

  $mensaje = 'Caja abierta correctamente';
}

echo json_encode($mensaje);

?>