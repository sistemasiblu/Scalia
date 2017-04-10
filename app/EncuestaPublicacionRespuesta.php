<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EncuestaPublicacionRespuesta extends Model
{
    protected $table = 'encuestapublicacionrespuesta';
    protected $primaryKey = 'idEncuestaPublicacionRespuesta';

    protected $fillable = ['EncuestaPublicacionDestino_idEncuestaPublicacionDestino', 
						    'EncuestaPregunta_idEncuestaPregunta', 
						    'valorEncuestaPublicacionDestino'
						  ];

    public $timestamps = false;

}
