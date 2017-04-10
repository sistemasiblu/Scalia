<?php

$idRadicado = $_POST['Radicado_idRadicado'];

$archivo = DB::select('DELETE from radicado
where idRadicado IN ('.$idRadicado.')');

echo json_encode('Documento(s) eliminado(s) correctamente');
?>