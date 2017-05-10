<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivoParte extends Model
{
    protected $table='activoparte';
    protected $primaryKey='idActivoParte';
    protected $fillable=['Activo_idActivo', 'Activo_idParte'];
    public $timestamps=false;

	public function activo()
	{
	    return $this->hasOne('App\Activo','idActivo');
	}  
}
