<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EncuestaPublicacion extends Model
{
    protected $table = 'encuestapublicacion';
    protected $primaryKey = 'idEncuestaPublicacion';

    protected $fillable = ['Encuesta_idEncuesta', 
						    'nombreEncuestaPublicacion', 
						    'fechaEncuestaPublicacion', 
						    'Users_idCrea', 
						    'created_at', 
						    'Users_idModifica', 
						    'updated_at'
						  ];

    public $timestamps = false;

    public function EncuestaPublicacionDestino()
	{
		return $this->hasMany('App\EncuestaPublicacionDestino','EncuestaPublicacion_idEncuestaPublicacion');
	}

}
