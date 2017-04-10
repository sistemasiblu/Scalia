<!DOCTYPE html>
<html >
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link type="image/x-icon" rel="icon" href="imagenes/LogoScaliaMiniN.png">  
        <script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
        <link rel="stylesheet" href="css/style.css">
        <script src="js/index.js"></script>   
        @yield('clases')
        
    <title>Scalia</title>
  </head>

  <body>
<section>
<div class="col-md-12">
<div >
    <nav id="app-nav" class="app-nav">
        <ul class="links-lvl1 app-nav-main-links links-with-text">
            <li style="list-style:none;">
                {!! HTML::decode(HTML::link('scalia', HTML::image('imagenes/Logo.png','Imagen no encontrada',array('class' => 'logo', 'style'=>'width: 52px;')))) !!}
            </li>
            <li class="trigger" style="list-style:none;"><a href="#" class="trigger-lvl2 link-lvl2">
                <i class="fa fa-folder-open" aria-hidden="true" style="color:#CECECE"></i>
                <span>Maestros</span>

                </a>
                <div class="links-lvl2 with-header">
                    {!! HTML::link('dependencia','Dependencias')!!}
                    {!! HTML::link('documento','Documentos')!!}
                    {!! HTML::link('serie','Series')!!}
                    {!! HTML::link('lista','Listas')!!}
                    {!! HTML::link('sistemainformacion','Sistemas de Información')!!}
                    {!! HTML::link('normograma','Normograma')!!}
                    {!! HTML::link('sitioweb','Sitios Web')!!}
                    {!! HTML::link('etiqueta','Etiquetas')!!}
                </div>
            </li style="list-style:none;">

            <li class="trigger" style="list-style:none;"><a href="#" class="trigger-lvl2 link-lvl2">
                <i class="fa fa-list-alt" aria-hidden="true" style="color:#CECECE"></i>
                <span>Tablas</span>
                </a>
                <div class="links-lvl2 with-header">
                    {!! HTML::link('retencion','Retención Documental')!!}
                    {!! HTML::link('clasificaciondocumental','Clasificación Documental')!!}
                    {!! HTML::link('#','Control de Informes')!!}
                </div>
            </li>
      
            <li class="trigger" style="list-style:none;"><a href="#" class="trigger-lvl2 link-lvl2">
                <i class="fa fa-file-text" aria-hidden="true" style="color:#CECECE"></i>
                <span>Radicado</span>
                </a>
                <div class="links-lvl2 with-header">
                    {!! HTML::link('radicado/create','Radicar Archivos')!!}
                    {!! HTML::link('formulario','Formulario')!!}
                    {!! HTML::link('consultaradicado','Consultar Archivos')!!}
                    {!! HTML::link('#','Sitios Web')!!}
                </div>
            </li>
            <li class="trigger" style="list-style:none;"><a href="#" class="trigger-lvl2 link-lvl2">
                <i class="fa fa-user" aria-hidden="true" style="color:#CECECE"></i>
                <span>Usuarios</span>

                </a>
                <div class="links-lvl2 with-header">
                    {!! HTML::link('users','Usuarios')!!}
                    {!! HTML::link('#','Cambio de clave')!!}
                    {!! HTML::link('rol','Roles')!!}
                    {!! HTML::link('paquete','Paquetes')!!}
                    {!! HTML::link('opcion','Opciones')!!}
                    {!! HTML::link('compania','Compañía')!!}
                </div>
            </li>


            <li style="list-style:none;"><a href="#" class="link-lvl1"><i class="fa fa-sign-out" style="color:#CECECE"></i></a></li>
        </ul>
    </nav>
</div>

<div class="col-md-12" style="overflow: auto;">
  <div id="contenedor">
        @yield('titulo')
    </div>
    <div id="contenedor-fin">
        <div id="pantalla" >
           @yield('content') 
        </div>
    </div>
</div>
</div>    
</section>
        
  </body>
</html>
