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
<div class="head">
<div class="form-group">
<div class="col-md-2">
{!! HTML::decode(HTML::link('scalia', HTML::image('imagenes/LogoScaliaHorizontalNaranja.png','Imagen no encontrada',array('style' => 'width:130px')))) !!}
</div>
<div class="container-fluid" style="top: 10px;">
	<div class="row">
	<div class="col-md-7">
<div class="menu">
            <ul id="menu">
           			<li class="paquete">    

                       {!! HTML::decode(HTML::link('#', HTML::image('imagenes/Maestros.png','Imagen no encontrada',array('style' => 'width:50px; height:50px')))) !!}
                        <ul>
                                    <li class="hula">
                                    {!! HTML::link('dependencia','Dependencias')!!}
                                    </li>
                                    <li>
                                    {!! HTML::link('documento','Documento')!!}
                                    </li>
                                    <li>
                                    {!! HTML::link('documentoimportacion','Documentos de importación')!!}
                                    </li>
                                    <li>
                                    {!! HTML::link('serie','Serie')!!}
                                    </li>
                                    <li>
                                    {!! HTML::link('lista','Listas')!!}
                                    </li>
                                    <li>
                                    {!! HTML::link('sistemainformacion','Sistema de información')!!}
                                    </li>
                                    <li>
                                    {!! HTML::link('normograma','Normograma')!!}
                                    </li>
                                    <li>
                                    {!! HTML::link('sitioweb','Sitios Web')!!}
                                    </li>
                                    <li>
                                    {!! HTML::link('etiqueta','Etiquetas')!!}
                                    </li>
                                    
                        </ul>				
                    </li>

                    <li class="paquete">                     
                    {!! HTML::decode(HTML::link('#', HTML::image('imagenes/Tabla.png','Imagen no encontrada',array('style' => 'width:50px; height:50px')))) !!}
                        <ul>
                             <li class="hula">
							<li>
                            {!! HTML::link('retencion','Retención documental (TRD)')!!}
                            </li>	
							<li>
                            {!! HTML::link('clasificaciondocumental','Clasificación documental (CCD)')!!}
                            </li>
							<li>
                            {!! HTML::link('#','Control de informes (CCI)')!!}
                            </li>		
                    		</li>
                    	</ul>
                    </li>

                    <li class="paquete">                     
                    {!! HTML::decode(HTML::link('#', HTML::image('imagenes/Movimiento_.png','Imagen no encontrada',array('style' => 'width:50px; height:50px')))) !!}
                        <ul>
                             <li class="hula">
							<li>
                            {!! HTML::link('radicado/create','Radicar archivos')!!}
                            </li>	
                            <li>
                            {!! HTML::link('formulario','Formularios')!!}
                            </li>   
							<li>
                            {!! HTML::link('consultaradicado','Consultar archivos')!!}
                            </li>
                            <li>
                                {!! HTML::link('#','Pedidos de importación')!!}
                                <ul>
                                    <li>
                                    {!! HTML::link('compra?idDocumento=1','Compras china')!!}
                                    </li>
                                    <li>
                                    {!! HTML::link('compra?idDocumento=2','Compras panama')!!}
                                    </li>
                                </ul>
                            </li>
							<li>
                            {!! HTML::link('visorinforme?modo=F','Diseñador de informes')!!}
                            </li>
                            <li>
                            {!! HTML::link('consultaproduccion','Consulta de producción')!!}
                            </li>		
                    		</li>
                    	</ul>
                    </li>

                    <li class="paquete">                     
                    {!! HTML::decode(HTML::link('#', HTML::image('imagenes/Usuarios.png','Imagen no encontrada',array('style' => 'width:50px; height:50px')))) !!}
                        <ul>
                             <li class="hula">
							<li>
                            {!! HTML::link('users','Usuarios')!!}
                            </li>	
							<li>
                            {!! HTML::link('#','Cambio de clave')!!}
                            </li>
							<li>
                            {!! HTML::link('rol','Rol')!!}
                            </li>
                            <li>
                            {!! HTML::link('paquete','Paquetes')!!}
                            </li>
                            <li>
                            {!! HTML::link('opcion','Opciones')!!}
                            </li>
                            <li>
                                {!! HTML::link('compania','Compa&ntilde;&iacute;a')!!}
                            </li>		
                    		</li>
                    	</ul>
                    </li>
                    <li class="paquete"> 
                    {!! HTML::decode(HTML::link('auth/logout', HTML::image('imagenes/Salir_.png','Imagen no encontrada',array('style' => 'width:50px; height:50px')))) !!}
                    </li>
                    </ul>

        </div>  
        </div>  
        <div clas="col-md-3">
            <?php echo 'Bienvenido, '. \Session::get("nombreUsuario").'<br/>
            Compañía: '.\Session::get("nombreCompania")?>
        </div>
        </div>  
        </div>     
</div>
</div>
	<div id="contenedor">
	    @yield('titulo')
	</div>
	<div id="contenedor-fin">
	    <div id="pantalla">
	       @yield('content') 
	    </div>
	</div>

    <!-- <div id="footer" style="z-index: 2">
        <center><p>Scalia &copy; - Versión 1.0.0 | C.I Iblu S.A.S</p></center>
    </div>
	 -->
	
</body>
</html>
