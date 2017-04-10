<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CargoVacuna extends Model
{
    protected $table = 'cargovacuna';
    protected $primaryKey = 'idCargoVacuna';

    protected $fillable = ['Cargo_idCargo', 'ListaGeneral_idVacuna'];

    public $timestamps = false;

    public function cargo()
    {
		return $this->hasOne('App\Cargo','idCargo');
    }
}
