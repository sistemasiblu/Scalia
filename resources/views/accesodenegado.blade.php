@extends('layouts.modal')
@section('titulo')<h1 id="titulo" style="color:#EB564F; font-family: Arial, Helvetica, sans-serif;"><center>ACCESO DENEGADO</center></h1>@stop
{!!Html::style('css/BootSideMenu.css'); !!}
{!!Html::script('js/BootSideMenu.js'); !!}
@section('content')
@include('alerts.request')

<center><img style="width:50%; height:50%;" src="imagenes/accesodenegado.png"></center>
@stop