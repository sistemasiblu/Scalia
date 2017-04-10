<?php

function ConsultarPermisosCRM($idDoc)
{
  	

// -------------------------------------------
// O P C I O N E S  D E  C R M   S E G U N   E L   R O L 
// D E L   U S U A R I O  Y   L A   C O M P A N I A
// -------------------------------------------
$permiso = DB::Select('
SELECT 
    nombreRol,
    adicionarDocumentoCRMRol,
    modificarDocumentoCRMRol,
    anularDocumentoCRMRol,
    consultarDocumentoCRMRol,
    aprobarDocumentoCRMRol
From
  documentocrm 
  Inner Join documentocrmrol
    On documentocrmrol.DocumentoCRM_idDocumentoCRM = documentocrm.idDocumentoCRM
  Inner Join rol
    On documentocrmrol.Rol_idRol = rol.idRol 
  Inner Join users
    On users.Rol_idRol = rol.idRol
Where
  users.id = '.\Session::get("idUsuario").' And
  documentocrm.Compania_idCompania = '.\Session::get("idCompania"). 
  ' AND idDocumentoCRM = "'.$idDoc.'"');

return($permiso);
}
?>