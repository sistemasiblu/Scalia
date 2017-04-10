<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CargoCompetencia extends Model
{
     protected $table ='cargocompetencia'; //la tabla siempre es en miniscula 
	protected $primaryKey = 'idCargoCompetencia'; //camello
	
	protected $fillable = ['Competencia_idCompetencia','Cargo_idCargo'];

	public $timestamps = false;

	public function Cargo()
	{
		return $this->hasOne('App\Cargo','idCargo');
    }
}
