<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Encuesta extends Model
{
    protected $table = 'encuesta';
    protected $primaryKey = 'idEncuesta';

    protected $fillable = ['tituloEncuesta', 
						    'descripcionEncuesta', 
						    'Compania_idCompania',
						    'Users_idCrea', 
						    'created_at', 
						    'Users_idModifica', 
						    'updated_at'

						  ];

    public $timestamps = false;

    public function EncuestaPregunta()
	{
		return $this->hasMany('App\EncuestaPregunta','Encuesta_idEncuesta');
	}

	public function EncuestaRol()
	{
		return $this->hasMany('App\EncuestaRol','Encuesta_idEncuesta');
	}
}
