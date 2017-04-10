<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CargoHabilidad extends Model
{
     protected $table ='cargohabilidad'; //la tabla siempre es en miniscula 
	protected $primaryKey = 'idCargoHabilidad'; //camello
	
	protected $fillable = ['PerfilCargo_idPerfilCargo','porcentajeCargoHabilidad','Cargo_idCargo'];


	public $timestamps = false;

	public function Cargo()
	{
		return $this->hasOne('App\Cargo','idCargo');
    }
}
