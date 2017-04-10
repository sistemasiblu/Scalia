<?php

function ConsultarPermisos($vista)
{
  	$permiso = DB::Select('
    SELECT 
        nombreOpcion,
        nombreRol,
        adicionarRolOpcion,
        modificarRolOpcion,
        eliminarRolOpcion,
        consultarRolOpcion,
        rutaOpcion
    FROM
        rol AS r
            LEFT JOIN
        rolopcion AS ro ON ro.Rol_idRol = r.idRol
            LEFT JOIN
        users AS u ON u.Rol_idRol = r.idRol
            LEFT JOIN
        opcion AS o ON ro.Opcion_idOpcion = o.idOpcion
            LEFT JOIN
        paquete AS p ON o.Paquete_idPaquete = p.idPaquete
    WHERE
        Compania_idCompania = '.\Session::get("idCompania").' AND id = '.\Session::get("idUsuario"). ' AND rutaOpcion = "'.$vista.'"');

    return($permiso);
}

?>