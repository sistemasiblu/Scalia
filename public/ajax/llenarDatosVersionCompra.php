<?php 

$idDocumento = $_POST['idDocImp'];
$numeroVersion = $_POST['numeroVersion'];
$numeroCompra = $_POST['numeroCompra'];

$datosFormulario = DB::Select('SELECT * from compra where DocumentoImportacion_idDocumentoImportacion = '.$idDocumento.' and numeroCompra = "'.$numeroCompra.'" and numeroVersionCompra = '.$numeroVersion);

$datosForm = get_object_vars($datosFormulario[0]);

echo json_encode($datosForm);

?>