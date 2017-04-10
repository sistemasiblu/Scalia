<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ControlIngresoDetalle extends Model
{
	protected $table ='controlingresodetalle';
	protected $primaryKey = 'idControlIngresoDetalle';
	
	protected $fillable = ['ControlIngreso_idControlIngreso', 'Dispositivo_idDispositivo', 'Marca_idMarca','referenciaDispositivoControlIngresoDetalle',
	'observacionControlIngresoDetalle','retiraDispositivoControlIngresoDetalle'];

	public $timestamps = false;	

    public function documento()
    {
    	return $this->hasOne('App\ControlIngreso','idControlIngreso');
    }
}