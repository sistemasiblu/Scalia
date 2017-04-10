<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EncuestaRol extends Model
{
    protected $table = 'encuestarol';
    protected $primaryKey = 'idEncuestaRol';

    protected $fillable = ['Encuesta_idEncuesta', 
						    'adicionarEncuestaRol', 
						    'modificarEncuestaRol', 
						    'eliminarEncuestaRol', 
						    'consultarEncuestaRol', 
						    'publicarEncuestaRol'
						  ];

    public $timestamps = false;

}
