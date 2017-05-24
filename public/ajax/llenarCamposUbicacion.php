<?php

$idUbicacion = $_POST['idUbicacion'];

$ubicacion = DB::Select('
      SELECT
        idUbicacionDocumento,
        tipoUbicacionDocumento,
        idUbicacionDocumento,
        DependenciaLocalizacion_idDependenciaLocalizacion,
        posicionUbicacionDocumento,
        descripcionUbicacionDocumento,
        Tercero_idTercero,
        "" as nombreTerceroUbicacionDocumento,
        "" as documentoTerceroUbicacionDocumento,
        numeroLegajoUbicacionDocumento,
        numeroFolioUbicacionDocumento,
        fechaInicialUbicacionDocumento,
        fechaFinalUbicacionDocumento,
        nombreTipoSoporteDocumental,
        idTipoSoporteDocumental,
        nombreDependencia,
        idDependencia,
        nombreCompania,
        idCompania,
        estadoUbicacionDocumento,
        observacionUbicacionDocumento
      FROM
        ubicaciondocumento ud
          LEFT JOIN
        tiposoportedocumental tsd ON ud.TipoSoporteDocumental_idTipoSoporteDocumental = tsd.idTipoSoporteDocumental
          LEFT JOIN
        dependencia d ON ud.Dependencia_idProductora = d.idDependencia
          LEFT JOIN
        compania c ON ud.Compania_idCompania = c.idCompania
      WHERE idUbicacionDocumento = '.$idUbicacion);

echo json_encode($ubicacion);

?>