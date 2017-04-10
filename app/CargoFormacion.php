<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CargoFormacion extends Model
{
     protected $table ='cargoformacion'; //la tabla siempre es en miniscula 
	protected $primaryKey = 'idCargoFormacion'; //camello
	
	protected $fillable = ['PerfilCargo_idPerfilCargo','porcentajeCargoFormacion','Cargo_idCargo'];


	public $timestamps = false;

	public function Cargo()
	{
		return $this->hasOne('App\Cargo','idCargo');
    }
}
