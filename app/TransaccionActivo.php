<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransaccionActivo extends Model
{
    protected $table='transaccionactivo';
    protected $primaryKey='idTransaccionActivo';
    protected $fillable=['codigoTransaccionActivo', 'nombreTransaccionActivo', 'formatoTransaccionActivo', 'tipoNumeracionTransaccionActivo', 'longitudTransaccionActivo', 'desdeTransaccionActivo', 'hastaTransaccionActivo', 'TransaccionGrupo_idTransaccionGrupo', 'accionTransaccionActivo', 'estadoTransaccionActivo','Compania_idCompania'];
    public $timestamps=false;

    public function transacciongrupo()
    {
    	return $this->hasOne('App\TransaccionGrupo','idTransaccionGrupo');
    }

     public function transaccionactivocampo()
    {
    	return $this->hasMany('App\TransaccionActivoCampo','idTransaccionActivoCampo');
    }
}
