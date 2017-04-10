<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CargoEducacion extends Model
{
     protected $table ='cargoeducacion'; //la tabla siempre es en miniscula 
	protected $primaryKey = 'idCargoEducacion'; //camello
	
	protected $fillable = ['PerfilCargo_idPerfilCargo','porcentajeCargoEducacion','Cargo_idCargo'];


	public $timestamps = false;

	public function Cargo()
	{
		return $this->hasOne('App\Cargo','idCargo');
    }
}
