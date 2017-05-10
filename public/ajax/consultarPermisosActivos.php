<?php

function ConsultarPermisosActivos($idDoc)
{
  	

// -------------------------------------------
// O P C I O N E S  D E  C R M   S E G U N   E L   R O L 
// D E L   U S U A R I O  Y   L A   C O M P A N I A
// -------------------------------------------
$permiso = DB::Select('
SELECT 
    nombreRol,
    adicionarTransaccionRol,
    modificarTransaccionRol, 
    anularTransaccionRol, 
    consultarTransaccionRol, 
    autorizarTransaccionRol
From
  transaccionactivo 
  Inner Join transaccionrol
    On transaccionrol.TransaccionActivo_idTransaccionActivo = transaccionactivo.idTransaccionActivo
  Inner Join rol
    On transaccionrol.Rol_idRol = rol.idRol 
  Inner Join users
    On users.Rol_idRol = rol.idRol
Where
  users.id = '.\Session::get("idUsuario").' And
  transaccionactivo.Compania_idCompania = '.\Session::get("idCompania"). 
  ' AND idTransaccionActivo = "'.$idDoc.'"');

return($permiso);
}
?>

