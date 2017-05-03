<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link type="image/x-icon" rel="icon" href="{!!('imagenes/LogoScaliaMiniN.png')!!}">
	@yield('clases')
	{!!Html::style('css/menu_nuevo.css'); !!}
	{!!Html::script('js/menu_nuevo.js'); !!}


	<title>Scalia</title>
</head>
<body id='body'>

	<div id="header">
		<div id="container">
		<div class="barramenu">
			
		<?php

			// -------------------------------------------
			// P A Q U E T E S   S E G U N   E L   R O L 
			// D E L   U S U A R I O 
			// -------------------------------------------
			$paquetes = DB::select(
			    'SELECT P.idPaquete,
				    P.nombrePaquete,
				    P.iconoPaquete
				FROM users U
				left join rol R
				on U.Rol_idRol = R.idRol
				left join rolopcion RO
				on U.Rol_idRol = RO.Rol_idRol
				left join opcion O
				on RO.Opcion_idOpcion = O.idOpcion
				left join paquete P
				on O.Paquete_idPaquete = P.idPaquete
				where U.id = '.\Session::get("idUsuario").'
				GROUP BY P.idPaquete
				ORDER BY P.ordenPaquete, P.nombrePaquete;');
			
				
				foreach ($paquetes as $idP => $datosP) 
				{

					// si el paquete comienza por CRM,consultamos los Movimientos que tiene permisos, de lo contrario consultamos opciones generales con permiso
					if(substr($datosP->nombrePaquete,0,3) == 'CRM')
					{
						// -------------------------------------------
						// O P C I O N E S  D E  C R M   S E G U N   E L   R O L 
						// D E L   U S U A R I O  Y   L A   C O M P A N I A
						// -------------------------------------------
						$opciones = DB::select(
						'Select
						  CONCAT("movimientocrm?idDocumentoCRM=", idDocumentoCRM) as rutaOpcion,
						  "menu/casocrm.png" as iconoOpcion,
						  documentocrm.nombreDocumentoCRM as nombreOpcion,
						  documentocrm.nombreDocumentoCRM as nombreCortoOpcion
						From
						  documentocrm  Inner Join
						  documentocrmrol
						    On documentocrmrol.DocumentoCRM_idDocumentoCRM = documentocrm.idDocumentoCRM
						  Inner Join
						  rol
						    On documentocrmrol.Rol_idRol = rol.idRol Inner Join
						  users
						    On users.Rol_idRol = rol.idRol
						Where
						  users.id = '.\Session::get("idUsuario").' And
						  documentocrm.Compania_idCompania = '.\Session::get("idCompania"));

						// foreach ($opciones as $idO => $datosO) 
						// {

						// 	echo 
						// 	'
						// 	<li>
						// 		<a href="http://'.$_SERVER["HTTP_HOST"].'/movimientocrm?idDocumentoCRM='.$datosO->idDocumentoCRM.'"> <img src="http://'.$_SERVER["HTTP_HOST"].'/imagenes/menu/casocrm.png" title="'.$datosO->nombreDocumentoCRM.'" style="width:48px; height:48px;"><br>
						// 			'.$datosO->nombreDocumentoCRM.'
						// 		</a>
						// 	</li>';
						// }
					}
					else
					{
						// -------------------------------------------
						// O P C I O N E S   S E G U N   E L   R O L 
						// D E L   U S U A R I O 
						// -------------------------------------------
						$opciones = DB::select(
						    'SELECT O.idOpcion,
							    P.nombrePaquete,
							    P.iconoPaquete,
							    O.nombreOpcion,
							    O.nombreCortoOpcion,
							    O.iconoOpcion,
							    O.rutaOpcion
							FROM users U
							left join rol R
							on U.Rol_idRol = R.idRol
							left join rolopcion RO
							on U.Rol_idRol = RO.Rol_idRol
							left join opcion O
							on RO.Opcion_idOpcion = O.idOpcion
							left join paquete P
							on O.Paquete_idPaquete = P.idPaquete
							where 	U.id = '.\Session::get("idUsuario").' and
							 		O.Paquete_idPaquete = '.$datosP->idPaquete.'
							order by O.ordenOpcion, O.nombreOpcion;');


						
					}

					$importacion = '';
					foreach ($opciones as $idO => $datosO) 
					{
						if ($datosO->nombreOpcion == 'Programación importaciones') 
						{
							$importacion .=	
							'
							
							<div id="gridboximportacion'.$datosO->idOpcion.'" class="gridboximportacion">
							<div id="innergrid'.$datosO->idOpcion.'" class="innergrid">
							<ul id="icons'.$datosO->idOpcion.'" class="icons">';
							$docImportacion = DB::Select('SELECT * from documentoimportacion');

								foreach ($docImportacion as $idO => $datos) 
								{	
									$importacion .=
									'
									<input id="estadomenu" type="hidden" value=0>
									<li>
										<a href="http://'.$_SERVER["HTTP_HOST"].'/compra?idDocumento='.$datos->idDocumentoImportacion.'"> <img src="http://'.$_SERVER["HTTP_HOST"].'/imagenes/menu/compra.png" title="Compra '.$datos->nombreDocumentoImportacion.'" style="width:48px; height:48px;"><br>
											Compra '.$datos->nombreDocumentoImportacion.'
										</a>
									</li>

									<li>
										<a href="http://'.$_SERVER["HTTP_HOST"].'/embarque?idDocumento='.$datos->idDocumentoImportacion.'"> <img src="http://'.$_SERVER["HTTP_HOST"].'/imagenes/menu/embarque.png" title="Embarque '.$datos->nombreDocumentoImportacion.'" style="width:48px; height:48px;"><br>
											Embarque '.$datos->nombreDocumentoImportacion.'
										</a>
									</li>

									<li>
										<a href="http://'.$_SERVER["HTTP_HOST"].'/consultaembarque?idDocumento='.$datos->idDocumentoImportacion.'"> <img src="http://'.$_SERVER["HTTP_HOST"].'/imagenes/menu/consultar_embarque.png" title="Consulta Embarque '.$datos->nombreDocumentoImportacion.'" style="width:48px; height:48px;"><br>
											Consulta Embarque '.$datos->nombreDocumentoImportacion.'
										</a>
									</li>';
								}
							$importacion .=
							'
									</ul>
								</div>
							</div>
							</li>

							';

						}
					}

					echo $importacion;


					

					// Creamos el icono del paquete
					echo 
					'<div id="menu'.$datosP->idPaquete.'" class="menu">
						<img src="http://'.$_SERVER["HTTP_HOST"].'/imagenes/'.$datosP->iconoPaquete.'" title="'.$datosP->nombrePaquete.'" style="width:40px; height:36px; margin:5px;" onclick="abreMenu('.$datosP->idPaquete.', '.count($opciones).');">
					</div>';

					// Creamos el marco para las opciones del paquete
					echo 
					'<div id="gridbox'.$datosP->idPaquete.'" class="gridbox" style="margin-left: 15px;">
						<div id="innergrid'.$datosP->idPaquete.'" class="innergrid">
							<ul id="icons'.$datosP->idPaquete.'" class="icons">';

					foreach ($opciones as $idO => $datosO) 
					{
						if ($datosO->nombreOpcion == 'Programación importaciones') 
						{
							echo 
							'<li id="'.$datosO->idOpcion.'" onclick="abreMenuImportacion($(\'#estadomenu\').val(), '.$datosO->idOpcion.',2)">
								<a> <img src="http://'.$_SERVER["HTTP_HOST"].'/imagenes/'.$datosO->iconoOpcion.'" title="'.$datosO->nombreOpcion.'" style="width:48px; height:48px; cursor:pointer;"><br>
									'.$datosO->nombreCortoOpcion.'
								</a>
							</li>';								

						}
						else
						{
							echo 
							'
							<li>
								<a href="http://'.$_SERVER["HTTP_HOST"].'/'.$datosO->rutaOpcion.'"> <img src="http://'.$_SERVER["HTTP_HOST"].'/imagenes/'.$datosO->iconoOpcion.'" title="'.$datosO->nombreOpcion.'" style="width:50px; height:50px;"><br>
									'.$datosO->nombreCortoOpcion.'
								</a>
							</li>';
						}
					}
					
					// Cerramos el marco de opciones del paquete					
					echo 
					'		</ul>
						</div>
					</div>';
				}

				echo 
				'<div id="menuuser1" class="menu" style="float: right; left:0;">
		            <div>
        		         '.\Session::get("nombreUsuario").', '.\Session::get("nombreCompania").'

                		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<a href="http://'.$_SERVER["HTTP_HOST"].'/auth/logout"> <img src="http://'.$_SERVER["HTTP_HOST"].'/imagenes/menu/Salir_.png" title="Salir de Scalia" style="width:32px; height:32px;">
						</a>
					</div>
				</div>

			</div>';
		?>



<!-- 	<div id="contenedor" class="panel panel-primary">
 -->	  <div   class="panel panel-default">
	    @yield('titulo')
	  </div>
	  <div  id="contenedor-fin" class="panel-body">
	    @yield('content') 
	  </div>
	<!-- </div> -->


	<!-- <div id="footer">
	    <p>Scalia... Grupo Bluman - Todos los derechos reservados</p>
	</div> -->
</body>
</html>