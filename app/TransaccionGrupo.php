<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransaccionGrupo extends Model
{   
	protected $table='transacciongrupo';
	protected $primaryKey='idTransaccionGrupo';
    protected $fillable=['nombreTransaccionGrupo'];
    public $timestamps=false;

    public function transaccionactivo()
    {
    	return $this->hasMany('App\TransaccionActivo','idTransaccionActivo');
    }

}
