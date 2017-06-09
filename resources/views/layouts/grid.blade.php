@extends('layouts.menudinamico_nuevo')

    @section('clases')

      {!!Html::style('assets/bootstrap-v3.3.5/css/bootstrap-theme.min.css'); !!}
      {!!Html::style('assets/font-awesome-v4.3.0/css/font-awesome.min.css'); !!}
    
      {!!Html::style('choosen/docsupport/style.css'); !!}
      {!!Html::style('choosen/docsupport/prism.css'); !!}
      {!!Html::style('choosen/chosen.css'); !!}
      {!!Html::script('sb-admin/bower_components/ckeditor/ckeditor.js'); !!}

      {!!Html::style('sb-admin/bower_components/metisMenu/dist/metisMenu.min.css'); !!}
      {!!Html::style('sb-admin/dist/css/sb-admin-2.css'); !!}
      {!!Html::style('sb-admin/bower_components/font-awesome/css/font-awesome.min.css'); !!}
      {!!Html::style('sb-admin/bower_components/datetimepicker/css/bootstrap-datetimepicker.min.css'); !!}
      {!!Html::style('sb-admin/bower_components/fileinput/css/fileinput.css'); !!}

        <style type="text/css" media="all">
          /* fix rtl for demo */
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
      {!!Html::script('sb-admin/bower_components/ckeditor/ckeditor.js'); !!}
      {!!Html::script('js/general.js'); !!} 

        <!-- DataTables -->
        {!!Html::script('DataTables/media/js/jquery.dataTables.js'); !!}
        {!!Html::style('DataTables/media/css/jquery.dataTables.min.css'); !!}
        <!-- <script src="https://code.jquery.com/jquery-1.12.4.js"></script> -->
        <script src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.2.4/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.flash.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
        <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.24/build/pdfmake.min.js"></script>
        <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.24/build/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.print.min.js"></script>
        <!-- <link href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css"> -->
        <link href="https://cdn.datatables.net/buttons/1.2.4/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css">

<link href="https://cdn.datatables.net/fixedcolumns/3.2.2/css/fixedColumns.dataTables.min.css" rel="stylesheet" type="text/css">
        <script src="https://cdn.datatables.net/fixedcolumns/3.2.2/js/dataTables.fixedColumns.min.js"></script>
       

        <style>

               
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

     @stop