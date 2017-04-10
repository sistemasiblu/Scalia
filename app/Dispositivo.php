<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dispositivo extends Model
{
    protected $table = 'dispositivo';
    protected $primaryKey = 'idDispositivo';

    protected $fillable = ['codigoDispositivo', 'nombreDispositivo'];

    public $timestamps = false;

    public function controlingreso() 
	{
		return $this->hasMany('App\ControlIngreso','Dispositivo_idDispositivo');
	}
}

	
