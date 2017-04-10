<?php

$idDocumento = $_POST['idDocImp'];
$numeroCompra = $_POST['numeroCompra'];

$numeroV = \App\Compra::where('DocumentoImportacion_idDocumentoImportacion', "=", $idDocumento)->where('numeroCompra', "=", $numeroCompra)->lists('numeroVersionCompra');

$numeroVersion = DB::Select('SELECT max(numeroVersionCompra) as numeroVersionCompra from compra
    where DocumentoImportacion_idDocumentoImportacion = '.$idDocumento.' and numeroCompra = "'.$numeroCompra.'"');

$select = '';
$numeroVer = get_object_vars($numeroVersion[0]);
$num = (int)$numeroVer['numeroVersionCompra']+1;	

	$select .= '<option value="'.$num.'*" selected="selected">Versión '.$num.'</option>';

foreach ($numeroV as $idVersion => $valVersion) 
    {
        $select .= '<option value="'.$valVersion.'">Versión '.$valVersion.'</option>';
    }

echo json_encode($select);
?>
