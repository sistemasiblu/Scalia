<?php 

$idUbicacion = $_POST['idUbicacion'];

$ubicacion = DB::Select("
	SELECT 
	    CONCAT(descripcionDependenciaLocalizacion,'-',posicionUbicacionDocumento) as puntoLocalizacion
	FROM
	    ubicaciondocumento ud
	        LEFT JOIN
	    dependencialocalizacion dl ON ud.DependenciaLocalizacion_idDependenciaLocalizacion = dl.idDependenciaLocalizacion
	WHERE idUbicacionDocumento = ".$idUbicacion);

echo json_encode($ubicacion);

?>