<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ControlIngreso extends Model
{
    protected $table ='controlingreso';
	protected $primaryKey = 'idControlIngreso';
	
	protected $fillable = ['TipoIdentificacion_idTipoIdentificacion', 'numeroDocumentoVisitanteControlIngreso', 'nombreVisitanteControlIngreso', 'apellidoVisitanteControlIngreso', 'Tercero_idResponsable', 'dependenciaControlIngreso', 'fechaIngresoControlIngreso', 'fechaSalidaControlIngreso', 'observacionControlIngreso'];

	public $timestamps = false;	

	public function controlingresodetalle() 
	{
		return $this->hasMany('App\ControlIngresoDetalle','ControlIngreso_idControlIngreso');
	}

	public function dispositivo()
    {
    	return $this->hasOne('App\Dispositivo','idDispositivo');
    }

    public function marca()
    {
    	return $this->hasOne('App\Marca','idMarca');
    }

}
