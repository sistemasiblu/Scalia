<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link type="image/x-icon" rel="icon" href="{!!('imagenes/LogoScaliaMiniN.png')!!}">
	{!!Html::style('css/menubootstrap.css'); !!}
	@yield('clases')

	<title>Scalia</title>
</head>
<body>

<!-- <div id="container" class="container"> -->
	<nav class="navbar navbar-default" role="navigation">
		<div  id="container-fluid" class="container-fluid navbar-border">
		    <div class="navbar-header">
			    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
			        <span class="sr-only">Scalia</span>
			        <span class="icon-bar"></span>
			    	<span class="icon-bar"></span>
			    	<span class="icon-bar"></span>
			    </button>
			      <a class="navbar-brand" href=<?php echo 'http://'.$_SERVER["HTTP_HOST"].'/scalia';?>><i></i>Scalia</a>
		    </div>

		    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		    	<ul class="nav navbar-nav">

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

							echo '
							<li class="dropdown">
						        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-"></i>'.$datosP->nombrePaquete.'<span class="caret"></span></a>
						        <ul class="dropdown-menu" role="menu">';

						    foreach ($opciones as $idO => $datosO) 
							{
								if ($datosO->nombreOpcion == 'Programación importaciones') 
								{
									echo '
									<li id="li" class="dropdown-submenu">
										<a class="test" tabindex="-1" href="#"><i class="fa fa-"></i>Programación importaciones</a></li>
										<ul class="dropdown-menu">';	

										$docImportacion = DB::Select('SELECT * from documentoimportacion');

										foreach ($docImportacion as $idO => $datos) 
										{
											echo'
											<li id="li"><a tabindex="-1" href="http://'.$_SERVER["HTTP_HOST"].'compra?idDocumento='.$datos->idDocumentoImportacion.'"><i class="fa fa-"></i>Compra '.$datos->nombreDocumentoImportacion.'</a></li>

											<li id="li"><a href="http://'.$_SERVER["HTTP_HOST"].'embarque?idDocumento='.$datos->idDocumentoImportacion.'"><i tabindex="-1" class="fa fa-"></i>Embarque '.$datos->nombreDocumentoImportacion.'</a></li>

											<li id="li"><a href="http://'.$_SERVER["HTTP_HOST"].'consultaembarque?idDocumento='.$datos->idDocumentoImportacion.'"><i tabindex="-1" class="fa fa-"></i>Consulta Embarque '.$datos->nombreDocumentoImportacion.'</a></li>';
										}

									echo '
										</ul>
									</li>';
								}
								else
								{
									echo '
									<li id="li"><a href="http://'.$_SERVER["HTTP_HOST"].'/'.$datosO->rutaOpcion.'"><i class="fa fa-"></i>'.$datosO->nombreCortoOpcion.'</a></li>';
								}
							}

							echo '
								</ul>
			        		</li>';
						}

					?> 
			    </ul>
			    <ul class="nav navbar-nav navbar-right">
			        <li class="dropdown">
			          <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"><?php echo \Session::get("nombreUsuario");?></i> 
			          <ul class="dropdown-menu login-panel">
			            <li id="li">
			               <a href=<?php echo 'http://'.$_SERVER["HTTP_HOST"].'/auth/logout';?>><i class="fa fa-sign-in"></i>Salir</a>
			            </li>
			            <!--<li class="divider"></li>-->
			          </ul>
			        </li>
		      	</ul>
		    </div>
		</div>
	</nav>
<!-- </div> -->

	<div id="contenedor">
	    @yield('titulo')
	</div>
	<div id="contenedor-fin">
	    <div id="pantalla">
	       @yield('content') 
	    </div>
	</div>

	<script>
		$(document).ready(function(){
		  $('.dropdown-submenu a.test').on("click", function(e){
		    $(this).next('ul').toggle();
		    e.stopPropagation();
		    e.preventDefault();
		  });
		});
	</script>
	<!-- <div id="footer">
	    <p>Todos los derechos reservados</p>
	</div> -->
</body>
</html>

