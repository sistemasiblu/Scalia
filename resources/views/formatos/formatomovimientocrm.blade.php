<?php 

$campos = DB::select(
    'SELECT codigoDocumentoCRM, nombreDocumentoCRM, nombreCampoCRM,descripcionCampoCRM, 
            mostrarGridDocumentoCRMCampo, relacionTablaCampoCRM, relacionNombreCampoCRM, relacionAliasCampoCRM
    FROM documentocrm
    left join documentocrmcampo
    on documentocrm.idDocumentoCRM = documentocrmcampo.DocumentoCRM_idDocumentoCRM
    left join campocrm
    on documentocrmcampo.CampoCRM_idCampoCRM = campocrm.idCampoCRM
    where documentocrm.idDocumentoCRM = '.$idDocumentoCRM.' and mostrarVistaDocumentoCRMCampo = 1');


$datos = array();
for($i = 0; $i < count($campos); $i++)
{
    $datos = get_object_vars($campos[$i]); 
    
}



?>
@extends('layouts.formato')
<h3 id="titulo"><center><?php echo '('.$datos["codigoDocumentoCRM"].') '.$datos["nombreDocumentoCRM"];?></center></h3>

@section('contenido')
<?php 
	echo $movimientocrm;
?>	
@stop