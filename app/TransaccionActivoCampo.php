<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransaccionActivoCampo extends Model
{
   protected $table='transaccionactivocampo';
   protected $primaryKey='idTransaccionActivoCampo';
   protected $fillable=['TransaccionActivo_idTransaccionActivo', 'CampoTransaccion_idCampoTransaccion','gridTransaccionActivoCampo', 'vistaTransaccionActivoCampo','obligatorioTransaccionActivoCampo'];
   public $timestamps=false;

   public function transaccionactivo()
    {
    	return $this->hasOne('App\TransaccionActivo','idTransaccionActivo');
    }

     public function campotransaccion()
    {
    	return $this->hasOne('App\CampoTransaccion','idCampoTransaccion');
    }

}
