<?php

$tiposervicio=DB::select('select * from tiposervicio');
$row=array();

foreach ($tiposervicio as $key => $value) {
	# code...
$value=get_object_vars($value);
$row[$key][]='<a href="tiposervicio/'.$value['idTipoServicio'].'/edit">'.
                            '<span class="glyphicon glyphicon-pencil" ></span>'.
                        '</a>&nbsp;'.
                        '<a href="tiposervicio/'.$value['idTipoServicio'].'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash" ></span>'.
                        '</a>';

 $row[$key][]=$value['idTipoServicio'];
 $row[$key][]=$value['codigoTipoServicio'];
 $row[$key][]=$value['nombreTipoServicio'];                       




}

$output['aaData']=$row;
echo json_encode($output);
