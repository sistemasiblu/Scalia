<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CargoTareaRiesgo extends Model
{
    protected $table = 'cargotareariesgo';
    protected $primaryKey = 'idCargoTareaRiesgo';

    protected $fillable = ['Cargo_idCargo','ListaGeneral_idTareaAltoRiesgo'];

    public $timestamps = false;

    public function cargo()
    {
		return $this->hasOne('App\Cargo','idCargo');
    }
}
