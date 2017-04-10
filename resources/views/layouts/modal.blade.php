<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link type="image/x-icon" rel="icon" href="{!!('imagenes/LogoScaliaMini.png') !!}">
    

      {!!Html::style('assets/bootstrap-v3.3.5/css/bootstrap-theme.min.css'); !!}
      {!!Html::style('assets/font-awesome-v4.3.0/css/font-awesome.min.css'); !!}
    
      {!!Html::style('choosen/docsupport/style.css'); !!}
      {!!Html::style('choosen/docsupport/prism.css'); !!}
      {!!Html::style('choosen/chosen.css'); !!}
    
      {!!Html::style('sb-admin/bower_components/metisMenu/dist/metisMenu.min.css'); !!}
      {!!Html::style('sb-admin/dist/css/sb-admin-2.css'); !!}
      {!!Html::style('sb-admin/bower_components/font-awesome/css/font-awesome.min.css'); !!}
      {!!Html::style('sb-admin/bower_components/datetimepicker/css/bootstrap-datetimepicker.min.css'); !!}
      {!!Html::style('sb-admin/bower_components/fileinput/css/fileinput.css'); !!}

        <style type="text/css" media="all">
          .chosen-rtl .chosen-drop { left: -9000px; }
        </style>

      {!!Html::script('js/jquery.min.js'); !!}
    
      {!!Html::script('choosen/chosen.jquery.js'); !!}
      {!!Html::script('choosen/docsupport/prism.js'); !!}

      <!-- Bootstrap -->
      {!!Html::style('assets/bootstrap-v3.3.5/css/bootstrap.min.css'); !!}
      {!!Html::script('assets/bootstrap-v3.3.5/js/bootstrap.min.js'); !!}

      {!!Html::script('sb-admin/bower_components/datetimepicker/js/moment.js'); !!}
      {!!Html::script('sb-admin/bower_components/datetimepicker/js/bootstrap-datetimepicker.min.js'); !!}

        

       <script type="text/javascript">
         $(document).bind("pageinit", function() { $(".chzn-select").chosen(); 
          for (var selector in config) {
              $(selector).chosen(config[selector]);
            }  
          }); 
        </script>  
        
         
        <!-- DataTables -->
        {!!Html::script('DataTables/media/js/jquery.dataTables.js'); !!}
        {!!Html::style('DataTables/media/css/jquery.dataTables.min.css'); !!}

               
        <style type="text/css">
            a
            {
                color: #000;
            }   

            input[type=search]
            {
                width: 150px;
                height: 30px;
                background-color: #fff;
                background-image: none;
                border: 1px solid #ccc;
                border-radius: 4px;
            }
        </style>


  @yield('clases')

  <title>Scalia</title>

</head>
<body>
<div class="head">
<div class="form-group">
    
</div>
  <div id="contenedor">
      @yield('titulo')
  </div>
  <div id="contenedor-fin">
      <div id="pantalla">
         @yield('content') 
      </div>
  </div>
</div>
  
  
</body>
</html>

     