<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CampoTransaccion extends Model
{
    protected $table='campotransaccion';
    protected $primaryKey='idCampoTransaccion';
    protected $fillable=['nombreCampoTransaccion', 'descripcionCampoTransaccion', 'relacionTablaCampoTransaccion', 'relacionIdCampoTransaccion', 'relacionNombreCampoTransaccion', 'relacionAliasCampoTransaccion'];
    public $timestamps=false;

    public function transaccionactivocampo()
    {
    	return $this->hasOne('App\TransaccionActivoCampo','idTransaccionActivoCampo');
    }
}
