<?php 

$campos = DB::select(
    'SELECT * from movimientoactivo
    where Transaccionactivo_idTransaccionActivo = '.$idTransaccionActivo);


$datos = array();
for($i = 0; $i < count($campos); $i++)
{
    $datos = get_object_vars($campos[$i]); 
    
}



?>
@extends('layouts.formato')
<h3 id="titulo"><center><?php echo '('.$datos["numeroMovimientoActivo"].') '.$datos["numeroMovimientoActivo"];?></center></h3>

@section('contenido')
<?php 
	echo $movimientoactivo;
?>	
@stop