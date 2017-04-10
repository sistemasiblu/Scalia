@extends('layouts.modal') 
@section('content')

{!!Html::script('js/visorinforme.js');!!}

<style>
    tfoot input {
                width: 100%;
                padding: 3px;
                background-color: #fff;
                background-image: none;
                border: 1px solid #ccc;
                border-radius: 4px;
            }
</style> 

        <div class="container">
            <div class="row">
                <div class="container">
                    <div class="btn-group" style="margin-left: 94%;margin-bottom:4px" title="Columns">
                        <button  type="button" class="btn btn-default dropdown-toggle"data-toggle="dropdown">
                            <i class="glyphicon glyphicon-th icon-th"></i> 
                            <span class="caret"></span>
                        </button>
                       <ul class="dropdown-menu dropdown-menu-right" role="menu">
                            <li><a class="toggle-vis" data-column="0"><label> ID</label></a></li>
                            <li><a class="toggle-vis" data-column="0"><label> Concepto</label></a></li>
                        </ul>
                    </div>
                    
                    <table id="tinformeconceptoSelect" name="tinformeconceptoSelect" class="display table-bordered" width="100%">
                        <thead>
                            <tr class="btn-default active">

                                <th><b>ID</b></th>
                                <th><b>Concepto</b></th>
                              
                            </tr>
                        </thead>
                        <tfoot>
                            <tr class="btn-default active">

                                <th>ID</th>
                                <th>Concepto</th>
                               
                            </tr>
                        </tfoot>
                    </table>

                    <div class="modal-footer">
                        <button id="botonCompania" name="botonCompania" type="button" class="btn btn-primary" >Seleccionar</button>
                    </div>

                

                </div>
            </div>
        </div>

@stop