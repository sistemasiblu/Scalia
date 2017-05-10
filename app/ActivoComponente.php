<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivoComponente extends Model
{
    protected $table='activocomponente';
    protected $primaryKey='idActivoComponente';
    protected $fillable=['Activo_idActivo', 'Activo_idComponente', 'cantidadActivoComponente'];
    public $timestamps=false;


 

public function activo()
    {
    	return $this->hasOne('App\Activo','idActivo');
    }  

}
