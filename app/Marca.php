<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    protected $table = 'marca';
    protected $primaryKey = 'idMarca';

    protected $fillable = ['codigoMarca', 'nombreMarca'];

    public $timestamps = false;

    public function controlingreso() 
	{
		return $this->hasMany('App\ControlIngreso','Marca_idMarca');
	}
}
