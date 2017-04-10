<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'rol';
    protected $primaryKey = 'idRol';

    protected $fillable = ['codigoRol', 'nombreRol'];

    public $timestamps = false;

    public function rolOpcion()
    {
    	return $this->hasMany('App\RolOpcion','Rol_idRol');
    }

    public function users()
    {
        return $this->hasMany('App\User','Rol_idRol');
    }

    public function documentoPermiso()
    {
    	return $this->hasMany('App\DocumentoPermiso','Rol_idRol');
    }

}
