<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    protected $table = 'cargo';
    protected $primaryKey = 'idCargo';
    protected $fillable = ['codigoCargo', 'nombreCargo', 'salarioBaseCargo', 'nivelRiesgoCargo','Cargo_IdDepende','aniosExperienciaCargo', 'objetivoCargo','porcentajeEducacionCargo','porcentajeExperienciaCargo','porcentajeFormacionCargo','porcentajeHabilidadCargo','porcentajeResponsabilidadCargo', 'posicionPredominanteCargo', 'restriccionesCargo', 'responsabilidadesCargo', 'autoridadesCargo', 'Compania_idCompania'];
    public $timestamps = false;

    public function cargoElementoProtecciones()
    {
    	return $this->hasMany('App\CargoElementoProteccion','Cargo_idCargo');
    }

    public function cargoExamenMedicos()
    {
    	return $this->hasMany('App\CargoExamenMedico','Cargo_idCargo');
    }

    public function cargoTareaRiesgos()
    {
    	return $this->hasMany('App\CargoTareaRiesgo','Cargo_idCargo');
    }

    public function cargoVacunas()
    {
    	return $this->hasMany('App\CargoVacuna','Cargo_idCargo');
    }

    public function CargoResponsabilidad()
    {
        return $this->hasMany('App\CargoResponsabilidad','Cargo_idCargo');
    }


        public function CargoEducacion()
    {
        return $this->hasMany('App\CargoEducacion','Cargo_idCargo');
    }


       public function CargoFormacion()
    {
        return $this->hasMany('App\CargoFormacion','Cargo_idCargo');
    }

       public function CargoHabilidad()
    {
        return $this->hasMany('App\CargoHabilidad','Cargo_idCargo');
    }


       public function CargoCompetencia()
    {
        return $this->hasMany('App\CargoCompetencia','Cargo_idCargo');
    }
}
