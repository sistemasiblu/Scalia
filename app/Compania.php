<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Compania extends Model
{
    protected $table = 'compania';
    protected $primaryKey = 'idCompania';

    protected $fillable = ['codigoCompania', 'nombreCompania','directorioCompania'];

    public $timestamps = false;

    public function users() 
	{
		return $this->hasMany('App\User','Compania_idCompania');
	}
}
