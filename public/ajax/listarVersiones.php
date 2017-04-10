<?php

$idRadicado = $_POST['Radicado_idRadicado'];

$numeroV = \App\RadicadoVersion::where('Radicado_idRadicado', "=", $idRadicado)->lists('numeroRadicadoVersion');

$numeroVersion = DB::Select('SELECT max(numeroRadicadoVersion) as numeroRadicadoVersion from radicadoversion
    where Radicado_idRadicado = '.$idRadicado);

$select = '';
$numeroVer = get_object_vars($numeroVersion[0]);

foreach ($numeroV as $idVersion => $valVersion) 
    {
        $select .= '<option value="'.$valVersion.'"'.($valVersion == $numeroVer["numeroRadicadoVersion"] ? 'selected="selected"' : '') .' >Versión '.$valVersion.'</option>';
    }

echo json_encode($select);
?>