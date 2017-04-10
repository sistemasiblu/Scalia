<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EncuestaOpcion extends Model
{
    protected $table = 'encuestaopcion';
    protected $primaryKey = 'idEncuestaOpcion';

    protected $fillable = ['preguntaEncuestaOpcion', 
						    'valorEncuestaOpcion', 
						    'nombreEncuestaOpcion', 
						    'EncuestaPregunta_idEncuestaPregunta'
						  ];

    public $timestamps = false;
}