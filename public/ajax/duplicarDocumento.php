<?php

$idDocumento = $_POST['idDocumento'];

//Ejecuto una consulta la cual duplica los registros de la tabla documento especificando un ID
$duplicarDocumento = DB::select('INSERT INTO documento(idDocumento, codigoDocumento, nombreDocumento, directorioDocumento, tipoDocumento, origenDocumento, SistemaInformacion_idSistemaInformacion,
tipoConsultaDocumento, tablaDocumento, consultaDocumento, controlVersionDocumento, trazabilidadMetadatosDocumento, concatenarNombreDocumento) 
SELECT null as idDocumento, codigoDocumento, nombreDocumento, directorioDocumento, tipoDocumento, origenDocumento, SistemaInformacion_idSistemaInformacion,
tipoConsultaDocumento, tablaDocumento, consultaDocumento, controlVersionDocumento, trazabilidadMetadatosDocumento, concatenarNombreDocumento 
FROM documento 
WHERE idDocumento = '.$idDocumento);

//Consulto el ultimo ID de la tabla y lo guardo en la variable $documento
$ultIdDocumento = \App\Documento::All()->last();
$documento = $ultIdDocumento->idDocumento;

//Se ejecuta la consulta la cual duplica los registros de la tabla documento version tomando como base los datos del documento que llega en el POST
$duplicarDocumentoVersion = DB::select('INSERT INTO documentoversion (idDocumentoVersion, nivelDocumentoVersion, tipoDocumentoVersion, longitudDocumentoVersion, inicioDocumentoVersion,  
rellenoDocumentoVersion, Documento_idDocumento) 
SELECT null as idDocumentoVersion, nivelDocumentoVersion, tipoDocumentoVersion, longitudDocumentoVersion, inicioDocumentoVersion, 
rellenoDocumentoVersion, '.$documento.' as Documento_idDocumento 
FROM documentoversion 
WHERE Documento_idDocumento = '.$idDocumento);

//Se ejecuta la consulta la cual duplica los registros de la tabla documento propiedad 
$duplicarDocumentoPropiedad = DB::select('INSERT INTO documentopropiedad (idDocumentoPropiedad, ordenDocumentoPropiedad, tituloDocumentoPropiedad, campoDocumentoPropiedad, tipoDocumentoPropiedad, Lista_idLista, longitudDocumentoPropiedad, valorDefectoDocumentoPropiedad, gridDocumentoPropiedad, indiceDocumentoPropiedad, versionDocumentoPropiedad, validacionDocumentoPropiedad,
Documento_idDocumento) 
SELECT null as idDocumentoPropiedad, ordenDocumentoPropiedad, tituloDocumentoPropiedad, campoDocumentoPropiedad, tipoDocumentoPropiedad, Lista_idLista, longitudDocumentoPropiedad, valorDefectoDocumentoPropiedad, gridDocumentoPropiedad, indiceDocumentoPropiedad, versionDocumentoPropiedad, validacionDocumentoPropiedad, 
'.$documento.' as Documento_idDocumento
FROM documentopropiedad 
WHERE Documento_idDocumento = '.$idDocumento);

//Se ejecuta la consulta la cual duplica los registros de la tabla documento permiso
$duplicarDocumentoPermiso = DB::select('INSERT INTO documentopermiso (idDocumentoPermiso, Rol_idRol, cargarDocumentoPermiso, descargarDocumentoPermiso, eliminarDocumentoPermiso,  
modificarDocumentoPermiso, consultarDocumentoPermiso, correoDocumentoPermiso, imprimirDocumentoPermiso, Documento_idDocumento) 
SELECT null as idDocumentoPermiso, Rol_idRol, cargarDocumentoPermiso, descargarDocumentoPermiso, eliminarDocumentoPermiso, modificarDocumentoPermiso,
consultarDocumentoPermiso, correoDocumentoPermiso, imprimirDocumentoPermiso, 
'.$documento.' as Documento_idDocumento
FROM documentopermiso 
WHERE Documento_idDocumento = '.$idDocumento);

echo json_encode('Documento duplicado correctamente.')

?>