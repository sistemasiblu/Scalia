<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EncuestaPregunta extends Model
{
    protected $table = 'encuestapregunta';
    protected $primaryKey = 'idEncuestaPregunta';

    protected $fillable = ['preguntaEncuestaPregunta', 
						    'detalleEncuestaPregunta', 
						    'tipoRespuestaEncuestaPregunta', 
						    'Encuesta_idEncuesta'
						  ];

    public $timestamps = false;

    public function EncuestaOpcion()
	{
		return $this->hasMany('App\EncuestaOpcion','EncuestaPregunta_idEncuestaPregunta');
	}
}
