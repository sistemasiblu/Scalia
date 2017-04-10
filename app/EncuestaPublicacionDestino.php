<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EncuestaPublicacionDestino extends Model
{
    protected $table = 'encuestapublicaciondestino';
    protected $primaryKey = 'idEncuestaPublicacionDestino';

    protected $fillable = ['EncuestaPublicacion_idEncuestaPublicacion', 
						    'nombreEncuestaPublicacionDestino', 
						    'correoEncuestaPublicacionDestino', 
						    'telefonoEncuestaPublicacionDestino'
						  ];

    public $timestamps = false;

}
