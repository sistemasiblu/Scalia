<!DOCTYPE html>

<html>
    <head>
        <title>Scalia</title>
        <link type="image/x-icon" rel="icon" href="{!!('imagenes/LogoScaliaMini.png')!!}">
        {!!Html::script('jquery/jquery.js')!!}
        {!!Html::script('js/error.js')!!}
        {!!Html::style('css/leermas.css'); !!}
    </head>

    <body>
            <div id="contenido">
             <div class="title">Error </br> Vuelve atrás.</div>
            <p id="oculto">
            {!!$error_message!!}
                <br><br><br>
            
            <a href="#" id="ocultar">Regresar</a>
            </p>

            <a href="#" id="mostrar">Leer Más</a><br>
        </div>
    </body>
</html>