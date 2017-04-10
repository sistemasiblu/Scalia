<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SiSoft - Sistema Integrado de Salud Ocupacional</title>

    <!-- Bootstrap Core CSS -->
    {!!Html::style('sb-admin/bower_components/metisMenu/dist/metisMenu.min.css'); !!}
    {!!Html::style('sb-admin/bower_components/bootstrap/dist/css/bootstrap.min.css'); !!}

    <!-- MetisMenu CSS -->
    {!!Html::style('sb-admin/bower_components/metisMenu/dist/metisMenu.min.css'); !!}

    <!-- Timeline CSS -->
    {!!Html::style('sb-admin/dist/css/timeline.css'); !!}

    <!-- Custom CSS -->
    {!!Html::style('sb-admin/dist/css/sb-admin-2.css'); !!}

    <!-- Morris Charts CSS -->
    <!-- {!!Html::style('sb-admin/bower_components/morrisjs/morris.css'); !!} -->
    
    {!! Html::style('//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css'); !!}
    {!! Html::script('//ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js'); !!}
    {!! Html::script('//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js'); !!}
    {!! Html::script('//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js'); !!}


        <!-- jQuery 2.0.2 -->
        <!-- {!! Html::script('http://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js'); !!} -->
        <!-- Bootstrap -->
        <!-- {!! Html::script('../../js/bootstrap.min.js'); !!} -->
        <!-- AdminLTE App -->
        <!-- {!! Html::script('../../js/AdminLTE/app.js'); !!} -->
        <!-- AdminLTE for demo purposes -->
        <!-- {!! Html::script('js/AdminLTE/demo.js'); !!} -->
        <!-- FLOT CHARTS -->
        {!! Html::script('sb-admin/bower_components/flot/jquery.flot.js'); !!}
        <!-- FLOT RESIZE PLUGIN - allows the chart to redraw when the window is resized -->
        {!! Html::script('sb-admin/bower_components/flot/jquery.flot.resize.js'); !!}
        <!-- FLOT PIE PLUGIN - also used to draw donut charts -->
        {!! Html::script('sb-admin/bower_components/flot/jquery.flot.pie.js'); !!}
        <!-- FLOT CATEGORIES PLUGIN - Used to draw bar charts -->
        {!! Html::script('sb-admin/bower_components/flot/jquery.flot.categories.js'); !!}



    <!-- Custom Fonts -->
    {!!Html::style('sb-admin/bower_components/font-awesome/css/font-awesome.min.css'); !!}

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <div id="contenedor" class="panel panel-primary">
      <div   class="panel panel-heading">
        @yield('titulo')
      </div>
      <div  id="contenedor-fin" class="panel-body">
        @yield('tablero') 
      </div>
    </div>
</body>

</html>
