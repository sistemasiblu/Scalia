<?php

$datos = ['correo' => $_POST['correo'], 'asunto' => $_POST['asunto'], 'mensaje' => $_POST['mensaje'], 'adjunto' => $_POST['adjunto']];

    Mail::send('emails.contact',$datos,function($msj) use ($datos)
    {
    	$msj->to($datos['correo']);
        $msj->subject($datos['asunto']);
        // $msj->getSwitfMessage($datos['mensaje']);
        $archivos = explode('|', $datos['adjunto']);

        for($i=0; $i < count($archivos); $i++)
        {
	        $msj->attach($archivos[$i]);
        }
    });  

    echo json_encode('Mensaje enviado');
?>