<?php 

$idForward = $_POST['idForward'];

$forward = DB::Select('
	SELECT 
	    f.idForward,
	    f.descripcionForward,
	    f.fechaNegociacionForward,
	    f.fechaVencimientoForward,
	    f.modalidadForward,
	    f.valorDolarForward,
	    f.tasaForward,
	    f.tasaInicialForward,
	    f.valorPesosForward,
	    f.bancoForward,
	    f.rangeForward,
	    f.devaluacionForward,
	    f.spotForward,
	    f.estadoForward,
	    fp.numeroForward
	FROM
	    forward f
	        LEFT JOIN
	    forward fp ON f.ForwardPadre_idForwardPadre = fp.idForward
	WHERE f.idForward = '.$idForward);

$fwd = get_object_vars($forward[0]);

echo json_encode($fwd);