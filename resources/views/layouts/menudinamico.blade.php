<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link type="image/x-icon" rel="icon" href="{!!('imagenes/LogoScaliaMiniN.png')!!}">
    {!!Html::style('css/menu.css'); !!}
    {!!Html::script('js/menu.js'); !!}
	
	@yield('clases')

	<title>Scalia</title>
</head>
<body>
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

				echo 
					'<div class="head">
						<div class="form-group">
							<div class="col-md-2">
							<a href="http://'.$_SERVER["HTTP_HOST"].'/scalia"><img src="http://'.$_SERVER["HTTP_HOST"].'/imagenes/LogoScaliaHorizontalNaranja.png" style="width:130px;"></a>
							</div>
						<div class="container-fluid" style="top: 10px;">
						<div class="row">
						<div class="col-md-7">
						<div class="menu">
						<ul id="menu">';
				foreach ($paquetes as $idP => $datosP) 
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

					echo 
					'<li class="paquete">
								<img src="http://'.$_SERVER["HTTP_HOST"].'/imagenes/'.$datosP->iconoPaquete.'" title="'.$datosP->nombrePaquete.'" style="width:50px; cursor:pointer;"'.count($opciones).');">
							<ul>';
				
					foreach ($opciones as $idO => $datosO) 
					{	
						if ($datosO->nombreOpcion == 'Programación importaciones') 
						{
							echo 
							'
							<li> <a href="#">Programación importaciones</a>
								<ul>';
								$docImportacion = DB::Select('SELECT * from documentoimportacion');

								foreach ($docImportacion as $idO => $datos) 
								{	
									echo 
									'
									<li>
										<a href="http://'.$_SERVER["HTTP_HOST"].'/compra?idDocumento='.$datos->idDocumentoImportacion.'">Compra '.$datos->nombreDocumentoImportacion.' 
											</a>
									</li>
									<li>
										<a href="http://'.$_SERVER["HTTP_HOST"].'/embarque?idDocumento='.$datos->idDocumentoImportacion.'">Embarque '.$datos->nombreDocumentoImportacion.' 
											</a>
									</li>
									<li>
										<a href="http://'.$_SERVER["HTTP_HOST"].'/consultaembarque?idDocumento='.$datos->idDocumentoImportacion.'">Consulta Embarque '.$datos->nombreDocumentoImportacion.' 
											</a>
									</li>';
								}
								
							echo '									
								</ul>
							</li>';		
						}
						else
						{

							echo 
							'
							<li>
								<a href="http://'.$_SERVER["HTTP_HOST"].'/'.$datosO->rutaOpcion.'"> 
									'.$datosO->nombreOpcion.'</a>
							</li>';
						}
					}
					echo 
					'
						</ul>
					</li>
						
					';
					
				}

				echo 
				'
				</ul>
					</div>  
        			</div> 
				<div class="col-md-3">
    		         '.\Session::get("nombreUsuario").', '.\Session::get("nombreCompania").'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<a href="http://'.$_SERVER["HTTP_HOST"].'/auth/logout"> <img src="http://'.$_SERVER["HTTP_HOST"].'/imagenes/Salir_.png" title="Salir" style="width:45px; height:45px;">
						</a>
				</div>
			   </div>
			</div>
			</div>
			</div>';
		?>



	<div id="contenedor">
	    @yield('titulo')
	</div>
	<div id="contenedor-fin">
	    <div id="pantalla">
	       @yield('content') 
	    </div>
	</div>


	<!-- <div id="footer">
	    <p>Todos los derechos reservados</p>
	</div> -->
</body>
</html>