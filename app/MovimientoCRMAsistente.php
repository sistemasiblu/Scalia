<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovimientoCRMAsistente extends Model
{
    protected $table = 'movimientocrmasistente';
    protected $primaryKey = 'idMovimientoCRMAsistente';

    protected $fillable = ['nombreMovimientoCRMAsistente','cargoMovimientoCRMAsistente','telefonoMovimientoCRMAsistente','correoElectronicoMovimientoCRMAsistente','MovimientoCRM_idMovimientoCRM'];

    public $timestamps = false;

    public function movimientocrm()
    {
		return $this->hasOne('App\MovimientoCRM','idMovimientoCRM');
    }
}
