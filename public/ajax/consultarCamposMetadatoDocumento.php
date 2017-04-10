<?php

$idMetadato = $_POST['idMetadato'];

$datosMetadato = DB::Select('SELECT * from metadato m left join lista l on m.Lista_idLista = l.idLista where idMetadato = '.$idMetadato);

$metadato = get_object_vars($datosMetadato[0]);

echo json_encode($metadato);
?>